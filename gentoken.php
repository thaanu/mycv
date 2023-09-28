<?php
    include __DIR__ . '/helpers.php';
    $config = include __DIR__ . '/config.php';

    $response = [
        'status' => 200,
        'message' => ''
    ];

    try {

        $apacheHeader = getApacheHeaders();

        $authkey = date('YmdHi');
        
        // Check for authentication key
        if ( ! isset($apacheHeader['authentication']) )  {
            throw new Exception('Authorization key is required', 401);
        }

        // Authenticate User
        if ( $apacheHeader['authentication'] != $authkey ) {
            throw new Exception('Unauthorized', 401);
        }

        // Generating token
        $token = md5(time());

        $expiryTime = ( isset($_POST['expiry_time']) ? $_POST['expiry_time'] : '' );
        
        file_put_contents(__DIR__ . "/tokens/$token", $expiryTime);

        $url = $config['app_url'] . "?token=$token";
        
        $response['url'] = $url;

    }
    catch ( Exception $ex ) {
        $response['status'] = $ex->getCode();
        $response['message'] = $ex->getMessage();
    }
    finally {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }

    