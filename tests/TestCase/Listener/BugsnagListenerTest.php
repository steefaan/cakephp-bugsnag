<?php

namespace Bugsnag\Test\TestCase\Listener;

use Bugsnag\Listener\BugsnagListener;
use Bugsnag_Configuration;
use Bugsnag_Diagnostics;
use Bugsnag_Error;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;

class BugsnagListenerTest extends TestCase
{
    /**
     * BugsnagListenerTest::setUp()
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->listener = new BugsnagListener();
    }

    /**
     * BugsnagListenerTest::tearDown()
     *
     * @return void
     */
    public function tearDown()
    {

        unset($this->listener);

        parent::tearDown();
    }

    /**
     * BugsnagListenerTest::testImplementedEvents()
     *
     * @return void
     */
    public function testImplementedEvents()
    {
        $expected = [
            'Log.Bugsnag.beforeNotify' => 'beforeNotify'
        ];

        $result = $this->listener->implementedEvents();
        $this->assertSame($expected, $result);
    }

    /**
     * BugsnagListenerTest::testBeforeNotify()
     *
     * @return void
     */
    public function testBeforeNotify()
    {
        $config = new Bugsnag_Configuration();
        $diagnostics = new Bugsnag_Diagnostics($config);

        $error = Bugsnag_Error::fromNamedError($config, $diagnostics, 'test_name', 'test_message');

        $count = count($error->stacktrace->frames);

        $event = new Event('Log.Bugsnag.beforeNotify', $this, ['error' => $error]);
        $this->listener->beforeNotify($event);

        $this->assertCount(($count - 3), $error->stacktrace->frames);
    }
}
