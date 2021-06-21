<?php

namespace Psr\Log\Util\Tests;

use Psr\Log\LoggerInterface;
use Psr\Log\Util\Tests\Stub\TestLogger;

class TestLoggerTest extends LoggerInterfaceTest
{
    protected $logger;

    /**
     * @return TestLogger A new mock of the test subject.
     */
    protected function createSubject()
    {
        $mock = $this->getMockBuilder('Psr\\Log\\Util\\Tests\\Stub\\TestLogger')
            ->enableProxyingToOriginalMethods()
            ->getMock();

        return $mock;
    }

    public function getLogger()
    {
        if (! $this->logger instanceof LoggerInterface) {
            $this->logger = $this->createSubject();
        }

        return $this->logger;
    }

    public function getLogs()
    {
        $records = $this->logger->getRecords();
        $messages = array_map(function ($record) {
            return isset($record['message']) ? $record['message'] : null;
        }, $records);

        return $messages;
    }
}
