<?php

namespace Psr\Log\Util\Tests;

if (class_exists('PHPUnit\Framework\TestCase')) {
    class AbstractTestCase extends \PHPUnit\Framework\TestCase
    {
    }
} elseif (class_exists('PHPUnit_Framework_TestCase')) {
    // phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
    class AbstractTestCase extends \PHPUnit_Framework_TestCase
    {
    }
}
