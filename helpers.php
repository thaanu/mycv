<?php 
    function ageCalc($birthDate) {
        $birthDate = explode("-", $birthDate);
        return (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));
    }

    // function projectTag( $i, $data ) {
    //     $coverImage = ( empty($data['cover_photo']) ? '54aa419c436e3af8d3d6fad35c9e35dc0707.jpg' : $data['cover_photo'] );
    //     $output = '<div class="project-box">';
    //     $output .= '<div class="img" style="background: #000 url('.$coverImage.') no-repeat center center;  background-size: cover;"></div>';
    //     $output .= '<div class="info">';
    //     $output .= '<h3></h3>';
    //     $output .= '<p></p>';
        
    //     if ( ! empty($data['client_name']) ) {
    //         $output .= '<p><i class="fa-solid fa-user-tie"></i> '.$data['client_name'].'</p>';
    //     }

    //     $output .= '<ul class="tech">';
    //     foreach ( $data['technologies'] as $tag ) {
    //         $output .= "<li>$tag</li>";
    //     }
    //     $output .= '</ul>';

    //     if ( $data['is_offline'] ) {
    //         $output .= '<p><i class="fa-solid fa-server"></i> This project was hosted internally</p>';
    //     } else {
    //         if ( ! empty($data['website_url']) ) {
    //             $output .= '<a href="'.$data['website_url'].'" target="_blank" class="link">Goto Project <i class="fa-solid fa-arrow-up-right-from-square"></i></a>';
    //         }
    //     }

    //     $output .= '</div>';
    //     $output .= '</div>';
    //     return $output;
    // }

    // function projectCompactTag( $i, $data ) {
    //     $coverImage = ( empty($data['cover_photo']) ? '54aa419c436e3af8d3d6fad35c9e35dc0707.jpg' : $data['cover_photo'] );
    //     $output = '<div class="compact" style="background: #000 url('.$coverImage.') no-repeat center center; background-size: cover;">';
    //     $output .= '<div class="info">';
    //     $output .= '<h3>'.$data['project_name'].'</h3>';
    //     $output .= '<p>'.$data['summary'].'</p>';

    //     if ( ! empty($data['client_name']) ) {
    //         $output .= '<p><i class="fa-solid fa-user-tie"></i> '.$data['client_name'].'</p>';
    //     }
        
    //     $output .= '<ul class="tech">';
    //     foreach ( $data['technologies'] as $tag ) {
    //         $output .= "<li>$tag</li>";
    //     }
    //     $output .= '</ul>';

    //     if ( $data['is_offline'] ) {
    //         $output .= '<p class="small-text-note"><i class="fa-solid fa-server"></i> This project was hosted internally</p>';
    //     } else {
    //         if ( ! empty($data['website_url']) ) {
    //             $output .= '<a href="'.$data['website_url'].'" target="_blank" class="link">Goto Project <i class="fa-solid fa-arrow-up-right-from-square"></i></a>';
    //         }
    //     }

    //     $output .= '</div>';
    //     $output .= '</div>';
    //     return $output;
    // }

    // function projectList( $i, $data ) {
    //     $output = '<div class="project-box">';
    //     $output .= '<div><p><b>'.$data['project_name'].'</b></p>';
        
    //     if ( ! empty($data['client_name']) ) {
    //         $output .= '<p class="small-text-note"><i class="fa-solid fa-user-tie"></i> '.$data['client_name'].'</p>';
    //     }
        
    //     $output .= '</div>';
    //     $output .= '<div>';

    //     if ( $data['is_offline'] ) {
    //         $output .= '<span class="small-text-note"><i class="fa-solid fa-server"></i> This project was hosted internally</span>';
    //     } else {
    //         if ( ! empty($data['website_url']) ) {
    //             $output .= '<a href="'.$data['website_url'].'" class="link"></a>';
    //         }
    //     }

    //     $output .= '</div>';
    //     $output .= '</div>';
    //     return $output;
    // }

    

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