<?php

namespace pallo\library\log\listener;

use pallo\library\log\LogMessage;

/**
 * Log listener to echo log items
 */
class EchoLogListener extends AbstractLogListener {

    /**
     * Echos a log item
     * @param pallo\library\log\LogItem $item Item to echo
     * @return null
     */
    public function logMessage(LogMessage $message) {
        if (!$this->isLoggable($message->getLevel())) {
            return;
        }

        echo $this->getLogMessageAsString($message);
    }

}