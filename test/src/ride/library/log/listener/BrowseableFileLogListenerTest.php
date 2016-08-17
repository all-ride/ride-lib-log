<?php

namespace ride\library\log\listener;

use ride\library\decorator\DateFormatDecorator;
use ride\library\decorator\LowerCaseDecorator;
use ride\library\log\LogMessage;
use ride\library\log\LogSession;

class BrowseableFileLogListenerTest extends FileLogListenerTest {

    /**
     * @var string
     */
    protected $file;

    /**
     * @var ride\library\log\listener\FileLogListener
     */
    protected $listener;

    public function setUp() {
        $this->file = tempnam(sys_get_temp_dir(), 'log');
        $this->listener = new BrowseableFileLogListener($this->file);
    }

    /**
     * @dataProvider providerSetLogMessageDecoratorThrowsExceptionWhenNoLogMessageDecoratorPassed
     * @expectedException ride\library\log\exception\LogException
     */
    public function testSetLogMessageDecoratorThrowsExceptionWhenNoLogMessageDecoratorPassed($decorator) {
        $this->listener->setLogMessageDecorator($decorator);
    }

    public function providerSetLogMessageDecoratorThrowsExceptionWhenNoLogMessageDecoratorPassed() {
        return array(
            array(new LowerCaseDecorator()),
            array(new DateFormatDecorator()),
        );
    }

    public function testGetLogSession() {
        $id = 'id';

        $logSessions = $this->logMessages();

        $this->assertEquals($logSessions['id1'], $this->listener->getLogSession($logSessions['id1']->getId()));
    }

    public function testGetLogSessions() {
        $logSessions = $this->logMessages();

        $this->assertEquals($logSessions, $this->listener->getLogSessions());
    }

    public function testGetLogSessionsWithPaging() {
        $logSessions = $this->logMessages();

        $this->assertEquals(array('id2' => $logSessions['id2']), $this->listener->getLogSessions(array('page' => 2, 'limit' => 1), $pages));
        $this->assertEquals(3, $pages);
    }

    private function logMessages() {
        $message1 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title', 'description', 'source');
        $message1->setId('id1');
        $message1->setClient('client');
        $message1->setMicrotime(0.123);

        $this->listener->logMessage($message1);

        $message2 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title2', 'description2', 'source2');
        $message2->setId('id2');
        $message2->setClient('client2');
        $message2->setMicrotime(0.234);

        $this->listener->logMessage($message2);

        $message3 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title3', 'description3', 'source2');
        $message3->setId('id2');
        $message3->setClient('client2');
        $message3->setMicrotime(0.456);

        $this->listener->logMessage($message3);

        $message4 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title4', 'description4', 'source3');
        $message4->setId('id3');
        $message4->setClient('client3');
        $message4->setMicrotime(0.124);

        $this->listener->logMessage($message4);

        $logSession1 = new LogSession();
        $logSession1->addLogMessage($message1);
        $logSession2 = new LogSession();
        $logSession2->addLogMessage($message2);
        $logSession2->addLogMessage($message3);
        $logSession3 = new LogSession();
        $logSession3->addLogMessage($message4);

        return array(
            $logSession1->getId() => $logSession1,
            $logSession2->getId() => $logSession2,
            $logSession3->getId() => $logSession3,
        );
    }

}
