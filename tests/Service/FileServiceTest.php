<?php

namespace ZeitBuchung\Tests\Service;

use DateTime;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamException;
use ZeitBuchung\Exception\ZeitBuchungException;
use ZeitBuchung\Service\FileService;
use PHPUnit\Framework\TestCase;

/**
 * Class FileServiceTest
 *
 * @package ZeitBuchung\Tests\Service
 */
class FileServiceTest extends TestCase
{
    /** @var FileService */
    private $service;

    /** @var vfsStreamDirectory */
    private $vfsStreamRoot;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $date = new DateTime('06.01.2020');
        $this->vfsStreamRoot = vfsStream::setup('testRoot');
        $this->service = new FileService(vfsStream::url('testRoot'), $date);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $dateDirectory = '2020' . DIRECTORY_SEPARATOR . '01';
        $this->assertEquals('20200106.json', $this->service->getFileName());
        $this->assertEquals(vfsStream::url('testRoot'), $this->service->getSavePath());
        $this->assertEquals($dateDirectory, $this->service->getDateDirectory());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetterAndGetterOfFileName(): void
    {
        $this->assertEquals('20200106.json', $this->service->getFileName());
        $this->service->setFileNameByDate(new DateTime('24.12.2019'));
        $this->assertEquals('20191224.json', $this->service->getFileName());
    }

    /**
     * @return void
     * @throws ZeitBuchungException
     * @throws vfsStreamException
     */
    public function testSetterAndGetterOfSavePath(): void
    {
        $this->assertEquals(vfsStream::url('testRoot'), $this->service->getSavePath());
        $this->vfsStreamRoot->addChild(new vfsStreamDirectory('path'));

        $this->assertDirectoryExists(vfsStream::url('testRoot/path'));
        $this->service->setSavePath(vfsStream::url('testRoot/path'));
        $this->assertEquals(vfsStream::url('testRoot/path'), $this->service->getSavePath());

        $this->assertDirectoryNotExists(vfsStream::url('exception/path'));
        $this->expectException(ZeitBuchungException::class);
        $this->expectExceptionMessage('Given save path does not exist! "vfs://exception/path"');
        $this->service->setSavePath(vfsStream::url('exception/path'));
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetterAndGetterOfDateDirectory(): void
    {
        $dateDirectory = '2020' . DIRECTORY_SEPARATOR . '01';
        $newDateDirectory = '2019' . DIRECTORY_SEPARATOR . '11';
        $todayDateDirectory = str_replace('/', DIRECTORY_SEPARATOR, date('Y/m'));

        $this->assertEquals($dateDirectory, $this->service->getDateDirectory());
        $this->service->setDateDirectory(new DateTime('24.11.2019'));
        $this->assertEquals($newDateDirectory, $this->service->getDateDirectory());
        $this->service->setDateDirectory(null);
        $this->assertEquals($todayDateDirectory, $this->service->getDateDirectory());
    }

    /**
     * @return void
     */
    public function testGetFullSavePath(): void
    {
        $expectedString = vfsStream::url('testRoot') . DIRECTORY_SEPARATOR . '2020' . DIRECTORY_SEPARATOR . '01';
        $this->assertEquals($expectedString, $this->service->getFullSavePath());
    }

    /**
     * @return void
     */
    public function testFileExists(): void
    {
        $this->assertFalse($this->service->fileExists());
        vfsStream::create(
            [
                '2020' => [
                    '01' => [
                        '20200106.json' => '',
                    ],
                ],
            ],
            $this->vfsStreamRoot
        );
        $this->assertFileExists(vfsStream::url('testRoot/2020/01/20200106.json'));
        $this->assertTrue($this->service->fileExists());
    }

    /**
     * @return void
     */
    public function testDateDirectoryExists(): void
    {
        $this->assertFalse($this->service->dateDirectoryExists());
        vfsStream::create(
            [
                '2020' => [
                    '01' => [],
                ],
            ],
            $this->vfsStreamRoot
        );
        $this->assertDirectoryExists(vfsStream::url('testRoot/2020/01'));
        $this->assertTrue($this->service->dateDirectoryExists());
    }

    /**
     * @return void
     * @throws ZeitBuchungException
     */
    public function testCreateDateDirectory(): void
    {
        $this->assertFalse($this->service->dateDirectoryExists());
        $this->service->createDateDirectory();
        $this->assertDirectoryExists(vfsStream::url('testRoot/2020/01'));
    }

    /**
     * @return void
     * @throws ZeitBuchungException
     */
    public function testGetFileContent(): void
    {
        vfsStream::create(
            [
                '2020' => [
                    '01' => [
                        '20200106.json' => 'file content ABC 1234',
                    ],
                ],
            ],
            $this->vfsStreamRoot
        );
        $this->assertFileExists(vfsStream::url('testRoot/2020/01/20200106.json'));
        $this->assertEquals('file content ABC 1234', $this->service->getFileContent());
    }

    /**
     * @return void
     * @throws ZeitBuchungException
     */
    public function testSaveContentToFile(): void
    {
        vfsStream::create(
            [
                '2020' => [
                    '01' => [
                        '20200106.json' => 'file content ABC 1234',
                    ],
                ],
            ],
            $this->vfsStreamRoot
        );
        $this->assertFileExists(vfsStream::url('testRoot/2020/01/20200106.json'));
        $this->assertEquals('file content ABC 1234', file_get_contents(vfsStream::url('testRoot/2020/01/20200106.json')));
        $this->service->saveContentToFile('NEW CONTENT');
        $this->assertEquals('NEW CONTENT', file_get_contents(vfsStream::url('testRoot/2020/01/20200106.json')));
    }
}
