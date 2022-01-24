<?php

namespace Bugsnag;

use Bugsnag\Middleware\BugsnagErrorHandlerMiddleware;
use Cake\Core\BasePlugin;
use Cake\Http\MiddlewareQueue;

/**
 * Class Plugin
 *
 * @package Settings
 */
class Plugin extends BasePlugin
{
    protected $routesEnabled     = false;
    protected $bootstrapEnabled  = false;
    protected $middlewareEnabled = true;

    /**
     * Set up the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return parent::middleware($middlewareQueue)
                     ->add(BugsnagErrorHandlerMiddleware::class);
    }
}