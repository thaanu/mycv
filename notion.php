<?php 
class NotionInterface {

    protected $prefixUrl = 'https://api.notion.com/v1';
    protected $databaseId = '';
    protected $results = [];
    protected $headers = [];
    protected $dataset = '';
    protected $properties = [];
    protected $page = [];
    protected $url = '';

    public function __construct($databaseId, $key)
    {
        $this->databaseId = $databaseId;
        $this->headers[] = "Authorization:$key";
        $this->headers[] = "Notion-Version:2021-05-11";
    }

    public function workExperiences() 
    {
        $this->queryBuilder('databases');
        $payload["sorts"][] = ["property" => "Date", "direction" => "descending"];
        $this->sendRequest('work-exp', $this->url, $payload, 'POST');
        return $this;
    }

    public function educationQualification() 
    {
        $this->queryBuilder('databases');
        $payload["sorts"][] = ["property" => "Year", "direction" => "descending"];
        $this->sendRequest('education-qualification', $this->url, $payload, 'POST');
        return $this;
    }

    public function skills() 
    {
        $this->queryBuilder('databases');
        $payload["sorts"][] = ["property" => "Sort Order", "direction" => "descending"];
        $payload["sorts"][] = ["property" => "Rate", "direction" => "ascending"];
        $this->sendRequest('skills', $this->url, $payload, 'POST');
        return $this;
    }

    public function projects() 
    {
        $this->queryBuilder('databases');
        $payload["sorts"][] = ["property" => "Date", "direction" => "descending"];
        $this->sendRequest('projects', $this->url, $payload, 'POST');
        return $this;
    }

    public function getPage( $pageId )
    {
        $this->queryBuilder('pages', $pageId);
        $this->page = json_decode($this->call($this->url, 'GET'), true);
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function cleanup()
    {
        switch ( $this->dataset ) {
            case 'work-exp':
                return $this->cleanupWorkExperiences();
            case 'education-qualification':
                return $this->cleanupEducationQualification();
            case 'skills':
                return $this->cleanupSkills();
            case 'projects':
                return $this->cleanupProjects();
            case 'a-page':
                return $this->cleanupEducationQualification();
            default:
                return false;
        }
    }

    private function cleanupWorkExperiences() 
    {
        $col = [];
        if ( ! empty($this->results['results']) ) {
            foreach ( $this->results['results'] as $i => $we ) {
                $this->properties           = $we['properties'];
                $col[$i]['company_name']    = $this->title('Company Name');
                $col[$i]['duration']        = $this->formula('Duration');
                $col[$i]['start_date']      = $this->date('Date', 'start');
                $col[$i]['end_date']        = $this->date('Date', 'end');
                $col[$i]['department']      = $this->richText('Department');
                $col[$i]['job_type']        = $this->select('Job Type');
                $col[$i]['designation']     = $this->richText('Designation');
                $col[$i]['work_experiences']     = $this->relation('Work Experiences');
            }
        }
        return $col;
    }

    private function cleanupEducationQualification() 
    {
        $col = [];
        if ( ! empty($this->results['results']) ) {
            foreach ( $this->results['results'] as $i => $we ) {
                $this->properties               = $we['properties'];
                $col[$i]['program_name']        = $this->title('Program Name');
                $col[$i]['year']                = $this->number('Year');
                $col[$i]['facility']            = $this->richText('Facility');
                $col[$i]['facility_url']        = $this->url('Facility Website');
                $col[$i]['certificate_type']    = $this->select('Certificate Type');
                $col[$i]['mqa_level']           = $this->richText('MQA Level');
                $col[$i]['country']             = $this->select('Country');
            }
        }
        return $col;
    }

    private function cleanupSkills() 
    {
        $col = [];
        if ( ! empty($this->results['results']) ) {
            foreach ( $this->results['results'] as $i => $we ) {
                $this->properties       = $we['properties'];
                $col[$i]['skill']       = $this->title('Skill');
                $col[$i]['rate']        = $this->select('Rate');
                $col[$i]['category']    = $this->select('Category');
            }
        }
        return $col;
    }

    private function cleanupProjects() 
    {
        $col = [];
        if ( ! empty($this->results['results']) ) {
            foreach ( $this->results['results'] as $i => $we ) {
                $this->properties           = $we['properties'];
                $col[$i]['project_name']    = $this->title('Project Name');
                $col[$i]['client_name']     = $this->select('Client Name');
                $col[$i]['technologies']    = $this->multiSelect('Technologies');
                $col[$i]['summary']         = $this->richText('Summary');
                $col[$i]['date']            = $this->date('Date', 'start');
                $col[$i]['website_url']     = $this->url('Project Website');
                $col[$i]['is_offline']      = $this->checkbox('Is Offline');
                $col[$i]['cover_photo']     = $this->coverPhoto($we);
            }
        }
        return $col;
    }

    private function coverPhoto( $dataset )
    {
        $coverPhoto = $dataset['cover'];
        if ( $coverPhoto['type'] == 'file' ) {
            return $coverPhoto['file']['url'];
        }
        if ( $coverPhoto['type'] == 'external' ) {
            return $coverPhoto['external']['url'];
        }
    }

    private function title( $key ) 
    {
        return $this->properties[$key]['title'][0]['text']['content'];
    }

    private function number( $key ) 
    {
        return $this->properties[$key]['number'];
    }

    private function richText( $key )
    {
        return $this->properties[$key]['rich_text'][0]['text']['content'];
    }

    private function select( $key ) 
    {
        return $this->properties[$key]['select']['name'];
    }

    private function multiSelect( $key ) 
    {
        $data = [];
        $options = $this->properties[$key]['multi_select'];
        if ( ! empty($options) ) {
            foreach ( $options as $option ) {
                $data[] = $option['name'];
            }
        }
        return $data;
    }

    private function date( $key, $entry = 'start' )
    {
        return $this->properties[$key]['date'][$entry];
    }

    private function formula( $key ) 
    {
        return $this->properties[$key]['formula']['string'];
    }

    private function url( $key )
    {
        return $this->properties[$key]['url'];
    }

    private function relation( $key ) 
    {
        $output = [];
        $relations = $this->properties[$key]['relation'];
        if ( ! empty($relations) ) {
            foreach ( $relations as $r ) {
                $output[] = $r['id'];
            }
        }
        return $output;
    }

    private function checkbox( $key )
    {
        return $this->properties[$key]['checkbox'];
    }

    public function simplifyWorkExperiences()
    {
        if ( ! empty($this->page) ) {
            $content['cover'] = $this->page['cover'];
            $content['icon'] = $this->page['icon'];
            $content['job_info'] = $this->page['properties']['Job']['title'][0]['text']['content'];
            return $content;
        }
        return false;
    }

    private function sendRequest( $dataset, $url, $options = [], $method = 'POST' )
    {
        $this->dataset = $dataset;
        $this->results = json_decode($this->call($url, $method, $options), true);
        if ( isset($this->results['object']) && $this->results['object'] == 'error' ) {
            throw new Exception($this->results['message'], $this->results['status']);
        }
    }

    private function queryBuilder($entity, $param = '' )
    {
        if ( $entity == 'databases' ) {
            $this->url = $this->prefixUrl . "/$entity/" . $this->databaseId . '/query';
        }
        if ( $entity == 'pages' ) {
            $this->url = $this->prefixUrl . "/$entity/$param";
        }
        return $this;
    }

    /**
     * Call to a method
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * 
     * @return  string  JSON Response
     */
    private function call( $url, $method, $params = [] )
    {
        $ch = curl_init();

        $this->headers[] = 'Content-Type:application/json';

        if( $method == 'POST' ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $server_output = curl_exec($ch);

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close ($ch);
            return json_encode([
                'error_msg' => $err,
                'api_url' => $url
            ]);
        }

        curl_close ($ch);

        return ( empty($server_output) ? [] : $server_output );

    }

}