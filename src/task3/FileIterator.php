<?php

namespace Hypeauditor;

/**
 * Class FileIterator
 * @package Hypeauditor
 */

class FileIterator implements \SeekableIterator
{

    /**
     * Файла c путем до него
     * @var null
     */
    protected $_file = null;
    private $file;

    /** @var \Generator */
    private $fileGenerator;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function rewind()
    {
        $this->fileGenerator = $this->openFile($this->file);
    }

    /**
     * @return \Generator
     */
    protected function getFileGenerator()
    {
        if (!$this->fileGenerator) {
            $this->rewind();
        }
        return $this->fileGenerator;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->getFileGenerator()->current();
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->getFileGenerator()->key();
    }

    public function next()
    {
        $this->getFileGenerator()->next();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->getFileGenerator()->valid();
    }

    /**
     * @param int $position
     */
    public function seek($position)
    {
        while ($this->valid()) {
            if ($this->getFileGenerator()->key() == $position) {
                return;
            }
            $this->getFileGenerator()->next();
        }
        throw new \OutOfBoundsException("Invalid seek position ($position)");
    }

    /**
     * Читаем файл
     *
     * @param $file
     * @return \Generator
     */
    private function openFile($file)
    {
        $handle = fopen($file, "r");
        if (!$handle) {
            throw new Exception("Can't open " . $file);
        }

        while (!feof($handle)) {
            yield fgets($handle);
        }

        fclose($handle);
    }
}
