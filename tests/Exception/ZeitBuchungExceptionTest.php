<?php

namespace ZeitBuchung\Tests\Exception;

use Exception;
use ZeitBuchung\Exception\ZeitBuchungException;
use PHPUnit\Framework\TestCase;

/**
 * Class ZeitBuchungExceptionTest
 *
 * @package ZeitBuchung\Tests\Exception
 */
class ZeitBuchungExceptionTest extends TestCase
{
    /** @var ZeitBuchungException */
    private $testClass;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->testClass = new ZeitBuchungException();
    }

    /**
     * @return void
     */
    public function testIsInstanceOfException(): void
    {
        $this->assertInstanceOf(Exception::class, $this->testClass);
    }
}
