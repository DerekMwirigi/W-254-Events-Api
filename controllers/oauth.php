<?php 
    include 'sms.php';

    class Oauth extends DatabaseHandler {
        private $debug;
        private $sms;
        private $smsconfig;
        private $oauthModel;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->sms = new Sms($this->debug);
            $this->smsconfig = json_decode(file_get_contents("../../config/sms.config.json"), true);
            $this->oauthModel = json_decode(file_get_contents("../../models/oauth.json"), true);
        }

        public function signIn ($authModel){
            $errors = array();
            $validRes = $this->utils->validateModel($authModel, $this->oauthModel["validSignIn"]);
            if($validRes["success"]){
                $dbRes = $this->trySignIn($authModel["mobile"], $authModel["password"]);
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
                array_push($errors, $dbRes[1]);
                return array(
                    "success"=>true,
                    "errors"=>$errors,
                    "status_code"=>0,
                    "status_message"=>'Failed.',
                    "message"=>"Sign in failed.",
                    "data"=>null
                );
            }
            $errors = $validRes["errors"];
            return array(
                "success"=>true,
                "errors"=>$errors,
                "status_code"=>0,
                "status_message"=>'Failed.',
                "message"=>"Sign up failed.",
                "data"=>null
            );
        }

        public function signUp ($authModel){
            $errors = array();
            $validRes = $this->utils->validateModel($authModel, $this->oauthModel["validSignUp"]);
            if($validRes["success"]){
                $password = $authModel["password"];
                $authModel["code"] = $this->utils->generateRandom(1111, 9999, 4);
                $authModel["password"] = $this->utils->encryptPassword($password);
                $dbRes = $this->insert("users", $authModel);
                if($dbRes[0] == 1){
                    return array(
                        "success"=>true,
                        "errors"=>null,
                        "status_code"=>1,
                        "status_message"=>'Succesful.',
                        "message"=>"Sign up success",
                        "data"=>$this->signIn(array(
                            "email"=>$authModel["email"],
                            "password"=>$password
                        ))["data"]
                    );
                }
                array_push($errors, $dbRes[1]);
                return array(
                    "success"=>true,
                    "errors"=>$errors,
                    "status_code"=>0,
                    "status_message"=>'Failed.',
                    "message"=>"Sign up failed.",
                    "data"=>null
                );
            }
            $errors = $validRes["errors"];
            return array(
                "success"=>true,
                "errors"=>$errors,
                "status_code"=>0,
                "status_message"=>'Failed.',
                "message"=>"Sign up failed.",
                "data"=>null
            );
            
        }
        private function trySignIn ($email, $password){
            $accountTypes = array("users");
            $dbRes = array();
            foreach($accountTypes as $accountType){
                $dbRes = $this->fetchRow($accountType, array("email"=>$email, "password"=>$this->utils->encryptPassword($password)));
                if($dbRes[0] == 1){
                    break;
                }
            }
            return $dbRes;
        }         
    }
?>