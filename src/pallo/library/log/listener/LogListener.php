<?php

namespace pallo\library\log\listener;

use pallo\library\log\LogMessage;

/**
 * Interface for a log listener
 */
interface LogListener {

    /**
     * Logs a message to this listener
     * @param pallo\library\log\LogMessage $message
     * @return null
     */
    public function logMessage(LogMessage $message);

}