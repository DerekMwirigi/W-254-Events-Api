<?php 
    include 'db.php';
    
    class RequestHandler extends DatabaseHandler {
        private $debug;
        public $apiconfig;
        public $oauthToken_ = null;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->apiconfig = json_decode(file_get_contents("../../config/api.config.json"), true);
        }

        public function flagRequest ($server, $payLoad = null){
            $errors = array();
            $scriptDirectories = explode('/', $server["SCRIPT_NAME"]);
            if(isset($this->apiconfig["endpoints"][$scriptDirectories[count($scriptDirectories)-2]][basename("/" . $server["SCRIPT_NAME"], ".php")])){
                $apiModel = $this->apiconfig["endpoints"][$scriptDirectories[count($scriptDirectories)-2]][basename("/" . $server["SCRIPT_NAME"], ".php")];
                if($apiModel["method"] == $server["REQUEST_METHOD"]){
                    if(isset($apiModel["oauthToken"]) && $apiModel["oauthToken"] == 1){
                        $oauthToken = $this->getAauthToken($server);
                        if($oauthToken != null || !empty($oauthToken)){
                            $oauthToken = explode(" ", $oauthToken)[1];
                            $this->oauthToken_ = $oauthToken;
                            $dbRes = $this->verifyAauthToken($oauthToken);
                            if($dbRes[0] == 1){
                                $data = array(
                                    "id"=>$dbRes[2]["id"],
                                    "roleId"=>$dbRes[2]["roleId"],
                                    "token"=>$dbRes[2]["token"]
                                );
                                return array(
                                    "success"=>true,
                                    "errors"=>$errors,
                                    "status_code"=>1,
                                    "status_message"=>'Successful.',
                                    "message"=>"request flagged through.",
                                    "data"=>$data,
                                    "payLoad"=>$payLoad
                                );
                            }else{
                                array_push($errors, "Token is invalid or exipred.");
                                return array(
                                    "success"=>false,
                                    "errors"=>$errors,
                                    "status_code"=>0,
                                    "status_message"=>'Failed.',
                                    "message"=>"request not flagged through.",
                                    "data"=>null
                                );
                            }
                        }else{
                            array_push($errors, "Token is missing.");
                            return array(
                                "success"=>false,
                                "errors"=>$errors,
                                "status_code"=>0,
                                "status_message"=>'Failed.',
                                "message"=>"request not flagged through.",
                                "data"=>null
                            );
                        }
                    }else{
                        return array(
                            "success"=>true,
                            "errors"=>null,
                            "status_code"=>1,
                            "status_message"=>'Successful.',
                            "message"=>"request flagged through.",
                            "data"=>null,
                            "payLoad"=>$payLoad
                        );
                    }
                }else{
                    array_push($errors, "Bad request method.");
                    return array(
                        "success"=>false,
                        "errors"=>$errors,
                        "status_code"=>0,
                        "status_message"=>'Failed.',
                        "message"=>"request not flagged through.",
                        "data"=>null
                    );
                }
            }else{
                array_push($errors, "Bad request api not configured.");
                $reqRes = array(
                    "success"=>false,
                    "errors"=>$errors,
                    "status_code"=>0,
                    "status_message"=>'Failed.',
                    "message"=>"request not flagged through.",
                    "data"=>null
                );
            }
            return $reqRes;
        }

        private function getAauthToken($server){
            $oauthToken = null;
            if (isset($server['Authorization'])) {
                $oauthToken = trim($server["Authorization"]);
            }else if (isset($server['HTTP_AUTHORIZATION'])) { 
                $oauthToken = trim($server["HTTP_AUTHORIZATION"]);
            }elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if (isset($requestHeaders['Authorization'])) {
                    $oauthToken = trim($requestHeaders['Authorization']);
                }
            }
            return $oauthToken;
        } 

        private function verifyAauthToken ($oauthToken){
            foreach(array("users") as $accountType){
                $dbRes = $this->fetchRow($accountType, array("token"=>$oauthToken));
                if($dbRes[0] == 1 && count($dbRes[2]) > 0){
                    return $dbRes;
                }
            }
            return null;
        }
    }
?>