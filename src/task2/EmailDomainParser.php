<?php

namespace Hypeauditor;

/**
 * Class EmailDomainParser
 * @package Hypeauditor
 */

class EmailDomainParser
{
    /**
     * Файла c путем до него
     * @var null
     */
    protected $_file = null;

    /**
     * данные по почтовым доменам
     * @var array
     */
    protected $_email_domain_data = array();

    public function __construct($file)
    {
        $this->_file = $file;
    }

    /**
     * Получаем данные по почтовым доменам
     *
     * @return array
     */
    public function getData ()
    {
        foreach ($this->getItem($this->_file) as $item) {
            $item = trim($item);
            if(stripos($item, '<field name="email">') === 0) {
                $domains = $this->getDomains($item);
                if(count($domains) > 0) {
                    foreach ($domains as $domain) {
                        if(isset($this->_email_domain_data[$email_data[1]])) {
                            $this->_email_domain_data[$domain]++;
                        } else {
                            $this->_email_domain_data[$domain] = 1;
                        }
                    }
                }
            }
        }

        arsort($this->_email_domain_data);

        return $this->_email_domain_data;
    }

    /**
     * Получаем из почтовые домены
     *
     * @param string $email
     * @return array
     */
    public function getDomains ($email)
    {
        $domains = array();
        $email = strip_tags($email);
        $email = trim($email);
        if($email) {
            $emails = explode(',', $email);
            foreach ($emails as $email) {
                $email_data = explode('@', $email);
                if(!empty($email_data[1])) {
                    $domains[] = $email_data[1];
                }
            }
        }

        // убираем возможные дубли
        $domains = array_unique($domains);
        return $domains;
    }

    /**
     * Читаем файл
     *
     * @param $file
     * @return \Generator
     */
    public function getItem($file)
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            throw new Exception("Can't open " . $file);
        }
        while (!feof($handle)) {
            yield fgets($handle);
        }
        fclose($handle);
    }
}
