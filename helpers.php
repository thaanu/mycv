<?php 
    function ageCalc($birthDate) {
        $birthDate = explode("-", $birthDate);
        return (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));
    }

    function logMessage( $message, $file_path = '' )
    {
        $logFile = __DIR__ . '/logs/logFile.txt';

        if( $file_path != '' ) {
            $logFile = $file_path;
        }

        // Create a file if does not exist
        if( file_exists($logFile) == false ) {
            touch($logFile);
        }

        // Read File
        $oldContent = file_get_contents($logFile);

        // Handle array and object
        if ( is_object($message) || is_array($message) ) {
            $message = print_r($message, true);
        }

        // Append
        $newContent = date('Y-m-d H:i:s') . "\t";
        $newContent .= $message . "\n";
        $newContent .= $oldContent;

        // Write File
        file_put_contents($logFile, $newContent);

    }

    function error( $code ) {
        switch ( $code ) {
            case 401:
                include __DIR__ . '/errors/401.html'; exit;
                break;
            case 404:
                include __DIR__ . '/errors/404.html'; exit;
                break;
            default:
                include __DIR__ . '/errors/500.html'; exit;
        }
    }

    function validToken( $token ) {
        if ( file_exists(__DIR__ . "/tokens/$token") ) {
            if ( tokenExpired($token) ) {
                return false;
            }
            return true;
        }
        return false;
    }

    function auth() {

        // Check for token
        if ( ! isset($_GET['token']) ) { error( 401 ); }

        // Check if the token is expired
        if ( ! validToken( $_GET['token'] ) ) { error(401); }

    }

    function tokenExpired( $token ) {
        // Fetch the content
        $content = file_get_contents(__DIR__ . "/tokens/$token");

        // If there are no expiry time, return false
        if ( empty($content) ) { return false; }

        // Compare if the token is not expired
        if ( date("Y-m-d H:i") > $content ) { return true; }

        // By default the token is not expired
        return false;
    }

    function getApacheHeaders() {
        $output = [];
        $headers = apache_request_headers();
        foreach ( $headers as $key => $value ) {
            $output[strtolower($key)] = $value;
        }
        return $output;
    }