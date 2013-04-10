<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: vegass
     * Date: 04.04.13
     * Time: 13:23
     * To change this template use File | Settings | File Templates.
     */

    /**
     * Create access
     * https://oauth.vk.com/authorize?
     * client_id=APP_ID&
     * scope=PERMISSIONS&
     * redirect_uri=REDIRECT_URI&
     * response_type=code
     *
     * https://oauth.vk.com/authorize?client_id=3208672&scope=notify,friends,photos,audio,video,docs,notes,pages,status,offers,questions,wall,groups,notifications,stats,ads,offline,nohttps&redirect_uri=http://oauth.vk.com/blank.html&display=popup&response_type=token
     **/

    class VK_API
    {
        private $host;
        private $access_token;
        private $secret;
        private $response;

        public function __construct ($access_token, $secret, $host = 'https://api.vk.com')
        {
            $this->access_token = $access_token;
            $this->secret       = $secret;
            $this->host         = $host;
        }

        public function call ($method, $vars = array())
        {
            if (!is_array ($vars)) {
                $vars = array();
            }

            $vars['access_token'] = $this->access_token;
            $query                = '/method/' . $method . '?' . $this->build_http_query ($vars);

            $sig = md5 ($query . $this->secret);

            $query = $this->host . $query . '&sig=' . $sig;

            $this->response = file_get_contents ($query);

            return $this;
        }

        public function get_response (&$response = null)
        {
            $temp_response = json_decode ($this->response, true);

            if (array_key_exists ('response', $temp_response)) {
                $temp_response = $temp_response['response'];
            } elseif (array_key_exists ('error', $temp_response)) {
                $temp_response = $temp_response['error'];
            }

            $response = $temp_response;

            return $temp_response;
        }

        private function build_http_query ($query)
        {

            $query_array = array();

            foreach ($query as $key => $key_value) {

                $query_array[] = $key . '=' . urlencode ($key_value);

            }

            return implode ('&', $query_array);

        }
    }