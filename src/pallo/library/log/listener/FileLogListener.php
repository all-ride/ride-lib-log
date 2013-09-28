<?php

namespace pallo\library\log\listener;

use pallo\library\log\exception\LogException;
use pallo\library\log\LogMessage;

/**
 * Log listener to write log messages to file
 */
class FileLogListener extends AbstractLogListener {

    /**
     * Default maximum file size in kb
     * @var integer
     */
    const DEFAULT_TRUNCATE_SIZE = 1024;

    /**
     * File name of the log
     * @var string
     */
    private $fileName;

    /**
     * Maximum file size
     * @var integer
     */
    private $fileTruncateSize;

    /**
     * Construct a new file log listener
     * @param string $fileName Path of the log file
     * @return null
     * @throws pallo\library\log\exception\LogException when the provided file
     * name is empty or invalid
     */
    public function __construct($fileName) {
        if (!is_string($fileName) || $fileName == '') {
            throw new LogException('Could not construct the log listener: file name is empty or not a string');
        }

        $this->fileName = $fileName;
        $this->fileTruncateSize = self::DEFAULT_TRUNCATE_SIZE;
    }

    /**
     * Set the limit in kb before the log file gets truncated
     * @param integer $size Limit in kilobytes
     * @return null
     * @throws pallo\library\log\exception\LogException when the size is not a
     * positive number
     */
    public function setFileTruncateSize($size) {
        if (!is_numeric($size) || $size < 0) {
            throw new LogException('Could not set the file truncate size: size should be positive number or zero');
        }

        $this->fileTruncateSize = $size;
    }

    /**
     * Get the limit in kb before the log file gets truncate
     * @oaram integer size limit in kilobytes
     */
    public function getFileTruncateSize() {
        return $this->fileTruncateSize;
    }

    /**
     * Performs the actual logging
     * @param pallo\library\log\LogMessage $message
     * @return null
     */
    protected function log(LogMessage $message) {
        $output = $this->getLogMessageAsString($message);

        if ($this->writeFile($output)) {
            $this->truncateFile($output);
        }
    }

    /**
     * Append the output to the log file
     * @param string $output String to append to the file
     * @return boolean
     */
    private function writeFile($output) {
        if (!($f = @fopen($this->fileName, 'a'))) {
            return false;
        }

        fwrite($f, $output);
        fclose($f);

        return true;
    }

    /**
     * Truncate the log tile if the truncate size is set and the log file is
     * bigger then the truncate size
     * @param string $output String to write in the truncated file, empty by
     * default
     * @return null
     */
    private function truncateFile($output = '') {
        $truncateSize = $this->getFileTruncateSize();
        if (!$truncateSize) {
            return;
        }

        clearstatcache();

        $fileSize = filesize($this->fileName) / 1024; // we work with kb
        if ($fileSize < $truncateSize) {
            return;
        }

        if ($f = @fopen($this->fileName, 'w')) {
            fwrite($f, $output);
            fclose($f);
        }
    }

}