<?php

    if ( file_exists(__DIR__ . '/init') ) {
        echo "Already initialized\n";
        exit;
    }

    // Create folder for logs
    if ( ! is_dir(__DIR__ . '/logs') ) {
        mkdir(__DIR__.'/logs');
    }

    // Create folder for tokens
    if ( ! is_dir(__DIR__ . '/tokens') ) {
        mkdir(__DIR__.'/tokens');
    }