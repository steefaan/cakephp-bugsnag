<?php

namespace Steefaan\Bugsnag\Error;

use Bugsnag\Client;
use Bugsnag\Report;
use Cake\Core\Configure;
use Cake\Error\BaseErrorHandler;
use Steefaan\Bugsnag\BugsnagFactory;

class BugsnagErrorHandler extends BaseErrorHandler
{
    /**
     * @var \Bugsnag\Client|null
     */
    protected $_notifier;

    /**
     * @var \Cake\Error\BaseErrorHandler
     */
    protected $_handler;

    /**
     * BugsnagErrorHandler constructor.
     * @param \Bugsnag\Client $bugsnagFactory
     * @param \Cake\Error\BaseErrorHandle $handler
     */
    public function __construct(Client $bugsnag = null, BaseErrorHandler $handler)
    {
        if ($bugsnag !== null) {
            $this->_notifier = $bugsnag;
        }

        $this->_handler = $handler;
    }

    /**
     * Set as the default error handler by CakePHP.
     *
     * Use config/error.php to customize or replace this error handler.
     * This function will use Debugger to display errors when debug > 0. And
     * will log errors to Log, when debug == 0.
     *
     * You can use the 'errorLevel' option to set what type of errors will be handled.
     * Stack traces for errors can be enabled with the 'trace' option.
     *
     * @param int $code Code of error
     * @param string $description Error description
     * @param string|null $file File on which error occurred
     * @param int|null $line Line that triggered the error
     * @param array|null $context Context
     *
     * @return bool True if error was handled
     */
    public function handleError($code, $description, $file = null, $line = null, $context = null)
    {
        // Level is the current error reporting level to manage silent error.
        // Strong errors are not authorized to be silenced.
        $level = error_reporting() | E_RECOVERABLE_ERROR | E_USER_ERROR | E_DEPRECATED | E_USER_DEPRECATED;
        $log = 0 & $code;

        // 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
        $throw = 0x1FFF & $code & $level;

        // 0x55; // E_ERROR + E_CORE_ERROR + E_COMPILE_ERROR + E_PARSE
        $code &= $level | 0x55;
        if (!$code || (!$log && !$throw)) {
            return $code && $log;
        }

        if ($this->_notifier !== null) {
            $this->_notifier->notifyError('Error', $description, function (Report $report) {
                $report->getStacktrace()->removeFrame(0);
            });
        }

        return $this->_handler->handleError($code, $description, $file, $line, $context);
    }

    /**
     * Handle uncaught exceptions.
     *
     * Uses a template method provided by subclasses to display errors in an
     * environment appropriate way.
     *
     * @see http://php.net/manual/en/function.set-exception-handler.php
     *
     * @param \Exception $exception Exception instance.
     *
     * @throws \Exception When renderer class not found
     *
     * @return void
     */
    public function handleException(\Exception $exception)
    {
        $class = get_class($exception);
        if ($this->_notifier !== null && !in_array($class, Configure::read('Error.skipLog'))) {
            $this->_notifier->notifyException($exception);
        }

        $this->_handler->handleException($exception);
    }

    /**
     * Display/Log a fatal error.
     *
     * @param int $code Code of error
     * @param string $description Error description
     * @param string $file File on which error occurred
     * @param int $line Line that triggered the error
     *
     * @return bool
     */
    public function handleFatalError($code, $description, $file, $line)
    {
        if ($this->_notifier !== null) {
            $this->_notifier->notifyException(new \ErrorException($description, $code, 1, $file, $line));
        }

        return $this->_handler->handleFatalError($code, $description, $file, $line);
    }

    /**
     * Display an error message in an environment specific way.
     *
     * Subclasses should implement this method to display the error as
     * desired for the runtime they operate in.
     *
     * @param array $error An array of error data.
     * @param bool $debug Whether or not the app is in debug mode.
     *
     * @return void
     */
    protected function _displayError($error, $debug)
    {
        // pass
    }

    /**
     * Display an exception in an environment specific way.
     *
     * Subclasses should implement this method to display an uncaught exception as
     * desired for the runtime they operate in.
     *
     * @param \Exception $exception The uncaught exception.
     *
     * @return void
     */
    protected function _displayException($exception)
    {
        // pass
    }
}
