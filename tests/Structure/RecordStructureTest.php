<?php

namespace ZeitBuchung\Tests\Structure;

use DateTime;
use Exception;
use ZeitBuchung\Structure\RecordStructure;
use PHPUnit\Framework\TestCase;

/**
 * Class RecordStructureTest
 *
 * @package ZeitBuchung\Tests\Structure
 */
class RecordStructureTest extends TestCase
{
    /** @var RecordStructure */
    private $testClass;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->testClass = new RecordStructure(
            new DateTime('2020-01-04 12:00:00'),
            new DateTime('2020-01-04 13:00:00'),
            'This is a record message',
            60,
            '#12345'
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $recordStructure = new RecordStructure(
            new DateTime('2020-01-04 14:00:00'),
            new DateTime('2020-01-04 14:15:00'),
            'This is a test record message',
            15,
            '#54321'
        );

        $this->assertEquals(new DateTime('2020-01-04 14:00:00'), $recordStructure->getStart());
        $this->assertEquals(new DateTime('2020-01-04 14:15:00'), $recordStructure->getEnd());
        $this->assertEquals('This is a test record message', $recordStructure->getMessage());
        $this->assertEquals(15, $recordStructure->getTimeInMinutes());
        $this->assertEquals('#54321', $recordStructure->getTask());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testGetEnd(): void
    {
        $this->assertEquals(new DateTime('2020-01-04 13:00:00'), $this->testClass->getEnd());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetEnd(): void
    {
        $this->assertEquals(new DateTime('2020-01-04 13:00:00'), $this->testClass->getEnd());
        $newEnd = new DateTime('2020-01-31 23:24:25');
        $this->testClass->setEnd($newEnd);
        $this->assertEquals($newEnd, $this->testClass->getEnd());
    }

    /**
     * @return void
     */
    public function testGetMessage(): void
    {
        $this->assertEquals('This is a record message', $this->testClass->getMessage());
    }

    /**
     * @return void
     */
    public function testJsonSerialize(): void
    {
        $jsonDecodedData = [
            'start' => $this->testClass->getStart(),
            'end' => $this->testClass->getEnd(),
            'task' => $this->testClass->getTask(),
            'message' => $this->testClass->getMessage(),
            'timeInMinutes' => $this->testClass->getTimeInMinutes(),
        ];

        $this->assertEquals($jsonDecodedData, $this->testClass->jsonSerialize());
    }

    /**
     * @return void
     */
    public function testSetMessage(): void
    {
        $this->assertEquals('This is a record message', $this->testClass->getMessage());
        $newMessage = 'New message';
        $this->testClass->setMessage($newMessage);
        $this->assertEquals($newMessage, $this->testClass->getMessage());
    }

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $expectedArray = [
            'start' => '12:00:00',
            'end' => '13:00:00',
            'task' => '#12345',
            'message' => 'This is a record message',
            'time' => '1h',
        ];

        $this->assertEquals($expectedArray, $this->testClass->toArray());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testGetStart(): void
    {
        $this->assertEquals(new DateTime('2020-01-04 12:00:00'), $this->testClass->getStart());
    }

    /**
     * @return void
     */
    public function testSetTask(): void
    {
        $this->assertEquals('#12345', $this->testClass->getTask());
        $newTask = 'New task';
        $this->testClass->setTask($newTask);
        $this->assertEquals($newTask, $this->testClass->getTask());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetStart(): void
    {
        $this->assertEquals(new DateTime('2020-01-04 12:00:00'), $this->testClass->getStart());
        $newStart = new DateTime('2020-01-30 10:11:12');
        $this->testClass->setStart($newStart);
        $this->assertEquals($newStart, $this->testClass->getStart());
    }

    /**
     * @return void
     */
    public function testGetTask(): void
    {
        $this->assertEquals('#12345', $this->testClass->getTask());
    }

    /**
     * @return void
     */
    public function testGetHumanReadableStartTime(): void
    {
        $this->assertEquals('12:00:00', $this->testClass->getHumanReadableStartTime());
    }

    /**
     * @return void
     */
    public function testGetHumanReadableTimePeriod(): void
    {
        $this->assertEquals('1h', $this->testClass->getHumanReadableTimePeriod());
        $this->testClass->setTimeInMinutes(12);
        $this->assertEquals('12m', $this->testClass->getHumanReadableTimePeriod());
        $this->testClass->setTimeInMinutes(45);
        $this->assertEquals('0.75h', $this->testClass->getHumanReadableTimePeriod());
        $this->testClass->setTimeInMinutes(0);
        $this->assertEquals('0m', $this->testClass->getHumanReadableTimePeriod());
    }

    /**
     * @return void
     */
    public function testSetTimeInMinutes(): void
    {
        $this->assertEquals(60, $this->testClass->getTimeInMinutes());
        $newTimeInMinutes = 74;
        $this->testClass->setTimeInMinutes($newTimeInMinutes);
        $this->assertEquals($newTimeInMinutes, $this->testClass->getTimeInMinutes());
    }

    /**
     * @return void
     */
    public function testGetTimeInMinutes(): void
    {
        $this->assertEquals(60, $this->testClass->getTimeInMinutes());
    }

    /**
     * @return void
     */
    public function testGetHumanReadableEndTime(): void
    {
        $this->assertEquals('13:00:00', $this->testClass->getHumanReadableEndTime());
    }
}
