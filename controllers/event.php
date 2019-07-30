<?php 
    class Event extends DatabaseHandler {
        private $debug;
        private $eventModel;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->eventModel = json_decode(file_get_contents("../../models/event.json"), true);
        }

        public function fetch ($searchModel){
            if($searchModel == null) { $searchModel = $this->eventModel["getList"]; }
            $dbRes = $this->search($searchModel);
            if($dbRes[0] == 1){
                return array(
                    "success"=>true,
                    "errors"=>null,
                    "status_code"=>1,
                    "status_message"=>'Succesful.',
                    "message"=>"Found " . count($dbRes[2]) . " events",
                    "data"=>$dbRes[2]
                );
            }else{
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
        }

        public function categories ($searchModel){
            if($searchModel == null) { $searchModel = $this->eventModel["getListCategories"]; }
            $dbRes = $this->search($searchModel);
            if($dbRes[0] == 1){
                return array(
                    "success"=>true,
                    "errors"=>null,
                    "status_code"=>1,
                    "status_message"=>'Succesful.',
                    "message"=>"Found " . count($dbRes[2]) . " events",
                    "data"=>$dbRes[2]
                );
            }else{
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
        }
        
    }
?>