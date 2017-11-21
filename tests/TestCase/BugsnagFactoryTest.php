<?php

namespace Steefaan\Bugsnag\Test\TestCase;

use Bugsnag\Client;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Steefaan\Bugsnag\BugsnagFactory;

class BugsnagFactoryTest extends TestCase
{
    /**
     * BugsnagLogTest::setUp()
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Configure::write('Bugsnag.apiKey', 'test');
    }

    /**
     * BugsnagLogTest::tearDown()
     *
     * @return void
     */
    public function tearDown()
    {
        Configure::delete('Bugsnag.apiKey');

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testLog()
    {
        $bugsnag = BugsnagFactory::factory(true, [
            'apiKey' => 'foobar',
            'releaseStage' => 'prod',
            'filters' => [],
            'notifier' => [],
        ]);

        $this->assertInstanceOf(Client::class, $bugsnag);
    }
}
