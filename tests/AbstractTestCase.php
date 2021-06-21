<?php

namespace Psr\Log\Util\Tests;

if (class_exists('PHPUnit\Framework\TestCase')) {
    class AbstractTestCase extends \PHPUnit\Framework\TestCase
    {
    }
} elseif (class_exists('PHPUnit_Framework_TestCase')) {
    class AbstractTestCase extends \PHPUnit_Framework_TestCase
    {
    }
}
