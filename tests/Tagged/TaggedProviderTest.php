<?php
declare(strict_types=1);

namespace Fig\EventDispatcher\Tagged;

use Fig\EventDispatcher\ParameterDeriverTrait;
use Fig\EventDispatcher\TaggedProviderTrait;
use Fig\EventDispatcher\CollectingEvent;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

class TaggedProviderTest extends TestCase
{

    /** @var ListenerProviderInterface */
    protected $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new class implements ListenerProviderInterface {
            use TaggedProviderTrait;
            use ParameterDeriverTrait;

            /** @var array */
            protected $listeners = [];

            /** @var array */
            protected $all = [];


            protected function eventType(): string
            {
                return WorkflowEventInterface::class;
            }

            protected function tagMethod(): string
            {
                return 'workflowName';
            }

            public function addListener(callable $listener, string $tagName = '', string $type = null) : void
            {
                $type = $type ?? $this->getParameterType($listener);

                if ($tagName) {
                    $this->listeners[$tagName][$type][] = $listener;
                    return;
                }
                $this->all[$type][] = $listener;
            }

            protected function getListenersForTag(string $tag): iterable
            {
                return $this->listeners[$tag];
            }

            protected function getListenersForAllTags(): iterable
            {
                return $this->all;
            }
        };
    }

    public function test_non_workflow_events_ignored() : void
    {
        $p = $this->provider;

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
        $p = $this->provider;

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
