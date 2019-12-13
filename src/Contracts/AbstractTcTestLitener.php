<?php namespace Fantasyrock\Tcw\Contracts;

use Fantasyrock\Tcw\Services\TcTestStates;
use PHPUnit\Framework\TestListener as Listener;
use ReflectionClass;

abstract class AbstractTcTestLitener implements Listener
{
    protected static function getMessage(\Throwable $t)
    {
        $message = '';
        if (get_class($t) !== '') {
            $message .= get_class($t);
        }
        if ($message !== '' && $t->getMessage() !== '') {
            $message .= ' : ';
        }
        $message .= $t->getMessage();
        return self::escapeValue($message);
    }

    protected static function getDetails(\Throwable $t)
    {
        return self::escapeValue($t->getTraceAsString());
    }

    protected static function getValueAsString($value)
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value === true ? 'true' : 'false';
        }

        if (is_array($value) || is_string($value)) {
            $valueAsString = print_r($value, true);
            if (strlen($valueAsString) > 10000) {
                return null;
            }
            return $valueAsString;
        }

        if (is_scalar($value)) {
            return print_r($value, true);
        }
        return null;
    }

    public static function getFileName($className)
    {
        try {
            $reflectionClass = new ReflectionClass($className);
            return $reflectionClass->getFileName();
        } catch (\ReflectionException $e) {
            $params = [
                'name'    => $e->getFile(),
                'message' => self::getMessage($e),
                'details' => self::getDetails($e)
            ];

            self::printEvent(TcTestStates::$FAILED, $params);
        }
    }

    protected static function escapeValue($text)
    {
        return str_replace(['|', "'", "\n", "\r", ']'], ['||', "|'", '|n', '|r', '|]'], $text);
    }

    public static function printEvent($eventName, $params = []): void
    {
        self::printText("\n##teamcity[$eventName");
        foreach ($params as $key => $value) {
            self::printText(" $key='$value'");
        }
        self::printText("]\n");
    }

    public static function printText($text): void
    {
        file_put_contents('php://stderr', $text);
    }
}