<?php

namespace DNADesign\Workflow\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Security;
use DNADesign\Workflow\DataObjects\WorkflowInstance;
use DNADesign\Workflow\DataObjects\WorkflowTransition;
use DNADesign\Workflow\Services\WorkflowService;

/**
 * Handles actions triggered from external sources, eg emails or web frontend
 */
class AdvancedWorkflowActionController extends Controller
{
    public function transition($request)
    {
        if (!Security::getCurrentUser()) {
            return Security::permissionFailure(
                $this,
                _t(
                    'AdvancedWorkflowActionController.ACTION_ERROR',
                    "You must be logged in"
                )
            );
        }

        $id = $this->request->requestVar('id');
        $transition = $this->request->requestVar('transition');

        $instance = DataObject::get_by_id(WorkflowInstance::class, (int) $id);
        if ($instance && $instance->canEdit()) {
            $transition = DataObject::get_by_id(WorkflowTransition::class, (int) $transition);
            if ($transition) {
                if ($this->request->requestVar('comments')) {
                    $action = $instance->CurrentAction();
                    $action->Comment = $this->request->requestVar('comments');
                    $action->write();
                }

                singleton(WorkflowService::class)->executeTransition($instance->getTarget(), $transition->ID);
                $result = array(
                    'success' => true,
                    'link'    => $instance->getTarget()->AbsoluteLink()
                );
                if (Director::is_ajax()) {
                    return json_encode($result);
                }
                return $this->redirect($instance->getTarget()->Link());
            }
        }

        if (Director::is_ajax()) {
            $result = array(
                'success' => false,
            );
            return json_encode($result);
        }

        return $this->redirect($instance->getTarget()->Link());
    }
}
