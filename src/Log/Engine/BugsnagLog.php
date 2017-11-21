<?php

namespace Steefaan\Bugsnag\Log\Engine;

use Bugsnag\Client;
use Bugsnag\Report;
use Cake\Log\Engine\BaseLog;
use Steefaan\Bugsnag\BugsnagFactory;

class BugsnagLog extends BaseLog
{
    /**
     * Dispatched reports.
     *
     * @var array
     */
    protected static $_dispatched = [];

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

        $this->_bugsnag = BugsnagFactory::factory(null, $config);
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
        if ($this->_bugsnag === null) {
            return false;
        }

        $this->_bugsnag->notifyError('Error', $message, function (Report $report) use ($context) {
            for ($i = 0; $i < 5; $i++) {
                $report->getStacktrace()->removeFrame(0);
            }

            preg_match('/^[\w+\ \:]+? \(\d\):(.+?)\ in\ \[/', $report->getMessage(), $matches);

            $message = $report->getMessage();
            if (isset($matches[1])) {
                $message = trim($matches[1]);
            }

            $context = array_map($this->_getContextMapper(), $context);

            $report->setMetaData($context);
            $report->setMessage($message);
        });

        return true;
    }

    /**
     * @param Report $report
     * @return bool
     */
    public static function hasDispatchedReport(Report $report)
    {
        return isset(self::$_dispatched[spl_object_hash($report)]);
    }

    /**
     * Maps object instance as its instance name.
     *
     * @return \Closure
     */
    protected function _getArgMapper()
    {
        return function ($arg) {
            if (is_object($arg)) {
                $arg = get_class($arg);
            }
            return $arg;
        };
    }
    /**
     * Maps all object instances as their instance names.
     *
     * @return \Closure
     */
    protected function _getContextMapper()
    {
        $that = $this;

        return function ($entry) use ($that) {
            $args = array_map($that->_getArgMapper(), $entry);
            $entry = $args;

            return $entry;
        };
    }
}
