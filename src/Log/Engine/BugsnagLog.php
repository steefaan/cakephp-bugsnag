<?php

namespace Bugsnag\Log\Engine;

use Bugsnag_Client;
use Bugsnag_Error;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Log\Engine\BaseLog;

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
     * @var \Bugsnag_Client
     */
    protected $_client = null;

    /**
     * Constructor.
     *
     * @param array $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $client = new Bugsnag_Client(Configure::read('Bugsnag.apiKey'));

        foreach ($this->config() as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($client, $method)) {
                $client->{$method}($value);
            }
        }

        $client->setBeforeNotifyFunction(function(Bugsnag_Error $error) {
            $event = new Event('Log.Bugsnag.beforeNotify', $this, ['error' => $error]);
            EventManager::instance()->dispatch($event);
        });

        $this->setClient($client);
    }

    /**
     * Set client instance.
     *
     * @param Bugsnag_Client $client
     * @return void
     */
    public function setClient(Bugsnag_Client $client)
    {
        $this->_client = $client;
    }

    /**
     * Send log to Bugsnag.
     *
     * @param string $level The severity level of the message being written.
     *    See Cake\Log\Log::$_levels for list of possible levels.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message.
     * @return bool success of write.
     */
    public function log($level, $message, array $context = [])
    {
        $level = !isset($this->_levels[$level]) ? $this->_levels[$level] : 'info';
        $this->_client->notifyError(ucfirst($level), $message, $context, $level);
    }
}
