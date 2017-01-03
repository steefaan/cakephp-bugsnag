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
     * @var bool
     */
    protected $_notify = true;

    /**
     * @var array
     */
    protected $_config = [];

    /**
     * BugsnagFactory constructor.
     * @param bool $notify
     * @param array $config
     */
    public function __construct($notify, array $config = [])
    {
        $this->_notify = $notify;
        $this->_config = $config;
    }

    /**
     * @return bool
     */
    public function shouldNotify()
    {
        return $this->_notify;
    }

    /**
     * @return \Bugsnag\Client
     */
    public function factory()
    {
        if (self::$_bugsnag instanceof Client) {
            return self::$_bugsnag;
        }

        $bugsnag = Client::make($this->_config['apiKey']);

        $bugsnagConfiguration = $bugsnag->getConfig();
        $bugsnagConfiguration->setReleaseStage($this->_config['releaseStage']);
        $bugsnagConfiguration->setFilters($this->_config['filters']);
        $bugsnagConfiguration->setNotifier($this->_config['notifier']);
        $bugsnagConfiguration->setProjectRoot(ROOT);

        $bugsnag->registerCallback(function (Report $report) use ($bugsnag) {
            $event = new Event('Log.Bugsnag.beforeNotify', $this, ['report' => $report]);

            EventManager::instance()->dispatch($event);
        });

        if ($this->shouldNotify()) {
            Handler::register($bugsnag);
        }

        return self::$_bugsnag = $bugsnag;
    }
}
