<?php 
    include '../../controllers/request.php';
    $requestHandler = new RequestHandler();
    $reqRes = $requestHandler->flagRequest($_SERVER, $_POST);
    if($reqRes["success"]){
        include '../../controllers/account.php';
        $account = new Account();
        $reqRes = $account->updateFirebaseToken($reqRes["payLoad"], $reqRes["data"]);
    }
    echo json_encode($reqRes);
?>