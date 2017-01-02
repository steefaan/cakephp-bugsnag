<?php

namespace Steefaan\Bugsnag\Error;

use Cake\Core\Configure;
use Cake\Error\BaseErrorHandler;
use Steefaan\Bugsnag\BugsnagFactory;

class BugsnagErrorHandler extends BaseErrorHandler
{
    /**
     * @var \Bugsnag_Client|null
     */
    protected $_notifier;

    /**
     * @var \Cake\Error\BaseErrorHandler
     */
    protected $_handler;

    /**
     * BugsnagErrorHandler constructor.
     * @param BugsnagFactory $bugsnagFactory
     * @param BaseErrorHandler $handler
     */
    public function __construct(BugsnagFactory $bugsnagFactory, BaseErrorHandler $handler)
    {
        if ($bugsnagFactory->shouldNotify()) {
            $this->_notifier = $bugsnagFactory->factory();
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
        if ($this->_notifier !== null) {
            $this->_notifier->notifyException(new \ErrorException($description, $code, 1, $file, $line));
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
