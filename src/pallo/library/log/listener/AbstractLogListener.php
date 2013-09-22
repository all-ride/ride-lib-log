<?php

namespace pallo\library\log\listener;

use pallo\library\decorator\Decorator;
use pallo\library\log\LogMessage;

/**
 * Log listener to echo log items to the screen
 */
abstract class AbstractLogListener implements LogListener {

	/**
	 * Separator between the fields
	 * @var string
	 */
	const FIELD_SEPARATOR = ' - ';

	/**
	 * Array with the level translated in human readable form
	 * @var array
	 */
	protected $levels;

	/**
	 * Maximum level to log
	 * @var integer
	 */
	protected $level;

	/**
	 * Decorator for the date value
	 * @var pallo\library\decorator\Decorator
	 */
	protected $dateDecorator;

	/**
	 * Decorator for the memory value
	 * @var pallo\library\decorator\Decorator
	 */
	protected $memoryDecorator;

    /**
     * Construct a new file log listener
     * @param string $fileName Path of the log file
     * @return null
     */
    public function __construct() {
        $this->levels = array(
            LogMessage::LEVEL_ERROR => 'E',
            LogMessage::LEVEL_WARNING => 'W',
            LogMessage::LEVEL_INFORMATION => 'I',
            LogMessage::LEVEL_DEBUG => 'D',
        );

        $this->level = 0;
    }

    /**
     * Sets the log level
     * @param integer $level 0 for all levels, see LogMessage level constants
     * @return null
     * @see LogMessage
     */
    public function setLevel($level) {
        $this->level = $level;
    }

    /**
     * Gets the log level
     * @return integer
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * Sets the decorator for the date value
     * @param pallo\library\decorator\Decorator $dateDecorator
     * @return null
     */
    public function setDateDecorator(Decorator $dateDecorator) {
    	$this->dateDecorator = $dateDecorator;
    }

  	/**
   	 * Gets the decorator for the date value
   	 * @return pallo\library\decorator\Decorator
   	 */
    public function getDateDecorator() {
    	return $this->dateDecorator;
    }

    /**
     * Sets the decorator for the memory value
     * @param pallo\library\decorator\Decorator $memoryDecorator
     * @return null
     */
    public function setMemoryDecorator(Decorator $memoryDecorator) {
    	$this->memoryDecorator = $memoryDecorator;
    }

    /**
     * Gets the decorator for the memory value
     * @return pallo\library\decorator\Decorator
     */
    public function getMemoryDecorator() {
    	return $this->memoryDecorator;
    }

    /**
     * Checks if the provided level should be logged
     * @param integer $level Level to check
     * @return boolean True to log, false otherwise
     */
    protected function isLoggable($level) {
        if (!$this->level || $this->level & $level) {
            return true;
        }

        return false;
    }

    /**
     * Get the output string of a log item
     * @param pallo\library\log\LogMessage $message
     * @return string
     */
    protected function getLogMessageAsString(LogMessage $message) {
    	$date = $message->getDate();
    	if ($this->dateDecorator) {
    		$date = $this->dateDecorator->decorate($date);
    	}

    	$memory = memory_get_usage();
    	if ($this->memoryDecorator) {
    		$memory = $this->memoryDecorator->decorate($memory);
    	}

        $output = $message->getId();
        $output .= self::FIELD_SEPARATOR . $date;
        $output .= self::FIELD_SEPARATOR . substr($message->getMicroTime(), 0, 5);
        $output .= self::FIELD_SEPARATOR . $message->getClient();
        $output .= self::FIELD_SEPARATOR . str_pad($message->getSource(), 8);
        $output .= self::FIELD_SEPARATOR . str_pad($memory, 9, ' ', STR_PAD_LEFT);
        $output .= self::FIELD_SEPARATOR . $this->levels[$message->getLevel()];
        $output .= self::FIELD_SEPARATOR . $message->getTitle();

        $description = $message->getDescription();
        if (!empty($description)) {
            $output .= self::FIELD_SEPARATOR . $description;
        }
        $output .= "\n";

        return $output;
    }

}