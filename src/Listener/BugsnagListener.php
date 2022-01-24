<?php

namespace Bugsnag\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

class BugsnagListener implements EventListenerInterface
{
    /**
     * List of implemented events.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Log.Bugsnag.beforeNotify' => 'beforeNotify'
        ];
    }

    /**
     * Format stacktrace.
     *
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function beforeNotify(Event $event)
    {
        /** @var \Bugsnag\Report $report */
        $report = $event->getData('report');
    }
}
