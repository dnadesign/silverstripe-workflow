<?php

namespace DNADesign\Workflow\Actions;

use DNADesign\Workflow\DataObjects\WorkflowAction;

class CancelWorkflowAction extends WorkflowAction
{
    private static $icon = 'dnadesign/silverstripe-workflow:images/cancel.png';

    private static $table_name = 'CancelWorkflowAction';
}
