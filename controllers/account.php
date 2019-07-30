<?php 
    include 'sms.php';

    class Account extends DatabaseHandler {
        private $debug;
        private $sms;
        private $smsconfig;
        private $accountModel;
        private $errors;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->sms = new Sms($this->debug);
            $this->errors = array();
            $this->smsconfig = json_decode(file_get_contents("../../config/sms.config.json"), true);
            //$this->accountModel = json_decode(file_get_contents("../../models/account.json"), true);
        }
        public function updateFirebaseToken ($payLoad, $userModel){
            $dbRes = $this->update("users", $payLoad, $userModel);
            if($dbRes[0] == 1){
                return array(
                    "success"=>true,
                    "errors"=>null,
                    "status_code"=>1,
                    "status_message"=>'Succesful.',
                    "message"=>"Signed In.",
                    "data"=>$this->fetchRow("users", $payLoad)[2]
                );
            }
            array_push($this->errors, "FirebaseToken Not updated");
            return array(
                "success"=>true,
                "errors"=>$this->errors,
                "status_code"=>0,
                "status_message"=>'Failed.',
                "message"=>"FirebaseToken update failed.",
                "data"=>null
            );
        }  
    }
?>