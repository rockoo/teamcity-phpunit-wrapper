<?php namespace Fantasyrock\Tcw\Services;

use PHPUnit\TextUI\Command;
use PHPUnit\TextUI\TestRunner;
use SebastianBergmann\CodeCoverage\Filter;

class TcTextUICommand extends Command
{
    public static function main($exit = true): int
    {
        $command = new self();
        $command->run($_SERVER['argv'], $exit);
    }

    protected function handleArguments(array $argv): void
    {
        parent::handleArguments($argv);

        // Add listener which reports to TeamCity using service messages
        $this->arguments['listeners'][] = new TcTestListener();
    }

    protected function createRunner(): TestRunner
    {
        // Disable coverage on the current file
        $coverage_Filter = new Filter();
        $coverage_Filter->addFileToWhitelist(__FILE__);
        return new TestRunner($this->arguments['loader'], $coverage_Filter);
    }
}