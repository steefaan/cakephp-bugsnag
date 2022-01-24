<?php

namespace Bugsnag\Middleware;

use Bugsnag\Client;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class BugsnagErrorHandlerMiddleware implements MiddlewareInterface
{
    /**
     * Client instance.
     *
     * @var \Bugsnag\Client|null
     */
    protected Client|null $client = null;

    /**
     * Constructor method
     */
    public function __construct()
    {
        $apiKey = Configure::read('Bugsnag.apiKey');
        if (!$apiKey && env('BUGSNAG_API_KEY')) {
            $apiKey = env('BUGSNAG_API_KEY');
        }

        if ($apiKey) {
            $this->setClient(Client::make($apiKey));
        }
    }

    /**
     * Set client instance.
     *
     * @param \Bugsnag\Client $client
     *
     * @return void
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!isset($this->client)) {
            return $handler->handle($request);
        }

        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            // Send event
            EventManager::instance()->dispatch(new Event('Log.Bugsnag.beforeNotify', $this, [
                'client'    => $this->client,
                'exception' => $exception
            ]));

            // Notify exception
            $this->client->notifyException($exception);

            throw $exception;
        }
    }
}