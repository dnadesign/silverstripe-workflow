<?php

namespace Symbiote\AdvancedWorkflow\Jobs;

use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use SilverStripe\Core\Extensible;

// Prevent failure if queuedjobs module isn't installed.
if (!class_exists(AbstractQueuedJob::class)) {
    return;
}

/**
 * A queued job that publishes a target after a delay.
 *
 * @package advancedworkflow
 */
class WorkflowPublishTargetJob extends AbstractQueuedJob
{
    use Extensible;

    public function __construct($obj = null, $type = null)
    {
        if ($obj) {
            $this->setObject($obj);
            $this->publishType = $type ? strtolower($type) : 'publish';
            $this->totalSteps = 1;
        }
    }

    public function getTitle()
    {
        return _t(
            'AdvancedWorkflowPublishJob.SCHEDULEJOBTITLE',
            "Scheduled {type} of {object}",
            "",
            array(
                'type' => $this->publishType,
                'object' => $this->getObject()->Title
            )
        );
    }

    public function process()
    {
        if ($target = $this->getObject()) {
            if ($this->publishType == 'publish') {
                $target->setIsPublishJobRunning(true);
                $target->PublishOnDate = '';
                $target->writeWithoutVersion();
                $target->publishRecursive();
                $this->extend('onAfterWorkflowPublish', $target);
            } elseif ($this->publishType == 'unpublish') {
                $target->setIsPublishJobRunning(true);
                $target->UnPublishOnDate = '';
                $target->writeWithoutVersion();
                $target->doUnpublish();
                $this->extend('onAfterWorkflowUnublish', $target);
            }
        }
        $this->currentStep = 1;
        $this->isComplete = true;
    }
}
