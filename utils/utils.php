<?php 
    class Utils { 
        private $debug;

        public function __construct($debug = NULL){
            $this->debug = $debug;
        }

        public function validateModel ($entityModel, $validModel){
            $errors = array();
            if(isset($validModel) && isset($entityModel)){
                foreach($validModel as $vKey=>$vValue){
                    $fAttrib = explode ("|", $vValue);
                    if(isset($entityModel[$vKey])){
                        if(!empty($fAttrib[1]) && strlen($entityModel[$vKey]) < $fAttrib[1]){
                            array_push($errors, $vKey . " is too short.");
                        }
                        if(!empty($fAttrib[2]) &&  strlen($entityModel[$vKey]) > $fAttrib[2]){
                            array_push($errors, $vKey . " is too long.");
                        }
                    }else{
                        array_push($errors, $vKey . " should not be blank.");
                    }
                }
                if(count($errors) > 0){
                    return array(
                        "success"=>false,
                        "status_code"=>0,
                        "status_message"=>'Failed.',
                        "errors"=>$errors,
                        "message"=>"Error in inputs."
                    );
                }else{
                    return array(
                        "success"=>true,
                        "status_code"=>1,
                        "status_message"=>'Suucess.',
                        "errors"=>null,
                        "message"=>"Valid inputs."
                    );
                }
            }else{
                array_push($errors, "Invalid inputs.");
                return array(
                    "success"=>false,
                    "status_code"=>0,
                    "status_message"=>'Failed.',
                    "errors"=>$errors,
                    "message"=>"Invalid inputs."
                );
            }
        }

        public function encryptPassword($password){
            return md5($password);
        }
        
        public function createToken (){
            $date = new Dates();
            return sha1(sha1(md5(uniqid()))) . $date->timeStamp();
        }

        public function sanitizePair ($pair){
            $model = array();
            foreach($pair as $key=>$value){
                if(is_array($value)) { $value_ = json_encode($value); }
                else if(is_object($value)) { $value_ = json_encode($value); }
                else { $value_ = $value; }
                $model[$key] = $value_;
            }
            return $model;
        }

        public function desanitizePair ($pair){
            $model = array();
            foreach($pair as $key=>$value){
                $value_ = json_decode($value, true);
                if($value_ == null || empty($value_)) { $value_ = $value; }
                $model[$key] = $value_;
            }
            return $model;
        }

        public function generateRandom($Start, $End, $Length){
            $Number = rand($Start, $End);
            if(strlen($Number) != $Length){
                $Number = $this->generateRandom($Start, $End, $Length); 
            }
            return $Number;
        }
        
        public function cURLRequest($requestMethod = NULL, $requestData = NULL, $requestEndpointURL = NULL, $requestHeaders = NULL){
            $ch = curl_init($requestEndpointURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            if(!empty($requestData)){
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
            }
        
            if (!empty($requestHeaders)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
            }
        
            $response = curl_exec($ch);
        
            if (curl_error($ch)) {
                //trigger_error('Curl Error:' . curl_error($ch));
                return json_encode(array(null, "0", curl_error($ch)));
            }

            if($this->debug){ echo 'Request Method: ' . $requestMethod; echo '<hr>'; echo 'Endpoint URL: ' . $requestEndpointURL; echo '<hr>'; echo 'Request Data: '; print_r($requestData); echo '<hr>';  echo 'Response Data: '; print_r($response); }

            curl_close($ch);
            return $response;
        }

        public function uniqueMultidimArray($array, $key) { 
            $temp_array = array(); 
            $i = 0; 
            $key_array = array(); 
            foreach($array as $val) { 
                if (!in_array($val[$key], $key_array)) { 
                    $key_array[$i] = $val[$key]; 
                    $temp_array[$i] = $val; 
                } 
                $i++; 
            } 
            return $temp_array; 
        } 

        public function createFile ($filePath = NULL, $fileName, $fileText, $encode = null){
            $myfile = fopen($fileName, "w") or die("Unable to open file!");
            fwrite($myfile, json_encode($fileText));
            fclose($myfile);
            if(!$encode || $encode == null){
                echo json_encode($fileText);
            }            
        }

        public function getImageDominatColor ($imageUrl){
            $i = imagecreatefromjpeg($imageUrl); 
            for ($x=0;$x<imagesx($i);$x++) { 
                for ($y=0;$y<imagesy($i);$y++) { 
                    $rgb = imagecolorat($i,$x,$y); 
                    $r = ($rgb >> 16) & 0xFF; 
                    $g = ($rgb >>  1)& 0xFF; 
                    $b = ($rgb )& 0xFF; 
                    $rTotal += $r; 
                    $gTotal += $g; 
                    $bTotal += $b; 
                    $total++; 
                } 
            } 
            $rAverage = round($rTotal/$total); 
            $gAverage = round($gTotal/$total); 
            $bAverage = round($bTotal/$total);
            return array(
                "rgb"=> $rAverage . '' . $gAverage. '' . $bAverage, 
                array("r"=>$rAverage, "g"=>$gAverage, "b"=>$bAverage)
            );
        }
        
        public function formatMoney($number, $fractional=false) { 
            if ($fractional) { 
                $number = sprintf('%.2f', $number); 
            } 
            while (true) { 
                $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 
                if ($replaced != $number) { 
                    $number = $replaced; 
                } else { 
                    break; 
                } 
            } 
            return $number; 
        }

        public function sortMultiDimensionalArray($data, $key, $order){
            foreach ($data as $keyX => $row) {
                $keyItem[$keyX]  = $row[$key];
            }
            $keyItem  = array_column($data, $key);
            switch($order){
                case 'SORT_ASC':
                    array_multisort($keyItem, SORT_ASC, $data);
                break;
                case 'SORT_DESC':
                    array_multisort($keyItem, SORT_DESC, $data);
                break;
                default:
                    array_multisort($keyItem, SORT_ASC, $data);
                break;
            }
            return $data;
        }
    }

    class Geo {
        private $utils;
        public function __construct($debug = NULL){
            $this->debug = $debug;
            $this->utils = new Utils($this->debug);
        }
        public function distanceBetweenPoint__($gpsPoints) {
            $reqRes = json_decode($this->utils->cURLRequest('GET', null, 'https://maps.googleapis.com/maps/api/directions/json?origin=-1.257352,36.6945518&destination=-1.28,36.82&sensor=false&waypoints=optimize:true%7C-1.249999,36.666664%7C-1.3166654,36.7833302&mode=driving&key=AIzaSyArhBu7Usq_ZoHsA4G-M5wjAl6pZZr8LKU'), true);
            $distance = 0.00;
            $duration = 0.00;
            foreach($reqRes["routes"]["0"]["legs"] as $leg){
               // echo json_encode($leg);
                $distance += floatval(explode(' ', $leg["distance"]["text"])[0]);
                $duration += floatval(explode(' ', $leg["duration"]["text"])[0]);
            }
        }

        public function distanceBetweenPoint($gpsPoints) {
            $lon1 = $gpsPoints["pointA"]["lon"];
            $lat1 = $gpsPoints["pointA"]["lat"];
            $lon2 = $gpsPoints["pointB"]["lon"];
            $lat2 = $gpsPoints["pointB"]["lat"];
            $theta = $lon1 - $lon2;
            $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
            $miles = acos($miles);
            $miles = rad2deg($miles);
            $miles = $miles * 60 * 1.1515;
            $feet = $miles * 5280;
            $yards = $feet / 3;
            $kilometers = $miles * 1.609344;
            $meters = $kilometers * 1000;
            return compact('miles','feet','yards','kilometers','meters'); 
        }

        public function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
            $theta = $lon1 - $lon2;
            $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
            $miles = acos($miles);
            $miles = rad2deg($miles);
            $miles = $miles * 60 * 1.1515;
            $feet = $miles * 5280;
            $yards = $feet / 3;
            $kilometers = $miles * 1.609344;
            $meters = $kilometers * 1000;
            return compact('miles','feet','yards','kilometers','meters'); 
        }
    }

    class Dates {
        private $timeZones = array("Africa/Nairobi");
        public function __construct($debug = NULL, $timeZoneIndex = NULL){
            $timeZoneIndex = 0;
            date_default_timezone_set($this->timeZones[$timeZoneIndex]);
        }

        public function getDateTimeNow(){
            return date('Y-m-d H:i:s');
        }

        public function timeStamp (){
            $date = date('Y-m-d H:i:s');
            $date = str_replace('-', '', $date);
            $date = str_replace(' ', '', $date);
            $date = str_replace(':', '', $date);
            return $date;
        }

        public function getDateTimeDiff ($laterDate, $earlierDate){
            $datetime1 = new DateTime($laterDate);
            $datetime2 = new DateTime($earlierDate);
            $interval = $datetime1->diff($datetime2);
            return $interval->format('%Y-%m-%d %H:%i:%s');
        }
    }
    
    class FirebaseService {
        private $debug;
        public function __construct($debug = NULL){
            $this->debug = $debug;
        }

        public function sendNotification ($headers, $payLoad){
            //Initializing curl to open a connection
            $ch = curl_init();
            //Setting the curl url
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            // curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            //setting the method as post
            curl_setopt($ch, CURLOPT_POST, true);
            //adding headers
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //disabling ssl support
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //adding the fields in json format
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payLoad));
            //finally executing the curl request
            $result = curl_exec($ch);
            curl_close($ch);
            if ($result === FALSE) {
                return 0;
            }else{
                return $result;
            }
        }
    }
?>