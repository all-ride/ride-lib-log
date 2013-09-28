<?php

namespace pallo\library\log\listener;

use pallo\library\decorator\Decorator;
use pallo\library\log\LogMessage;

use \PHPUnit_Framework_TestCase;

class EchoLogListenerTest extends PHPUnit_Framework_TestCase implements Decorator {

    public function decorate($value) {
        return 'logged';
    }

    public function testLogMessage() {
        $this->expectOutputString("logged");

        $message = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title', 'description', 'source');

        $listener = new EchoLogListener();
        $listener->setLogMessageDecorator($this);

        $listener->logMessage($message);
    }

}