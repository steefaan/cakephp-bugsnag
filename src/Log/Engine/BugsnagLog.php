<?php

namespace Steefaan\Bugsnag\Log\Engine;

use Bugsnag\Client;
use Bugsnag\Report;
use Cake\Log\Engine\BaseLog;
use Steefaan\Bugsnag\BugsnagFactory;

class BugsnagLog extends BaseLog
{
    /**
     * Default config.
     *
     * Please take a look to the bugsnag docs for a detailed
     * list of possible configuration values.
     *
     * - `levels` string or array, levels the engine is interested in.
     * - `scopes` string or array, scopes the engine is interested in.
     *
     * @link https://bugsnag.com/docs/notifiers/php
     * @var array
     */
    protected $_defaultConfig = [
        'notify' => true,
        'levels' => [
            'error',
            'warning',
            'info'
        ],
        'scopes' => []
    ];

    /**
     * Bugsnag doesn't support all levels.
     *
     * @var array
     */
    protected $_levels = [
        'emergency' => 'error',
        'alert' => 'error',
        'critical' => 'error',
        'error' => 'error',
        'warning' => 'warning',
        'notice' => 'warning',
        'info' => 'info',
        'debug' => 'info'
    ];

    /**
     * Client instance.
     *
     * @var \Bugsnag\Client
     */
    protected $_bugsnag = null;

    /**
     * BugsnagLog constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $bugsnagFactory = new BugsnagFactory($this->config('notify'), $config);
        $bugsnag = $bugsnagFactory->factory();

        $this->setBugsnag($bugsnag);
    }

    /**
     * Set client instance.
     *
     * @param \Bugsnag\Client $bugsnag
     *
     * @return void
     */
    public function setBugsnag(Client $bugsnag)
    {
        $this->_bugsnag = $bugsnag;
    }

    /**
     * Send log to Bugsnag.
     *
     * @param string $level The severity level of the message being written.
     *    See Cake\Log\Log::$_levels for list of possible levels.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message.
     *
     * @return bool success of write.
     */
    public function log($level, $message, array $context = [])
    {
        $level = isset($this->_levels[$level]) ? $this->_levels[$level] : 'info';

        $this->_bugsnag->notifyError(ucfirst($level), $message, function (Report $report) use ($context) {
            $report->setContext($context);
        });

        return true;
    }
}
