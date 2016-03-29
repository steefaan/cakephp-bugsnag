<?php

namespace Bugsnag\Test\TestCase\Log\Engine;

use Bugsnag\Log\Engine\BugsnagLog;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use FriendsOfCake\TestUtilities\AccessibilityHelperTrait;

class BugsnagLogTest extends TestCase
{
    use AccessibilityHelperTrait;

    /**
     * BugsnagLogTest::setUp()
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Configure::write('Bugsnag.apiKey', 'test');

        $this->client = $this->getMockBuilder('Bugsnag_Client')
            ->setConstructorArgs(['test'])
            ->setMethods(['notifyError'])
            ->getMock();
    }

    /**
     * BugsnagLogTest::tearDown()
     *
     * @return void
     */
    public function tearDown()
    {
        Configure::delete('Bugsnag.apiKey');

        unset($this->client);

        parent::tearDown();
    }

    /**
     * BugsnagLogTest::testValidConfig()
     *
     * @return void
     */
    public function testValidConfig()
    {
        $engine = new BugsnagLog([
            'releaseStage' => 'development',
            'batchSending' => false
        ]);

        $this->setReflectionClassInstance($engine);
        $client = $this->getProtectedProperty('_client', $engine);

        $this->setReflectionClassInstance($client);
        $config = $this->getProtectedProperty('config', $client);

        $this->assertSame('development', $config->releaseStage);
        $this->assertFalse($config->batchSending);
    }

    /**
     * BugsnagLogTest::testInvalidConfig()
     *
     * @return void
     */
    public function testInvalidConfig()
    {
        $engine = new BugsnagLog([
            'missingOption' => true
        ]);

        $this->setReflectionClassInstance($engine);
        $client = $this->getProtectedProperty('_client', $engine);

        $this->setReflectionClassInstance($client);
        $config = $this->getProtectedProperty('config', $client);

        $this->assertTrue(!isset($config->missingOption));
    }

    /**
     * BugsnagLogTest::testLog()
     *
     * @return void
     */
    public function testLog()
    {
        $engine = new BugsnagLog();
        $engine->setClient($this->client);

        $this->client
            ->expects($this->once())
            ->method('notifyError');

        $engine->log('error', 'test_message');
    }
}
