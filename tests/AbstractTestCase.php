<?php

namespace Psr\Log\Util\Tests;

if (class_exists('PHPUnit\Framework\TestCase')) {
    class AbstractTestCase extends \PHPUnit\Framework\TestCase
    {
        public function setExpectedException($exceptionName, $exceptionMessage = '', $exceptionCode = null)
        {
            if (method_exists($this, 'expectException')) {
                $this->expectException($exceptionName, $exceptionMessage, $exceptionCode);
                return;
            }

            parent::setExpectedException($exceptionName, $exceptionMessage, $exceptionCode);
        }
    }
} elseif (class_exists('PHPUnit_Framework_TestCase')) {
    // phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
    class AbstractTestCase extends \PHPUnit_Framework_TestCase
    {
    }
}
