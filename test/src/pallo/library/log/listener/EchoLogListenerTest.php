<?php

namespace pallo\library\log\listener;

use pallo\library\decorator\DateFormatDecorator;
use pallo\library\decorator\StorageSizeDecorator;
use pallo\library\log\LogMessage;

use \PHPUnit_Framework_TestCase;

class EchoLogListenerTest extends PHPUnit_Framework_TestCase {

	public function testLogMessage() {
		$this->expectOutputRegex("/id - ([0-9])* - 0\.123 - client - source   - ([0-9 ])* - I - title - description\\n/");

		$message = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title', 'description', 'source');
		$message->setId('id');
		$message->setClient('client');
		$message->setMicrotime(0.123456789);

		$listener = new EchoLogListener();
		$listener->logMessage($message);

		$listener->setLevel(LogMessage::LEVEL_ERROR);
		$listener->logMessage($message);

		$this->assertEquals(LogMessage::LEVEL_ERROR, $listener->getLevel());
	}

	public function testDecorators() {
		$this->expectOutputRegex("/id - " . date('Y-m-d') . " - 0\.123 - client - source   - ([0-9 .])*Mb - I - title - description\\n/");

		$message = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title', 'description', 'source');
		$message->setId('id');
		$message->setClient('client');
		$message->setMicrotime(0.123456789);

		$dateDecorator = new DateFormatDecorator();
		$dateDecorator->setDateFormat('Y-m-d');
		$memoryDecorator = new StorageSizeDecorator();

		$listener = new EchoLogListener();
		$listener->setDateDecorator($dateDecorator);
		$listener->setMemoryDecorator($memoryDecorator);
		$listener->logMessage($message);

		$this->assertEquals($dateDecorator, $listener->getDateDecorator());
		$this->assertEquals($memoryDecorator, $listener->getMemoryDecorator());
	}

}