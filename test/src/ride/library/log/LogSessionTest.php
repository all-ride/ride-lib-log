<?php

namespace ride\library\log;

use ride\library\log\listener\LogListener;
use ride\library\Timer;

use \Exception;
use \PHPUnit_Framework_TestCase;

class LogSessionTest extends PHPUnit_Framework_TestCase {

    protected $logSession;

    public function setUp() {
        $this->logSession = new LogSession();
    }

    public function testConstruct() {
        $this->assertNull($this->logSession->getId());
        $this->assertNull($this->logSession->getDate());
        $this->assertNull($this->logSession->getMicrotime());
        $this->assertNull($this->logSession->getClient());
        $this->assertEquals(array(), $this->logSession->getLogMessages());
    }

    /**
     * @dataProvider providerId
     */
    public function testId($id) {
        $this->logSession->setId($id);

        $this->assertEquals($id, $this->logSession->getId());
    }

    public function providerId() {
        return array(
            array('My id'),
            array('5'),
            array(null),
        );
    }

    /**
     * @dataProvider providerSetIdThrowsExceptionWhenInvalidIdProvided
     * @expectedException ride\library\log\exception\LogException
     */
    public function testSetIdThrowsExceptionWhenInvalidIdProvided($id) {
        $this->logSession->setId($id);
    }

    public function providerSetIdThrowsExceptionWhenInvalidIdProvided() {
        return array(
            array(false),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerDate
     */
    public function testDate($date) {
        $this->logSession->setDate($date);

        $this->assertEquals($date, $this->logSession->getDate());
    }

    public function providerDate() {
        return array(
            array(123456789),
            array('123456'),
            array(null),
        );
    }

    /**
     * @dataProvider providerSetDateThrowsExceptionWhenInvalidDateProvided
     * @expectedException ride\library\log\exception\LogException
     */
    public function testSetDateThrowsExceptionWhenInvalidDateProvided($date) {
        $this->logSession->setDate($date);
    }

    public function providerSetDateThrowsExceptionWhenInvalidDateProvided() {
        return array(
            array(false),
            array('test'),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerMicrotime
     */
    public function testMicrotime($microtime) {
        $this->logSession->setMicrotime($microtime);

        $this->assertEquals($microtime, $this->logSession->getMicrotime());
    }

    public function providerMicrotime() {
        return array(
            array(0.567),
            array('123456'),
            array(null),
        );
    }

    /**
     * @dataProvider providerSetMicrotimeThrowsExceptionWhenInvalidMicrotimeProvided
     * @expectedException ride\library\log\exception\LogException
     */
    public function testSetMicrotimeThrowsExceptionWhenInvalidMicrotimeProvided($microtime) {
        $this->logSession->setMicrotime($microtime);
    }

    public function providerSetMicrotimeThrowsExceptionWhenInvalidMicrotimeProvided() {
        return array(
            array(false),
            array('test'),
            array(-500),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerTitle
     */
    public function testTitle($title) {
        $this->logSession->setTitle($title);

        $this->assertEquals($title, $this->logSession->getTitle());
    }

    public function providerTitle() {
        return array(
            array('A title'),
            array('500'),
            array(500),
            array(null),
        );
    }

    /**
     * @dataProvider providerSetTitleThrowsExceptionWhenInvalidTitleProvided
     * @expectedException ride\library\log\exception\LogException
     */
    public function testSetTitleThrowsExceptionWhenInvalidTitleProvided($microtime) {
        $this->logSession->setTitle($microtime);
    }

    public function providerSetTitleThrowsExceptionWhenInvalidTitleProvided() {
        return array(
            array(false),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerClient
     */
    public function testClient($client) {
        $this->logSession->setClient($client);

        $this->assertEquals($client, $this->logSession->getClient());
    }

    public function providerClient() {
        return array(
            array('A client'),
            array('500'),
            array(500),
            array(null),
        );
    }

    /**
     * @dataProvider providerSetClientThrowsExceptionWhenInvalidClientProvided
     * @expectedException ride\library\log\exception\LogException
     */
    public function testSetClientThrowsExceptionWhenInvalidClientProvided($client) {
        $this->logSession->setClient($client);
    }

    public function providerSetClientThrowsExceptionWhenInvalidClientProvided() {
        return array(
            array(false),
            array(array()),
            array($this),
        );
    }

    public function testAddLogMessage() {
        $title = 'title';
        $microtime = 0.123456789;
        $microtime2 = $microtime + 0.0500;
        $id = 'id';
        $client = 'client';

        $message = new LogMessage(LogMessage::LEVEL_INFORMATION, $title, 'description', 'source');
        $message->setId($id);
        $message->setClient($client);
        $message->setMicrotime($microtime);

        $message2 = new LogMessage(LogMessage::LEVEL_INFORMATION, $title . '2', 'description', 'source');
        $message2->setId($id . '2');
        $message2->setClient($client . '2');
        $message2->setMicrotime($microtime2);

        $message3 = new LogMessage(LogMessage::LEVEL_INFORMATION, $title . '3', 'description', 'source');
        $message3->setId($id . '3');
        $message3->setClient($client . '3');
        $message3->setMicrotime($microtime + 0.0250);

        $this->logSession->addLogMessage($message);

        $this->assertNotNull($this->logSession->getDate());
        $this->assertEquals($id, $this->logSession->getId());
        $this->assertEquals($client, $this->logSession->getClient());
        $this->assertEquals($microtime, $this->logSession->getMicrotime());
        $this->assertEquals(array($message), $this->logSession->getLogMessages());

        $this->logSession->addLogMessage($message2);

        $this->assertNotNull($this->logSession->getDate());
        $this->assertEquals($id, $this->logSession->getId());
        $this->assertEquals($client, $this->logSession->getClient());
        $this->assertEquals($microtime2, $this->logSession->getMicrotime());
        $this->assertEquals(array($message, $message2), $this->logSession->getLogMessages());

        $this->logSession->addLogMessage($message3);

        $this->assertNotNull($this->logSession->getDate());
        $this->assertEquals($id, $this->logSession->getId());
        $this->assertEquals($client, $this->logSession->getClient());
        $this->assertEquals($microtime2, $this->logSession->getMicrotime());
        $this->assertEquals(array($message, $message2, $message3), $this->logSession->getLogMessages());
    }

    public function testGetLogMessagesBySource() {
        $source1 = 'source1';
        $source2 = 'source2';
        $source3 = 'source3';

        $message1 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title1', 'description', 'source');
        $message1->setSource($source1);

        $message2 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title2', 'description', 'source');
        $message2->setSource($source1);

        $message3 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title3', 'description', 'source');
        $message3->setSource($source2);

        $this->logSession->addLogMessage($message1);
        $this->logSession->addLogMessage($message2);
        $this->logSession->addLogMessage($message3);

        $this->assertEquals(array($message1, $message2), $this->logSession->getLogMessagesBySource($source1));
        $this->assertEquals(array($message3), $this->logSession->getLogMessagesBySource($source2));
        $this->assertEquals(array(), $this->logSession->getLogMessagesBySource($source3));
    }

    public function testGetLogMessagesByQuery() {
        $query1 = 'title';
        $query2 = 'description';
        $query3 = array('title', 'description');
        $query4 = 'foo';
        $query5 = 'title foo';
        $query6 = 'john';

        $message1 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title foo', 'description', 'source');
        $message2 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title bar', 'description', 'source');
        $message3 = new LogMessage(LogMessage::LEVEL_INFORMATION, 'title foo the second', 'description', 'source');

        $this->logSession->addLogMessage($message1);
        $this->logSession->addLogMessage($message2);
        $this->logSession->addLogMessage($message3);

        $this->assertEquals(array($message1, $message2, $message3), $this->logSession->getLogMessagesByQuery($query1));
        $this->assertEquals(array($message1, $message2, $message3), $this->logSession->getLogMessagesByQuery($query2));
        $this->assertEquals(array($message1, $message2, $message3), $this->logSession->getLogMessagesByQuery($query3));
        $this->assertEquals(array($message1, $message3), $this->logSession->getLogMessagesByQuery($query4));
        $this->assertEquals(array($message1, $message3), $this->logSession->getLogMessagesByQuery($query5));
        $this->assertEquals(array(), $this->logSession->getLogMessagesByQuery($query6));
    }

}
