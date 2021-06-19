<?php

namespace Psr\Log\Util\Tests\Stub;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use ReflectionClass;

/**
 * Used for testing purposes.
 *
 * It records all records and gives you access to them for verification.
 *
 * @method bool hasEmergency($record)
 * @method bool hasAlert($record)
 * @method bool hasCritical($record)
 * @method bool hasError($record)
 * @method bool hasWarning($record)
 * @method bool hasNotice($record)
 * @method bool hasInfo($record)
 * @method bool hasDebug($record)
 *
 * @method bool hasEmergencyRecords()
 * @method bool hasAlertRecords()
 * @method bool hasCriticalRecords()
 * @method bool hasErrorRecords()
 * @method bool hasWarningRecords()
 * @method bool hasNoticeRecords()
 * @method bool hasInfoRecords()
 * @method bool hasDebugRecords()
 *
 * @method bool hasEmergencyThatContains($message)
 * @method bool hasAlertThatContains($message)
 * @method bool hasCriticalThatContains($message)
 * @method bool hasErrorThatContains($message)
 * @method bool hasWarningThatContains($message)
 * @method bool hasNoticeThatContains($message)
 * @method bool hasInfoThatContains($message)
 * @method bool hasDebugThatContains($message)
 *
 * @method bool hasEmergencyThatMatches($message)
 * @method bool hasAlertThatMatches($message)
 * @method bool hasCriticalThatMatches($message)
 * @method bool hasErrorThatMatches($message)
 * @method bool hasWarningThatMatches($message)
 * @method bool hasNoticeThatMatches($message)
 * @method bool hasInfoThatMatches($message)
 * @method bool hasDebugThatMatches($message)
 *
 * @method bool hasEmergencyThatPasses($message)
 * @method bool hasAlertThatPasses($message)
 * @method bool hasCriticalThatPasses($message)
 * @method bool hasErrorThatPasses($message)
 * @method bool hasWarningThatPasses($message)
 * @method bool hasNoticeThatPasses($message)
 * @method bool hasInfoThatPasses($message)
 * @method bool hasDebugThatPasses($message)
 */
class TestLogger extends AbstractLogger
{
    /**
     * @var array
     */
    public $records = array();

    public $recordsByLevel = array();

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = array())
    {
        if (!in_array($level, $this->getLogLevels(), true)) {
            throw new InvalidArgumentException(sprintf('Log level "%1$s" is not valid', $level));
        }

        $record = array(
            'level' => $level,
            'message' => $this->formatMessage($message, $level, $context),
            'context' => $context,
        );

        $this->recordsByLevel[$record['level']][] = $record;
        $this->records[] = $record;
    }

    public function interpolateContext($message, array $context)
    {
        return preg_replace_callback('!\{([^\}\s]*)\}!', function ($matches) use ($context) {
            $key = isset($matches[1]) ? $matches[1] : null;
            if (array_key_exists($key, $context)) {
                return $context[$key];
            }

            return $matches[0];
        }, $message);
    }

    public function formatMessage($message, $level, array $context)
    {
        $message = $this->interpolateContext($message, $context);
        $message = "$level $message";

        return $message;
    }

    public function getLogLevels()
    {
        $reflection = new ReflectionClass('Psr\Log\LogLevel');
        $constants = $reflection->getConstants();

        return $constants;
    }

    public function getRecords()
    {
        return $this->records;
    }

    public function hasRecords($level)
    {
        return isset($this->recordsByLevel[$level]);
    }

    public function hasRecord($record, $level)
    {
        if (is_string($record)) {
            $record = array('message' => $record);
        }
        return $this->hasRecordThatPasses(function ($rec) use ($record) {
            if ($rec['message'] !== $record['message']) {
                return false;
            }
            if (isset($record['context']) && $rec['context'] !== $record['context']) {
                return false;
            }
            return true;
        }, $level);
    }

    public function hasRecordThatContains($message, $level)
    {
        return $this->hasRecordThatPasses(function ($rec) use ($message) {
            return strpos($rec['message'], $message) !== false;
        }, $level);
    }

    public function hasRecordThatMatches($regex, $level)
    {
        return $this->hasRecordThatPasses(function ($rec) use ($regex) {
            return preg_match($regex, $rec['message']) > 0;
        }, $level);
    }

    public function hasRecordThatPasses(callable $predicate, $level)
    {
        if (!isset($this->recordsByLevel[$level])) {
            return false;
        }
        foreach ($this->recordsByLevel[$level] as $i => $rec) {
            if (call_user_func($predicate, $rec, $i)) {
                return true;
            }
        }
        return false;
    }

    public function __call($method, $args)
    {
        if (preg_match('/(.*)(Debug|Info|Notice|Warning|Error|Critical|Alert|Emergency)(.*)/', $method, $matches) > 0) {
            $genericMethod = $matches[1] . ('Records' !== $matches[3] ? 'Record' : '') . $matches[3];
            $level = strtolower($matches[2]);
            if (method_exists($this, $genericMethod)) {
                $args[] = $level;
                return call_user_func_array(array($this, $genericMethod), $args);
            }
        }
        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()');
    }

    public function reset()
    {
        $this->records = array();
        $this->recordsByLevel = array();
    }
}
