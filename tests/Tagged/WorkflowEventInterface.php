<?php
declare(strict_types=1);

namespace Fig\EventDispatcher\Tagged;

interface WorkflowEventInterface
{

    public function workflowName() : string;

}
