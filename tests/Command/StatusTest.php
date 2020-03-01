<?php

namespace ZeitBuchung\Tests\Command;

use Prophecy\Prophet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use ZeitBuchung\Command\Status;
use PHPUnit\Framework\TestCase;
use ZeitBuchung\Service\DateTimeService;
use ZeitBuchung\Service\FileService;
use ZeitBuchung\Service\RecordService;

/**
 * Class StatusTest
 *
 * @package ZeitBuchung\Tests\Command
 */
class StatusTest extends TestCase
{
    /** @var Status */
    private $testClass;

    /** @var Prophet */
    private $prophet;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->prophet = new Prophet();
        $recordService = $this->prophet->prophesize(RecordService::class)->reveal();
        $fileService = $this->prophet->prophesize(FileService::class)->reveal();
        $dateTimeService = $this->prophet->prophesize(DateTimeService::class)->reveal();
        $this->testClass = new Status($recordService, $fileService, $dateTimeService);
    }

    /**
     * @return void
     */
    public function testNameAndDescription(): void
    {
        $this->assertEquals('status', $this->testClass->getName());
        $this->assertIsString($this->testClass->getDescription());
    }

    /**
     * @return void
     */
    public function testInputArguments(): void
    {
        $this->assertEquals(0, $this->testClass->getDefinition()->getArgumentCount());

        $expectedArguments = [
//            'argument' => [
//                'name' => 'argument',
//                'mode' => InputArgument::OPTIONAL,
//            ],
        ];

        if (!empty($this->testClass->getDefinition()->getArguments())) {
            foreach ($this->testClass->getDefinition()->getArguments() as $argument) {
                $this->assertArrayHasKey($argument->getName(), $expectedArguments);

                if (array_key_exists($argument->getName(), $expectedArguments)) {
                    $this->assertEquals($expectedArguments[$argument->getName()]['name'], $argument->getName());

                    if (InputArgument::REQUIRED === $expectedArguments[$argument->getName()]['mode']) {
                        $this->assertTrue($argument->isRequired());
                    } else {
                        $this->assertFalse($argument->isRequired());
                    }
                }

                $this->assertNotEmpty($argument->getDescription());
            }
        }
    }

    /**
     * @return void
     */
    public function testInputOptions(): void
    {
        $this->assertCount(0, $this->testClass->getDefinition()->getOptions());

        $expectedOptions = [
//            'option' => [
//                'name' => 'option',
//                'shortcut' => 'o',
//                'mode' => InputOption::VALUE_OPTIONAL,
//            ],
        ];

        if (!empty($this->testClass->getDefinition()->getOptions())) {
            foreach ($this->testClass->getDefinition()->getOptions() as $option) {
                $this->assertArrayHasKey($option->getName(), $expectedOptions);

                if (array_key_exists($option->getName(), $expectedOptions)) {
                    $this->assertEquals($expectedOptions[$option->getName()]['name'], $option->getName());
                    $this->assertEquals($expectedOptions[$option->getName()]['shortcut'], $option->getShortcut());

                    switch ($expectedOptions[$option->getName()]['mode']) {
                        case InputOption::VALUE_NONE:
                            $this->assertFalse(
                                $option->isValueRequired() && $option->isArray() && $option->isValueOptional()
                            );
                            break;
                        case InputOption::VALUE_REQUIRED:
                            $this->assertTrue($option->isValueRequired());
                            break;
                        case InputOption::VALUE_OPTIONAL:
                            $this->assertTrue($option->isValueOptional());
                            break;
                        case InputOption::VALUE_IS_ARRAY:
                            $this->assertTrue($option->isArray());
                            break;
                    }
                }

                $this->assertNotEmpty($option->getDescription());
            }
        }
    }
}
