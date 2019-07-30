<?php 
    include 'sms.php';
    class Firebase extends DatabaseHandler {
        private $debug, $sms;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            parent::__construct($this->debug);
            $this->sms = new Sms($this->debug);
        }

        public function sendNotification ($accountType, $accountModel, $text, $wSms = null){
            $accountModel = $this->fetchRow($accountType, array("token"=>$accountModel["token"]))[2];
            $smsText = $text;
            $smsText = str_replace('{names}', $accountModel["firstName"], $smsText);
            $smsText = str_replace('{companyName}', 'W~254 Events', $smsText);
            $smsModels = array(
                array(
                    "subject"=>"Failed",
                    "smsText"=>$smsText,
                    "recipientNumber"=>$accountModel["mobile"]
                )
            );
            $headers = array(
                'Authorization: key=AAAAKyBHkws:APA91bFjla89202nQJ8oEYqq6qBvFLNaY2FCRSTslZw5TqPU4V0vyTtvr-8INbS0D_vKJY0QKl5A8bHgvpqki9i1qxZlvwVt9p1RM0Zssxf6pMSBR8-Ftvlg2wgaRj78S3wiiEV2X72R',
                'Content-Type: application/json'
            );
            $fnModel = array(
                "data"=>array(
                    "android_channel_id"=>354,
                    "title"=>"Failed",
                    "message"=>$smsText,
                    "image"=>null,
                    "code"=>0,
                    "status"=>0
                ),
                "notification"=>array(
                    "sound"=>"default"
                )
            );
            $payLoad = array(
                'registration_ids'=>array(
                    $accountModel["firebaseToken"]
                ),
                'data'=>$fnModel,
            );
            $this->firebaseService->sendNotification($headers, $payLoad);
            if($wSms){ $curlRes = $this->sms->expressSmss2s($smsModels); }
        }
    }
?>