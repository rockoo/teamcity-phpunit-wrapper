<?php namespace Fantasyrock\Tcw\Services;

use Fantasyrock\Tcw\Contracts\AbstractTcTestListener;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;
use PHPUnit\Framework\AssertionFailedError;
use \Throwable;

class TcTestListener extends AbstractTcTestListener
{
    public function addWarning(Test $test, Warning $w, float $time): void
    {
        $params = [
            'name'    => $test->getName(),
            'message' => self::getMessage($w),
            'details' => self::getDetails($w)
        ];

        self::printEvent(TcTestStates::$WARNING, $params);
    }

    public function addError(Test $test, \Throwable $t, float $time): void
    {
        $params = [
            'name'    => $test->getName(),
            'message' => self::getMessage($t),
            'details' => self::getDetails($t)
        ];

        self::printEvent(TcTestStates::$FAILED, $params);
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        $params = [
            'name'    => $test->getName(),
            'message' => self::getMessage($e),
            'details' => self::getDetails($e)
        ];

        if ($e instanceof ExpectationFailedException) {
            $comparisonFailure = $e->getComparisonFailure();
            if ($comparisonFailure instanceof ComparisonFailure) {
                $actualResult = $comparisonFailure->getActual();
                $expectedResult = $comparisonFailure->getExpected();
                $actualString = self::getValueAsString($actualResult);
                $expectedString = self::getValueAsString($expectedResult);
                if ($actualString !== null && $expectedString !== null) {
                    $params['actual'] = self::escapeValue($actualString);
                    $params['expected'] = self::escapeValue($expectedString);
                }
            }
        }
        self::printEvent(TcTestStates::$FAILED, $params);
    }

    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
        $params = [
            'name'    => $test->getName(),
            'message' => self::getMessage($t),
            'details' => self::getDetails($t)
        ];

        self::printEvent(TcTestStates::$IGNORED, $params);
    }

    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
        $params = [
            'name'    => $test->getName(),
            'message' => self::getMessage($t),
            'details' => self::getDetails($t)
        ];

        self::printEvent(TcTestStates::$SKIPPED, $params);
    }

    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
        $params = [
            'name'    => $test->getName(),
            'message' => self::getMessage($t),
            'details' => self::getDetails($t)
        ];

        self::printEvent(TcTestStates::$RISKY, $params);
    }

    public function startTest(Test $test): void
    {
        $testName = $test->getName();
        $params = [
            'name'                  => $test->getName(),
            'captureStandardOutput' => 'true'
        ];

        if ($test instanceof TestCase) {

            $className = get_class($test);
            $fileName = self::getFileName($className);
            $params['locationHint'] = "file://$fileName::\\$className::$testName";

        }

        self::printEvent(TcTestStates::$STARTED, $params);
    }

    public function endTest(Test $test, float $time): void
    {
        $params = [
            'name'     => $test->getName(),
            'duration' => (int)(round($time, 2) * 1000)
        ];

        self::printEvent(TcTestStates::$FINISHED, $params);
    }

    public function startTestSuite(TestSuite $suite): void
    {
        $suiteName = $suite->getName();
        if (empty($suiteName)) {
            return;
        }
        $params = [
            'name' => $suiteName,
        ];
        if (class_exists($suiteName, false)) {
            $fileName = self::getFileName($suiteName);
            $params['locationHint'] = "file://$fileName::\\$suiteName";
        }
        self::printEvent(TcTestStates::$SUIT_STARTED, $params);
    }

    public function endTestSuite(TestSuite $suite): void
    {
        if (!$suite->getName()) {
            return;
        }

        self::printEvent(TcTestStates::$SUIT_FINISHED, ['name' => $suite->getName()]);
    }
}