<?php

namespace pallo\library\log\listener;

use pallo\library\log\LogMessage;

/**
 * Log listener to echo log items
 */
class EchoLogListener extends AbstractLogListener {

    /**
     * Performs the actual logging
     * @param pallo\library\log\LogMessage $message
     * @return null
     */
    protected function log(LogMessage $message) {
        echo $this->getLogMessageAsString($message);
    }

}