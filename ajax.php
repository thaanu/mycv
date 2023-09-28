<?php

    $config = include __DIR__ . '/config.php';
    include __DIR__ . '/helpers.php';
    include __DIR__ . '/notion.php';

    session_start();

    $response = [ 'status' => 200, 'message' => 'OK' ];

    try {

        // Check for correct request
        if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) { throw new Exception('Invalid method', 400); }
        
        // Authenticate        
        $apacheHeader = apache_request_headers();
        if ( !isset($apacheHeader['Tken']) ) { throw new Exception('Token Required', 401); }
        if ( $_SESSION['token'] != $apacheHeader['Tken'] ) { throw new Exception('Invalid token', 401); }


        // Set the action
        $action = $_GET['action'];

        // Work Experience
        if ( $action == 'work-experience' ) {
            $content = '';

            // Fetch information from Notion Work Experience Database
            $notionResponse = (new NotionInterface($config['notion']['work_exp_db_id'], $config['notion']['key']))->workExperiences()->cleanup();

            // Set tags
            if ( ! empty($notionResponse) ) {
                foreach ( $notionResponse as $i => $w ) {
                    $content .= workTag($i, $w);
                }
            }

            $response['content'] = $content;
        }

        // Get experience details
        else if ( $action == 'get-experiences' ) {

            if ( ! isset($_POST['pages']) ) { throw new Exception('Undefined experiences', 404); }

            // Extract
            $pages = explode(',', $_POST['pages']);
            $experiences = [];

            // Fetch all the experiences
            foreach ( $pages as $pageId ) {
                $experiences[] = (new NotionInterface(null, $config['notion']['key']))->getPage($pageId)->simplifyWorkExperiences();   
            }

            $response['content'] =  experiencesTag( $experiences );
        }

        // Get Skills
        else if ( $action == 'skills' ) {

            // todo: fetch from notion

            $notionResponse = (new NotionInterface($config['notion']['skills_db_id'], $config['notion']['key']))->skills()->cleanup();

            $skills = [];
            if ( ! empty($notionResponse) ) {
                foreach ( $notionResponse as $i => $nr ) {
                    $category   = $nr['category'];
                    $skill      = $nr['skill'];
                    $rate       = $nr['rate'];
                    $skills[$category][$i] = [
                        "skill" => $skill,
                        "rate" => $rate
                    ];
                }
            }

            $response['content'] = skillsTag($skills);
        }

        // Education Qualification
        else if ( $action == 'education-qualification' ) {
            $content = '';

            // Fetch information from Notion Work Experience Database
            $notionResponse = (new NotionInterface($config['notion']['education_db_id'], $config['notion']['key']))->educationQualification()->cleanup();

            // Set tags
            if ( ! empty($notionResponse) ) {
                foreach ( $notionResponse as $i => $w ) {
                    $content .= educationTag($i, $w);
                }
            }

            $response['content'] = $content;
        }

        // Fetch bio
        else if ( $action == 'bio' ) {

            $bioFile = __DIR__ . '/bio.json';

            if ( ! file_exists($bioFile) ) {
                throw new Exception("Bio data file not found", 404);
            }

            $bio = json_decode(file_get_contents($bioFile), true);

            $age = ageCalc($bio['dob']);

            $bio['dob_formatted'] = date('d F Y', strtotime($bio['dob']));
            $bio['age'] = $age;
            $bio['dob_n_age'] = date('d F Y', strtotime($bio['dob'])) . " ($age yrs)";
            $bio['website_link'] = '<a href="'.$bio['website']['link'].'" target="_blank" >'.$bio['website']['title'].'</a>';
            $bio['last_updated_date_formatted'] = date('d F Y', strtotime($bio['last_updated_date']));

            $response['content'] = $bio;

        }

        // Projects
        else if ( $action == 'projects' ) {

            // Fetch all the projects
            $notionResponse = (new NotionInterface($config['notion']['projects_db_id'], $config['notion']['key']))->projects()->cleanup();
            $abc = (new NotionInterface($config['notion']['projects_db_id'], $config['notion']['key']))->projects()->getResults();

            logMessage($abc);

            // Handle the first 3 projects
            $content = '<div class="project-stack">';
            for ( $i = 0; $i < 3; $i++ ) {
                $content .= projectTag( $i, $notionResponse[$i] );
            }
            $content .= '</div>';

            // Handle the next 4 projects
            $content .= '<div class="project-compact">';
            for ( $i = 3; $i < 7; $i++ ) {
                $content .= projectCompactTag( $i, $notionResponse[$i] );
            }
            $content .= '</div>';

            // Handle the rest of the projects
            $content .= '<div class="project-list">';
            for ( $i = 7; $i < count($notionResponse); $i++ ) {
                $content .= projectList( $i, $notionResponse[$i] );
            }
            $content .= '</div>';
            
            $response['content'] = $content;

            

        }
        
        // For everything else
        else {
            throw new Exception('Invalid action', 400);
        }

    }
    catch ( Exception $e ) {
        $response = [
            'status' => $e->getCode(),
            'message' => $e->getMessage()
        ];
    }
    finally {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }