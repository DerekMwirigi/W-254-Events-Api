<?php 
    include '../../controllers/request.php';
    $requestHandler = new RequestHandler();
    $reqRes = $requestHandler->flagRequest($_SERVER, json_decode(file_get_contents('php://input'), true));
    if($reqRes["success"]){
        include '../../controllers/auth.php';
        $auth = new Auth();
        $reqRes = $auth->verifyUId($reqRes["payLoad"]);
    }
    echo json_encode($reqRes);
?>