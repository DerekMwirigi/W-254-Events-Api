<?php 
    include 'firebase.php';
    class Payment extends DatabaseHandler {
        private $debug;
        private $smsconfig, $apiconfig, $firebase;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->firebase = new Firebase($this->debug);
            $this->apiconfig = json_decode(file_get_contents("../../config/api.config.json"), true);
            $this->smsconfig = json_decode(file_get_contents("../../config/sms.config.json"), true);
        }

        public function mpesaCallback ($callbackModel, $getModel){
            $this->utils->createFile(null, "callbackModel.json", $callbackModel);
            $getModel = json_decode($getModel["getData"], true);
            switch($callbackModel["Body"]["stkCallback"]["ResultCode"]){
                case 0:
                    $headers = array(
                        "Authorization:Bearer " . $getModel["token"]
                    );
                    $this->firebase->sendNotification("users", $getModel, $this->smsconfig["ticket"]["payment-success"]["text"], true);
                    $res = json_decode($this->utils->cURLRequest('POST', null, $this->apiconfig["base"] . $this->apiconfig["endpoints"]["ticket"]["buy"]["url"], $headers), true);
                break;
                default:
                    $this->firebase->sendNotification("users", $getModel, $this->smsconfig["ticket"]["payment-failuer"]["text"], true);
                break;
            }
            return array(1);
        }
    }
?>