<?php

namespace ZeitBuchung\Tests\Service;

use DateInterval;
use DateTime;
use Exception;
use Prophecy\Prophet;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Interfaces\SymfonyStyleInterface;
use ZeitBuchung\Service\DateTimeService;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeServiceTest
 *
 * @package ZeitBuchung\Tests\Service
 */
class DateTimeServiceTest extends TestCase
{
    /** @var DateTimeService */
    private $service;

    /** @var Prophet */
    private $prophet;

    /** @var SymfonyStyle */
    private $prophesiedSymfonyStyle;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = new DateTimeService();
        $this->prophet = new Prophet();
        $this->prophesiedSymfonyStyle = $this->prophet->prophesize(SymfonyStyle::class);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    /**
     * @return void
     */
    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(SymfonyStyleInterface::class, $this->service);
    }

    /**
     * @return void
     */
    public function testSetterAndGetterOfSymfonyStyle(): void
    {
        $this->assertEquals(null, $this->service->getSymfonyStyle());
        $this->service->setSymfonyStyle($this->prophesiedSymfonyStyle->reveal());
        $this->assertInstanceOf(SymfonyStyle::class, $this->service->getSymfonyStyle());
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function inputTimeDataProvider(): array
    {
        $return = [];
        $dateTime = new DateTime('00:00:00');
        $dateInterval = new DateInterval('PT59S');

        for ($i = 0; $i <= 1464; $i++) {
            $return[] = [$dateTime->format('G:i:s'), $dateTime->format('H:i:s')];
            $return[] = [$dateTime->format('H:i:s'), $dateTime->format('H:i:s')];

            if (0 === $i % 60) {
                $return[] = [$dateTime->format('G:i'), $dateTime->format('H:i:s')];
                $return[] = [$dateTime->format('H:i'), $dateTime->format('H:i:s')];
            }

            $dateTime->add($dateInterval);
        }

        return $return;
    }

    /**
     * @dataProvider inputTimeDataProvider
     * @param string $inputTime
     * @param string $expectedInputTime
     * @return void
     * @throws ZeitBuchungException
     * @throws Exception
     */
    public function testGetDateTimeObjectByInputTime(string $inputTime, string $expectedInputTime): void
    {
        $expectedDateTimeObject = new DateTime($expectedInputTime);
        $this->assertEquals($expectedDateTimeObject, $this->service->getDateTimeObjectByInputTime($inputTime));
    }

    /**
     * @return string[]
     */
    public function falseInputTimeDataProvider(): array
    {
        return [
            ['1259'],
            [':59'],
            ['0:60'],
            ['25:59'],
            ['12:60'],
            ['0:60:59'],
            ['5:59:60'],
            ['25:14:23'],
            ['12:60:25'],
            ['05:17:60'],
        ];
    }

    /**
     * @dataProvider falseInputTimeDataProvider
     * @param string $inputTime
     * @return void
     * @throws ZeitBuchungException
     * @throws Exception
     */
    public function testGetDateTimeObjectByInputTimeNegative(string $inputTime): void
    {
        $this->prophesiedSymfonyStyle->warning('Input time string is invalid. "' . $inputTime . '"')->shouldBeCalled();
        $this->service->setSymfonyStyle($this->prophesiedSymfonyStyle->reveal());
        $this->assertEquals(null, $this->service->getDateTimeObjectByInputTime($inputTime));
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function inputDateDataProvider(): array
    {
        $return = [];
        $dateTime = new DateTime('01.01.2020');
        $dateInterval = new DateInterval('P1D');

        for ($i = 0; $i <= 365; $i++) {
            $return[] = [$dateTime->format('d.m.'), $dateTime->format('Y-m-d')];
            $return[] = [$dateTime->format('d.m.Y'), $dateTime->format('Y-m-d')];
            $return[] = [$dateTime->format('y-m-d'), $dateTime->format('Y-m-d')];
            $return[] = [$dateTime->format('m-d'), $dateTime->format('Y-m-d')];
            $return[] = [$dateTime->format('Y-m-d'), $dateTime->format('Y-m-d')];
            $dateTime->add($dateInterval);
        }

        return $return;
    }

    /**
     * @dataProvider inputDateDataProvider
     * @param string $inputDate
     * @param string $expectedInputDate
     * @return void
     * @throws Exception
     */
    public function testGetDateTimeObjectByInputDate(string $inputDate, string $expectedInputDate): void
    {
        $expectedDateTimeObject = new DateTime($expectedInputDate);
        $this->assertEquals($expectedDateTimeObject, $this->service->getDateTimeObjectByInputDate($inputDate));
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function falseInputDateDataProvider(): array
    {
        return [
            ['1.1.'],
            ['12.1.'],
            ['13-26'],
            ['2020.10.17'],
            ['02-1'],
            ['2020-1-1'],
        ];
    }

    /**
     * @dataProvider falseInputDateDataProvider
     * @param string $inputDate
     * @return void
     * @throws Exception
     */
    public function testGetDateTimeObjectByInputDateNegative(string $inputDate): void
    {
        $this->prophesiedSymfonyStyle->warning('Input date string is invalid. "' . $inputDate . '"')->shouldBeCalled();
        $this->service->setSymfonyStyle($this->prophesiedSymfonyStyle->reveal());
        $this->assertEquals(null, $this->service->getDateTimeObjectByInputDate($inputDate));
    }
}
