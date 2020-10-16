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
    public function implementedEvents()
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
        $error = $event->getData('error'); /* @var $error \Bugsnag_Error */

        $frames = array_slice($error->stacktrace->frames, 3);
        $error->stacktrace->frames = $frames;
    }
}
