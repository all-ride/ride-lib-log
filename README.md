# Pallo: Log Library

Log library of the PHP Pallo framework.

Logging is used to keep an history of events or to debug an application.

## LogMessage

A log message defines what's being done or what happened.

It contains of a:

* __level__: error, warning, information or debug
* __title__: title of the message
* __description__: detailed information about the message
* __date__: date and time of the event
* __microtime__: microseconds in the application run 
* __id__: id of the incoming request
* __source__: source library or module which logged the message 
* __client__: Id of the client (eg. an IP address)

## Log

The log object offers an easy interface to log messages.
It uses the observer pattern to dispatch those logged messages to the listeners of the log.

## LogListener

A log listener performs the actual logging of the message.
The most common thing to do is write a log message to a file.
n implementation to do just that has been provided.

## Code Sample

Check this code sample to see the possibilities of this library:

    <?php
    
    use pallo\library\log\listener\FileLogListener;
    use pallo\library\log\Log;
    use pallo\library\String;
    
    // generate a request string
    $string = new String();
    $id = $string->generate();
    
    // obtain the client
    $client = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

    // create a listener    
    $listener = new FileLogListener('/path/to/log.file'); // make sure it's writable
    $listener->setFileTruncateSize(512); // in kilobytes
    
    // create the log object
    $log = new Log();
    $log->setId($id);
    $log->setClient($client);
    $log->addLogListener($listener);
    
    // do some logging
    $log->logDebug('Debug message');
    $log->logInformation('Information message', 'with a description', 'my-module');
    $log->logWarning('Warning message', 'with a description', 'my-module');
    $log->logError('Debug message', 'with a description');
    $log->logException(new Exception('A exception'));