<?php

namespace ZeitBuchung\Tests\Service;

use DateTime;
use Exception;
use Prophecy\Prophet;
use ZeitBuchung\Service\FileService;
use ZeitBuchung\Service\RecordService;
use PHPUnit\Framework\TestCase;

/**
 * Class RecordServiceTest
 *
 * @package ZeitBuchung\Tests\Service
 */
class RecordServiceTest extends TestCase
{
    /** @var RecordService */
    private $service;

    /** @var Prophet */
    private $prophet;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->prophet = new Prophet();
        $this->service = new RecordService();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }
}
