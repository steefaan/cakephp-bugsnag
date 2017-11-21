<?php

namespace Steefaan\Bugsnag;

use Bugsnag\Client;
use Bugsnag\Report;
use Bugsnag\Handler;
use Cake\Event\Event;
use Cake\Event\EventManager;

class BugsnagFactory
{
    /**
     * @var \Bugsnag\Client
     */
    protected static $_bugsnag;

    /**
     * @param bool $notify
     * @param array $config
     *
     * @return \Bugsnag\Client|null
     */
    public static function factory($notify, array $config)
    {
        if (self::$_bugsnag === false) {
            return null;
        }

        if (self::$_bugsnag === null && $notify === false) {
            self::$_bugsnag = false;

            return null;
        }

        if (self::$_bugsnag instanceof Client) {
            return self::$_bugsnag;
        }

        $bugsnag = Client::make($config['apiKey']);

        $bugsnagConfiguration = $bugsnag->getConfig();
        $bugsnagConfiguration->setReleaseStage($config['releaseStage']);
        $bugsnagConfiguration->setFilters($config['filters']);
        $bugsnagConfiguration->setNotifier($config['notifier']);
        $bugsnagConfiguration->setProjectRoot(ROOT);

        $bugsnag->registerCallback(function (Report $report) use ($bugsnag) {
            $event = new Event('Log.Bugsnag.beforeNotify', $bugsnag, ['report' => $report]);

            EventManager::instance()->dispatch($event);
        });

        if ($notify === true) {
            Handler::register($bugsnag);
        }

        return self::$_bugsnag = $bugsnag;
    }
}
