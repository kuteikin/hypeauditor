<?php

namespace Hypeauditor;

use \PHPUnit\Framework\TestCase;

final class EmailDomainParserTest extends TestCase
{
    private $email_domain_parser;

    private $file = '../src/task2/test.xml';

    protected function setUp()
    {
        $this->email_domain_parser = new \Hypeauditor\EmailDomainParser($this->file);
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
     * извлекаем домены
     */
    public function testGetDomains()
    {
        $this->assertEquals(array('mail.ru'), $this->email_domain_parser->getDomains('5670@mail.ru'));
        $this->assertEquals(array('mail.ru','yandex.ru'), $this->email_domain_parser->getDomains('5670@mail.ru,8451719@yandex.ru'));
    }

    /**
     * ищем тег с почтой в первых 500 строках
     */
    public function testSearchEmail()
    {
        $i = 0;
        $row_search = 500;
        foreach ($this->email_domain_parser->getItem($this->file) as $item) {
            $item = trim($item);
            if(stripos($item, '<field name="email">') === 0) {
               $i = $row_search;
               break;
            }
            if($i > $row_search) {
                break;
            }
            $i++;
        }

        $this->assertEquals($i, $row_search);
    }
}
