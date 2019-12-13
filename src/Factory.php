<?php namespace Fantasyrock\Tcw;

use Fantasyrock\Tcw\Services\TcTextUICommand;

class Factory
{
    public static function make(): void
    {
        TcTextUICommand::main();
    }
}