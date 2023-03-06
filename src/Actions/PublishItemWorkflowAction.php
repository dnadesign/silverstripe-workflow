<?php

namespace DNADesign\Workflow\Actions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataObject;
use DNADesign\Workflow\DataObjects\WorkflowAction;
use DNADesign\Workflow\DataObjects\WorkflowInstance;
use DNADesign\Workflow\Extensions\WorkflowEmbargoExpiryExtension;
use DNADesign\Workflow\Jobs\WorkflowPublishTargetJob;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;

/**
 * Publishes an item
 */
class PublishItemWorkflowAction extends WorkflowAction
{
    private static $db = array(
        'PublishDelay'          => 'Int',
        'AllowEmbargoedEditing' => 'Boolean',
    );

    private static $defaults = array(
        'AllowEmbargoedEditing' => true
    );

    private static $icon = 'dnadesign/silverstripe-workflow:images/publish.png';

    private static $table_name = 'PublishItemWorkflowAction';

    public function execute(WorkflowInstance $workflow)
    {
        if (!$target = $workflow->getTarget()) {
            return true;
        }

        if (class_exists(AbstractQueuedJob::class) && $this->PublishDelay) {
            $job   = new WorkflowPublishTargetJob($target);
            $days  = $this->PublishDelay;
            $after = date('Y-m-d H:i:s', strtotime("+$days days"));

            // disable editing, and embargo the delay if using WorkflowEmbargoExpiryExtension
            if ($target->hasExtension(WorkflowEmbargoExpiryExtension::class)) {
                $target->AllowEmbargoedEditing = $this->AllowEmbargoedEditing;
                $target->PublishOnDate = $after;
                $target->write();
            } else {
                singleton(QueuedJobService::class)->queueJob($job, $after);
            }
        } elseif ($target->hasExtension(WorkflowEmbargoExpiryExtension::class)) {
            $target->AllowEmbargoedEditing = $this->AllowEmbargoedEditing;
            // setting future date stuff if needbe

            // set this value regardless
            $target->UnPublishOnDate = $target->DesiredUnPublishDate;
            $target->DesiredUnPublishDate = '';

            // Publish dates
            if ($target->DesiredPublishDate) {
                // Hand-off desired publish date
                $target->PublishOnDate = $target->DesiredPublishDate;
                $target->DesiredPublishDate = '';
                $target->write();
            } else {
                // Ensure previously modified DesiredUnPublishDate values are written
                $target->write();
                if ($target->hasMethod('publishRecursive')) {
                    $target->publishRecursive();
                    $this->extend('onAfterWorkflowPublish', $target);
                }
            }
        } else {
            if ($target->hasMethod('publishRecursive')) {
                $target->publishRecursive();
                $this->extend('onAfterWorkflowPublish', $target);
            }
        }

        return true;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if (class_exists(AbstractQueuedJob::class)) {
            $fields->addFieldsToTab('Root.Main', [
                CheckboxField::create(
                    'AllowEmbargoedEditing',
                    _t(
                        __CLASS__ . '.ALLOWEMBARGOEDEDITING',
                        'Allow editing while item is embargoed? (does not apply without embargo)'
                    )
                ),
                NumericField::create(
                    'PublishDelay',
                    _t('PublishItemWorkflowAction.PUBLICATIONDELAY', 'Publication Delay')
                )->setDescription(_t(
                    __CLASS__ . '.PublicationDelayDescription',
                    'Delay publiation by the specified number of days'
                ))
            ]);
        }

        return $fields;
    }

    /**
     * Publish action allows a user who is currently assigned at this point of the workflow to
     *
     * @param  DataObject $target
     * @return bool
     */
    public function canPublishTarget(DataObject $target)
    {
        return true;
    }
}
