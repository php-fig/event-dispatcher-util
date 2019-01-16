<?php
declare(strict_types=1);

namespace Fig\EventDispatcher\Tagged;

use Fig\EventDispatcher\CollectingEvent;

class WorkflowEvent extends CollectingEvent implements WorkflowEventInterface
{
    /** @var string */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function workflowName(): string
    {
        return $this->name;
    }
}
