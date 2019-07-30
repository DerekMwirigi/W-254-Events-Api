<?php 
    include 'sms.php';

    class Auth extends DatabaseHandler {
        private $debug;
        private $sms;
        private $smsconfig;
        private $authModel;
        private $errors;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->sms = new Sms($this->debug);
            $this->errors = array();
            $this->smsconfig = json_decode(file_get_contents("../../config/sms.config.json"), true);
            $this->authModel = json_decode(file_get_contents("../../models/auth.json"), true);
        }

        public function verifyUId ($authModel){
            $validRes = $this->utils->validateModel($authModel, $this->authModel["verifyUId"]);
            if($validRes["success"]){
                foreach(array("users") as $accType){
                    $dbRes = $this->fetchRow($accType, array(
                        "mobile"=>$authModel["uId"]
                    ));
                    if($dbRes[0] == 1){
                        return array(
                            "success"=>true,
                            "errors"=>null,
                            "status_code"=>1,
                            "status_message"=>'Succesful.',
                            "message"=>"Welcome " . $dbRes[2]["firstName"] . ".",
                            "data"=>$dbRes[2]
                        );
                    }
                }
                array_push($this->errors, "Seems you don't have an account");
                return array(
                    "success"=>true,
                    "errors"=>$this->errors,
                    "status_code"=>0,
                    "status_message"=>'Failed.',
                    "message"=>"Sign in failed.",
                    "data"=>null
                );
            }
            return $validRes;
        }

        public function verifyUSecret ($authModel){
            $validRes = $this->utils->validateModel($authModel, $this->authModel["verifyUSecret"]);
            if($validRes["success"]){
                foreach(array("users") as $accType){
                    $dbRes = $this->fetchRow($accType, array(
                        "mobile"=>$authModel["uId"],
                        "password"=>$this->utils->encryptPassword($authModel["uSecret"])
                    ));
                    if($dbRes[0] == 1){
                        return array(
                            "success"=>true,
                            "errors"=>null,
                            "status_code"=>1,
                            "status_message"=>'Succesful.',
                            "message"=>"Signed In.",
                            "data"=>$dbRes[2]
                        );
                    }
                }
                array_push($this->errors, "Wrong details");
                return array(
                    "success"=>true,
                    "errors"=>$this->errors,
                    "status_code"=>0,
                    "status_message"=>'Failed.',
                    "message"=>"Sign in failed.",
                    "data"=>null
                );
            }
            return $validRes;
        }         
    }
?>