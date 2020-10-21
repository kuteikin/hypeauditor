<?php

namespace Hypeauditor;

use \PHPUnit\Framework\TestCase;

final class FileIteratorTest extends TestCase
{
    private $iterator;

    private $file = '../src/task3/test.xml';

    protected function setUp()
    {
        $this->iterator = new \Hypeauditor\FileIterator($this->file);
    }

    /**
     * существует ли файл
     */
    public function testFileExists()
    {
        $this->assertFileExists($this->file);
    }

    /**
     * не пустой файл
     */
    public function testFileNotEmpty()
    {
        $this->assertStringNotEqualsFile($this->file, '');
    }

    /**
     * Перемещение по файлу
     */
    public function testSeek()
    {
        $this->assertEquals(true, $this->iterator->valid());

        $this->iterator->seek(2);
        $this->assertEquals("<database name=\"ym_parser_1\">\r\n", $this->iterator->current());
        $this->assertEquals(2, $this->iterator->key());
        $this->assertEquals(true, $this->iterator->valid());

        $this->iterator->rewind();
        $this->iterator->next();
        $this->assertEquals(1, $this->iterator->key());
        $this->assertEquals("<mysqldump xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\r\n", $this->iterator->current());

        $this->iterator->next();
        $this->assertEquals(2, $this->iterator->key());
        $this->assertEquals(true, $this->iterator->valid());

        $this->iterator->next();
        $this->assertEquals(3, $this->iterator->key());
        $this->assertEquals("\t<table_structure name=\"archive_shop\">\r\n", $this->iterator->current());
    }

    /**
     * тестируем Exception
     */
    public function testInvalidPosition()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->iterator->seek(10000000000);
    }
}
