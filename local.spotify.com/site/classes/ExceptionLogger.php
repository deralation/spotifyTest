<?php
class ExceptionLogger extends Exception {
	public function __construct($message = null, $code = 0, Exception $previous = null) {
        
		// Pass to default Exception handler
        parent::__construct($message, $code, $previous);
        
        // Log on server
        error_log($message);

        // Send to Sentry via Raven client
        if(ENV=="PRODUCTION") {
            global $logger;
            $logger->captureException($this);
        }

        // If DEV environment stop
        if(ENV!="PRODUCTION" && MODULE!="TRACKING") {
            echo "File: <br />".$this->getFile()."<br /><br />Line: <br />".$this->getLine()." <br /><br />Message: <br />".$message." <br /><br />Trace: <br />".$this->getTraceAsString();
        }
    }
}

?>