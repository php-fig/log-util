<?php


namespace Psr\Log\Util\Tests;


use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
{
    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|TestLogger
     */
    protected function createSubject()
    {
        $mock = $this->getMockBuilder('Psr\Log\Test\TestLogger')
            ->enableProxyingToOriginalMethods()
            ->getMock();

        return $mock;
    }

    public function testLog()
    {
        $message = uniqid('message');
        $level = LogLevel::INFO;
        $subject = $this->createSubject();

        $subject->log($level, $message, array());

        $this->assertTrue($subject->hasRecord($message, $level));
    }
}
