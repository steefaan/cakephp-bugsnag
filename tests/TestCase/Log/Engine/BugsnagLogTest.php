<?php

namespace Steefaan\Bugsnag\Test\TestCase\Log\Engine;

use Bugsnag\Client;
use Cake\TestSuite\TestCase;
use Steefaan\Bugsnag\Log\Engine\BugsnagLog;

class BugsnagLogTest extends TestCase
{
    /**
     *
     */
    public function testLog()
    {
        $bugsnag = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bugsnag->expects(self::once())
            ->method('notifyError')
            ->willReturn(null);

        $engine = new BugsnagLog();
        $engine->setBugsnag($bugsnag);
        $engine->log('error', 'test_message');
    }
}
