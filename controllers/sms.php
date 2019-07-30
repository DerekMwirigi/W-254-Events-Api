<?php
    class Sms extends DatabaseHandler {
        private $debug;
        private $apiconfig;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->apiconfig = json_decode(file_get_contents("../../config/api.config.json"), true);
        }

        public function expressSmss2s ($messageModels){
            $headers = array(
                ""
            );
            $smsRes = json_decode($this->utils->cURLRequest('POST', $messageModels, $this->apiconfig["endpoints"]["sms"]["express-s2s"]["url"]), true);
            return $smsRes;
        }

        public function expressSmss2m ($messageModels){
            $smsRes = json_decode($this->utils->cURLRequest('POST', $messageModels, $this->apiconfig["endpoints"]["sms"]["expresss2m"]["url"]), true);
            return $smsRes;
        }

        public function queueSms ($messageQueueModels){
            
        }
    }