# Ride: Log Library

Log library of the PHP Ride framework.

Logging is used to keep an history of events or to debug an application.

## LogMessage

A log message defines what's being done or what happened.

It consists a:

* __level__: error, warning, information or debug
* __title__: title of the message
* __description__: detailed information about the message
* __date__: date and time of the event
* __microtime__: microseconds in the application run 
* __id__: id of the log session
* __source__: source library or module which logged the message 
* __client__: Id of the client (eg. an IP address)

## LogSession

A _LogSession_ is a collection of log messages which belong together.
For example, all logged messages from handling the same HTTP request. 

## Log

The log object is the facade to the library which offers an easy interface to log messages.
It uses the observer pattern to dispatch those logged messages to the listeners of the log.

## LogListener

A log listener performs the actual logging of the message.
The most common thing to do is write a log message to a file.
An implementation to do just that has been provided.

## BrowseableLogListener

The browseable log listener is an extension of the regular log listener.
It adds functionality to retrieve and inspect log messages back from the log.

## Code Sample

Check this code sample to see the possibilities of this library:

```php
<?php

use ride\library\decorator\LogMessageDecorator;
use ride\library\log\listener\BrowseableFileLogListener;
use ride\library\log\Log;

// obtain the client and generate a log session id
$client = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
$logSessionId = 'abc123';

// create a listener
$listener = new BrowseableFileLogListener('/path/to/log.file'); // make sure it's writable
$listener->setFileTruncateSize(512); // in kilobytes
$listener->setLogMessageDecorator(new LogMessageDecorator()); // formats the log messages

// create the log object
$log = new Log();
$log->setId($logSessionId);
$log->setClient($client);
$log->addLogListener($listener);

// do some logging
$log->logDebug('Debug message');
$log->logInformation('Information message', 'with a description', 'source');
$log->logWarning('Warning message', 'with a description', 'my-module');
$log->logError('Debug message', 'with a description');
$log->logException(new Exception('A exception'));

// browse the log
$logSessions = $listener->getLogSessions(array('limit' => 10, 'page' => 2), $pages);
$logSession = $listener->getLogSession($logSessionId);
$logMessages = $logSession->getLogMessages();
$logMessages = $logSession->getLogMessagesBySource('my-module');
$logMessages = $logSession->getLogMessagesByQuery('message');
```
    
