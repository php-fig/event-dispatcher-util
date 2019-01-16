<?php
declare(strict_types=1);

namespace Fig\EventDispatcher\Tagged;

use Fig\EventDispatcher\TaggedProvider;
use Fig\EventDispatcher\CollectingEvent;
use PHPUnit\Framework\TestCase;

class TaggedProviderTest extends TestCase
{

    public function test_non_workflow_events_ignored() : void
    {
        $p = new TaggedProvider(WorkflowEventInterface::class, 'workflowName');

        $p->addListener(function (WorkflowStart $event) {
            $event->add('A');
        }, 'bob');

        $event = new CollectingEvent();

        foreach ($p->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        $this->assertEmpty($event->result());
    }

    public function test_workflow_event_called_for_own_name_only() : void
    {
        $p = new TaggedProvider(WorkflowEventInterface::class, 'workflowName');

        // This has the right workflow name and event type.
        $p->addListener(function (WorkflowStart $event) {
            $event->add('A');
        }, 'bob');

        // This has the wrong workflow type.
        $p->addListener(function (WorkflowEnd $event) {
            $event->add('B');
        }, 'bob');

        // This matches the parent, so would be called for both WorkflowStart and WorkflowEnd.
        $p->addListener(function (WorkflowEvent $event) {
            $event->add('C');
        }, 'bob');

        // This is the right type but the wrong workflow name, so it won't be called.
        $p->addListener(function (WorkflowStart $event) {
            $event->add('D');
        }, 'anita');

        // This is the right type and applies to any workflow name.
        $p->addListener(function (WorkflowStart $event) {
            $event->add('E');
        });

        $event = new WorkflowStart('bob');

        foreach ($p->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        $this->assertEquals('ACE', implode($event->result()));
    }
}
