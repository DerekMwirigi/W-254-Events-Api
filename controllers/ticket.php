<?php 
    class Ticket extends DatabaseHandler {
        private $debug;
        private $ticketModel;
        private $apiconfig;
        public $errors;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->errors = array();
            $this->ticketModel = json_decode(file_get_contents("../../models/ticket.json"), true);
            $this->apiconfig = json_decode(file_get_contents("../../config/api.config.json"), true);
        }

        public function create ($ticketModel, $userModel){
            $ticketModel["userId"] = $userModel["id"];
            $validRes = $this->utils->validateModel($ticketModel, $this->ticketModel["validModel"]);
            if($validRes["success"]){
                switch($ticketModel["billingTypeId"]){
                    case 1:
                        $stkModel = array(
                            "phone"=>$ticketModel["mpesaPhone"],
                            "amount"=>$ticketModel["amount"],
                            "token"=>$userModel["token"],
                            "appId"=>3
                        );
                        $serviceRes = json_decode($this->utils->cURLRequest("POST", $stkModel, $this->apiconfig["endpoints"]["mpesa"]["stkpush"]["url"]), true);
                        if(isset($serviceRes["ResponseCode"])){
                            switch(intval($serviceRes["ResponseCode"])){
                                case 0:
                                    $this->insert("checkouts", array(
                                        "userId"=>$userModel["id"],
                                        "eventId"=>$ticketModel["eventId"],
                                        "noTickets"=>$ticketModel["noTickets"],
                                        "billingTypeId"=>1,
                                        "billingAddress"=>array(
                                            "billingTypeId"=>$ticketModel["billingTypeId"],
                                            "mpesaPhone"=>$ticketModel["mpesaPhone"],
                                            "amount"=>$ticketModel["amount"]
                                        ),
                                        "createdOn"=>$this->dates->getDateTimeNow()
                                    ));
                                    return array(
                                        "success"=>true,
                                        "errors"=>null,
                                        "status_code"=>1,
                                        "status_message"=>'Success.',
                                        "message"=>$serviceRes["ResponseDescription"],
                                        "data"=>$serviceRes
                                    );
                                default:
                                    $this->errors = array($serviceRes["ResponseDescription"]);
                                    return array(
                                        "success"=>true,
                                        "errors"=>$this->errors,
                                        "status_code"=>0,
                                        "status_message"=>'Failed.',
                                        "message"=>$serviceRes["ResponseDescription"],
                                        "data"=>null
                                    );
                            }
                        }
                        $this->errors = array($serviceRes["errorMessage"]);
                        return array(
                            "success"=>true,
                            "errors"=>$this->errors,
                            "status_code"=>0,
                            "status_message"=>'Failed.',
                            "message"=>$serviceRes["errorMessage"],
                            "data"=>null
                        );
                           
                }
            }
            return $validRes;
        }

        public function buy ($userModel){
            $checkOutModel = $this->fetchRow(null, null, "SELECT * FROM checkouts WHERE userId = " . $userModel["id"] . " ORDER BY id DESC")[2];
            if(!empty($checkOutModel)){
                $this->insert("tickets", array(
                    "buyerId"=>$userModel["id"],
                    "eventId"=>$checkOutModel["eventId"],
                    "noTickets"=>$checkOutModel["noTickets"],
                    "paymentStatus"=>"Paid",
                    "createdOn"=>$this->dates->getDateTimeNow()
                ));
            }
        }

        public function fetch ($userModel){
            $searchModel = $this->ticketModel["getList"];
            $searchModel["keyModel"] = array(
                "buyerId"=>"=".$userModel["id"]
            );
            $dbRes = $this->search($searchModel);
            if($dbRes[0] == 1){
                return array(
                    "success"=>true,
                    "errors"=>null,
                    "status_code"=>1,
                    "status_message"=>'Succesful.',
                    "message"=>"Found " . count($dbRes[2]) . " tickets",
                    "data"=>$dbRes[2]
                );
            }
            array_push($this->errors, $dbRes[1]);
            return array(
                "success"=>true,
                "errors"=>$this->errors,
                "status_code"=>0,
                "status_message"=>'Failed.',
                "message"=>"No tickets found.",
                "data"=>null
            );
            
        }
    }
?>