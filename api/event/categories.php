<?php 
    include '../../controllers/request.php';
    $requestHandler = new RequestHandler();
    $reqRes = $requestHandler->flagRequest($_SERVER, json_decode(file_get_contents('php://input'), true));
    if($reqRes["success"]){
        include '../../controllers/event.php';
        $event = new Event();
        $reqRes = $event->categories($reqRes["data"]);
    }
    echo json_encode($reqRes);
?>