<?php
    ini_set('display_errors', 1);
    ini_set('log_errors',true);

    @require_once('adodb/adodb.inc.php');
    @require_once('adodb/adodb-active-record.inc.php');

    $db_type  = 'mysqli';
    $db_host  = 'localhost:3309';
    $db_user  = 'root';
    $db_pass  = 'password';
    $db_name  = 'events';

    $db = ADONewConnection($db_type);

    if(! @$db->Connect($db_host,$db_user,$db_pass,$db_name)){
    echo("Could Not Connect to {$db_host}/{$db_name} ");
    }

    $db->SetFetchMode(ADODB_FETCH_ASSOC);

    ADODB_Active_Record::SetDatabaseAdapter( $db );
?>
