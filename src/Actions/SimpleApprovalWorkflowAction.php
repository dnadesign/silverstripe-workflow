<?php

namespace DNADesign\Workflow\Actions;

use DNADesign\Workflow\DataObjects\WorkflowAction;
use DNADesign\Workflow\DataObjects\WorkflowInstance;

/**
 * A simple approval step that waits for any assigned user to trigger one of the relevant
 * transitions
 *
 * A more complicated workflow might use a majority, quorum or other type of
 * approval functionality
 */
class SimpleApprovalWorkflowAction extends WorkflowAction
{
    private static $icon = 'dnadesign/silverstripe-workflow:images/approval.png';

    private static $table_name = 'SimpleApprovalWorkflowAction';

    public function execute(WorkflowInstance $workflow)
    {
        // we don't need to do anything for this execution,
        // as we're relying on the fact that there's at least 2 outbound transitions
        // which will cause the workflow to block and wait.
        return true;
    }
}
