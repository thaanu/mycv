<?php
    // Template for skills
    function skillsTag( $skillset ) {
        $content = '';
        foreach ( $skillset as $title => $skills ) {
            $content .= '<h3>'.$title.'</h3>';
            $content .= '<ul class="skillset">';
            foreach ( $skills as $skill ) {
                $percent = (($skill['rate'] / 10) * 100);
                $content .= '<li>';
                $content .= '<span>'.$skill['skill'].'</span>';
                $content .= '<span class="color-bar" style="width:'.$percent.'%;"><span class="solid-color"></span></span>';
                $content .= '</li>';
            }
            $content .= '</ul>';
        }
        return $content;
    }

    // Template for work tag
    function workTag( int $i, array $work ) {
        global $config;
        $from = date($config['date_format'], strtotime($work['start_date']));
        $to = 'Present';
        if ( !empty($work['end_date']) ) {
            $to = date($config['date_format'], strtotime($work['end_date']));
        }

        $duration = '<span class="current-working-status">Currently working here since ' . date($config['date_format'], strtotime($work['start_date'])).'</span>';

        if ( $work['duration'] != 'Present' ) {
            $duration = $from .' to '. $to .' - '.$work['duration'];
        }

        $content = '<div class="work-tag">
            <div class="flex">
                <h3>'.$work['company_name'].'</h3>
                <span class="duration">'.$duration.'</span>
            </div>
            <p class="designation">'.$work['designation'].'</p>
            <p class="department">'.$work['department'].'</p>';

            if ( empty($work['work_experiences']) ) {
                $content .= '<p class="job_type">'.$work['job_type'].'</p>';
            } else {
                $content .= '<p class="job_type">'.$work['job_type'].'</p> <a href="#" data-pages="'.implode(",",$work['work_experiences']).'" data-target-id="'.$i.'" class="more-info-btn"><i class="fa-solid fa-circle-info"></i> Work Experiences</a>';
                $content .= '<div class="more-info" id="we-box-'.$i.'" style="display:none;"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>';
            }
        $content .= '
        </div>';
        return $content;
    }

    // Template for xperience Tag
    function experiencesTag( $experiences ) {
        $content = '<ul>';
        foreach ( $experiences as $workExp ) {
            $content .= '<li><i class="fa-solid fa-circle-arrow-right"></i> '.$workExp['job_info'].'</li>';
        }
        $content .='</ul>';
        return $content;
    }

    // Template for education tag
    function educationTag( $i, $data ) {
        $output = '<div class="edu-box">
            <div class="year">'.$data['year'].'</div>
            <div class="details">
                <h3>'.$data['program_name'].'</h3>';
                if ( empty($data['facility_url']) ) {
                    $output .= '<p>'.$data['facility'].'</p>';
                } else {
                    $output .= '<p><a title="Visit '.$data['facility'].' Website" href="'.$data['facility_url'].'" target="_blank">'.$data['facility'].' <i class="fa-solid fa-arrow-up-right-from-square"></i></a></p>';
                }
        $output .= '
            </div>
        </div>';
        return $output;
    }

    // Template for project hero
    function projectHero( $data ) {
        $coverImage = ( empty($data['cover_photo']) ? '54aa419c436e3af8d3d6fad35c9e35dc0707.jpg' : $data['cover_photo'] );
        $output = '<div class="project-hero">';
        $output .= '<h2>'.$data['project_name'].'</h2>';
        $output .= '<div class="meta">';
        if ( ! empty($data['client_name']) ) {
            $output .= '<span><i class="fa-solid fa-user-tie"></i> '.$data['client_name'].'</span>';
        }
        $output .= '<span><i class="fa-solid fa-calendar-check"></i> '.$data['date'].'</span>';
        $output .= '</div>';
        $output .= '<div class="img"><img src="'.$coverImage.'" alt=""></div>';
        $output .= '<p>'.$data['summary'].'</p>';

        if ( !empty($data['technologies']) ) {
            $output .= '<ul class="tags">';
            foreach ( $data['technologies'] as $tag ) {
                $icon = getIcon($tag);
                $output .= "<li>$icon $tag</li>";
            }
            $output .= '</ul>';
        }
        
        if ( $data['is_offline'] ) {
            $output .= '<span class="badge"><i class="fa-solid fa-house-signal"></i> This project was hosted offline</span>';
        } else {
            if ( ! empty($data['website_url']) ) {
                $output .= '<a href="'.$data['website_url'].'" target="_blank" class="goto-btn goto-btn-normal">Goto Project <i class="fa-solid fa-arrow-up-right-from-square"></i></a>';
            }
        }
                
        $output .= '</div>';
            
        return $output;
    }

    function projectList( $i, $data ) {
        $coverImage = ( empty($data['cover_photo']) ? '54aa419c436e3af8d3d6fad35c9e35dc0707.jpg' : $data['cover_photo'] );
        $output = '<div class="project-box">';
        $output .= '<h2>'.$data['project_name'].'</h2>';
        $output .= '<div class="meta">';
        if ( ! empty($data['client_name']) ) {
            $output .= '<span><i class="fa-solid fa-user-tie"></i> '.$data['client_name'].'</span>';
        }
        $output .= '<span><i class="fa-solid fa-calendar-check"></i> '.$data['date'].'</span>';
        $output .= '</div>';
        $output .= '<div class="img"><img src="'.$coverImage.'" alt=""></div>';
        $output .= '<p>'.$data['summary'].'</p>';

        if ( !empty($data['technologies']) ) {
            $output .= '<ul class="tags">';
            foreach ( $data['technologies'] as $tag ) {
                $icon = getIcon($tag);
                $output .= "<li>$icon $tag</li>";
            }
            $output .= '</ul>';
        }

        if ( $data['is_offline'] ) {
            $output .= '<span class="badge"><i class="fa-solid fa-house-signal"></i> This project was hosted offline</span>';
        } else {
            if ( ! empty($data['website_url']) ) {
                $output .= '<a href="'.$data['website_url'].'" target="_blank" class="goto-btn goto-btn-normal">Goto Project <i class="fa-solid fa-arrow-up-right-from-square"></i></a>';
            }
        }
        $output .= '</div>';

        return $output;
    }

    function getIcon( $tag ) {
        $default = '<i class="fa-solid fa-hashtag"></i>';
        $icons = [
            'php' => '<i class="fa-brands fa-php"></i>',
            'javascript' => '<i class="fa-brands fa-square-js"></i>',
            'mysql' => '<i class="fa-solid fa-database"></i>',
            'html' => '<i class="fa-brands fa-html5"></i>',
            'html5' => '<i class="fa-brands fa-html5"></i>',
            'css' => '<i class="fa-brands fa-css3-alt"></i>',
            'css3' => '<i class="fa-brands fa-css3-alt"></i>',
            'wordpress' => '<i class="fa-brands fa-wordpress"></i>'
        ];
        $tag = strtolower($tag);
        if ( array_key_exists($tag, $icons) ) { return $icons[$tag]; }
        $icon = $default;
        return $icon;
    }