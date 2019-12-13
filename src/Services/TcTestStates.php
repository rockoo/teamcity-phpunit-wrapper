<?php namespace Fantasyrock\Tcw\Services;

class TcTestStates
{
    public static $STARTED  = 'testStarted';
    public static $IGNORED  = 'testIgnored';
    public static $FINISHED = 'testFinished';
    public static $FAILED   = 'testFailed';
    public static $SKIPPED  = 'testSkipped';
    public static $RISKY    = 'testRisky';
    public static $WARNING  = 'testWarning';

    public static $SUIT_STARTED  = 'testSuiteStarted';
    public static $SUIT_FINISHED = '';
}