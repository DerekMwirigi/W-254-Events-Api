<?php 
    include '../../controllers/request.php';
    $requestHandler = new RequestHandler();
    $reqRes = $requestHandler->flagRequest($_SERVER, json_decode(file_get_contents('php://input'), true));
    if($reqRes["success"]){
        include '../../controllers/payment.php';
        $payment = new Payment();
        $reqRes = $payment->mpesaCallback($reqRes["payLoad"], $_GET);
    }
    echo json_encode($reqRes);
?>