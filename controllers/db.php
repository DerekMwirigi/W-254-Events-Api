<?php 
    include '../../servers/db-connection.php';
    include '../../utils/utils.php';
    class DatabaseHandler {
        protected $utils;
        protected $dates;
        protected $geo;
        protected $firebaseService;
        private $debug;

        protected $dbTables = array(
            "systemconfig",//0
            "users",//1
            "userroles",//2
            "events",//3
            "tickets",//4
            "locations",//5
            "planners",//6
            "categories",//7
            "checkouts"//8
        );
        
        protected $defaultOrderOption = "DESC";

        public function __construct($debug = NULL){
            $this->debug = $debug;
            $this->utils = new Utils();
            $this->geo = new Geo();
            $this->firebaseService = new FirebaseService();
            $this->dates = new Dates();
        }

        private function errorHandling ($db){
            $dbResModel = (json_decode(json_encode($db), true));
            $errorMessage = explode(' ', $db->errorMsg());
            switch($errorMessage[0]){
                case 'Duplicate':
                    return array(0, 'Seems like there is a similar record for ' . $errorMessage[count($errorMessage)-1]);
                default:
                    return array(0, $db->errorMsg());
            }
        }

        protected function insert ($entityName, $tableModel){
            try{
                unset($tableModel["id"]);
                if(!count($tableModel) > 0) { return array(0, 'Missing values'); }
                global $db;
                $db->debug = $this->debug;
                $db->AutoExecute($entityName, $this->utils->sanitizePair($tableModel), 'INSERT');
                if($db->errorMsg() == null){
                    return array(1, 'Inserted');
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function update ($entityName, $updateModel, $keyModel){
            try{
                if(empty($updateModel) || empty($keyModel)) { return array(0, 'Missing values'); }
                global $db;
                $db->debug = $this->debug;
                $sqlStatement = "";
                if(count($updateModel) > 0){
                    $currentItem = 0;
                    foreach($keyModel as $keyItem=>$valueItem)
                    {
                        $currentItem ++;
                        if($currentItem != count($keyModel) && count($keyModel) != 1){  $sqlStatement .= $keyItem . " = '". $valueItem . "' AND "; }else{  $sqlStatement .= $keyItem . " = '". $valueItem . "' "; }
                    }
                }
                $db->AutoExecute($entityName, $this->utils->sanitizePair($updateModel), 'UPDATE', $sqlStatement);
                if($db->errorMsg() == null){
                    return array(1, 'Row updated');
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function delete ($entityName, $keyModel){
            if(!count($keyModel) > 0) { return array(0, 'Missing values'); }
            try{
                global $db;
                $db->debug = $this->debug;
                $sqlStatement = "DELETE FROM $entityName WHERE ";
                if(count($keyModel) > 0){
                    $currentItem = 0;
                    foreach($keyModel as $keyItem=>$valueItem)
                    {
                        $currentItem ++;
                        if($currentItem != count($keyModel) && count($keyModel) != 1){  $sqlStatement .= $keyItem . " = '". $valueItem . "' AND "; }else{  $sqlStatement .= $keyItem . " = '". $valueItem . "' "; }
                    }
                }
                $db->GetOne($sqlStatement);
                if($db->errorMsg() == null){
                    return array(1, 'Row updated');
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function fetchItem ($entityName = NULL, $keyModel = NULL, $sqlStatement = NULL){
            try{
                global $db;
                $db->debug = $this->debug;
                if($entityName != NULL){
                    if($keyModel != NULL){
                        $sqlStatement = "SELECT * FROM " . $entityName . " WHERE ";
                        if(count($keyModel) > 0){
                            $currentItem = 0;
                            foreach($keyModel as $keyItem=>$valueItem)
                            {
                                $currentItem ++;
                                if($currentItem != count($keyModel) && count($keyModel) != 1){  $sqlStatement .= $keyItem . " = '". $valueItem . "' AND "; }else{  $sqlStatement .= $keyItem . " = '". $valueItem . "' "; }
                            }
                        }else {
                            $sqlStatement = "SELECT * FROM " . $entityName;
                        }
                    }
                    else{
                        $sqlStatement = "SELECT * FROM " . $entityName;
                    }
                }
                $resultData = $db->GetOne($sqlStatement);
                if($db->errorMsg() == null){
                    if(count($resultData) > 0){
                        return array(1, 'Found row.', $resultData);
                    }else{
                        return array(0, 'No items found.', null);
                    }
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function fetchRow ($entityName = NULL, $keyModel = NULL, $sqlStatement = NULL){
            try{
                global $db;
                $db->debug = $this->debug;
                if($entityName != NULL){
                    if($keyModel != NULL){
                        $sqlStatement = "SELECT * FROM " . $entityName . " WHERE ";
                        if(count($keyModel) > 0){
                            $currentItem = 0;
                            foreach($keyModel as $keyItem=>$valueItem)
                            {
                                $currentItem ++;
                                if($currentItem != count($keyModel) && count($keyModel) != 1){  $sqlStatement .= $keyItem . " = '". $valueItem . "' AND "; }else{  $sqlStatement .= $keyItem . " = '". $valueItem . "' "; }
                            }
                        }else {
                            $sqlStatement = "SELECT * FROM " . $entityName;
                        }
                    }
                    else{
                        $sqlStatement = "SELECT * FROM " . $entityName;
                    }
                }
                $resultData = $db->GetRow($sqlStatement);
                if($db->errorMsg() == null){
                    if(count($resultData) > 0){
                        return array(1, 'Found row.', $this->utils->desanitizePair($resultData));
                    }else{
                        return array(0, 'No items found.', null);
                    }
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function fetchRows ($entityName = NULL, $keyModel = NULL, $likeliHood = NULL, $sqlStatementIn = NULL, $pageNo = NULL, $perPage = NULL){
            try{
                global $db;
                $db->debug = $this->debug;
                if($entityName != NULL){
                    if($keyModel != NULL){
                        $sqlStatement = "SELECT * FROM " . $entityName . " WHERE ";
                        if(count($keyModel) > 0){
                            $currentItem = 0;
                            foreach($keyModel as $keyItem=>$valueItem)
                            {
                                $currentItem ++;
                                if($likeliHood == true){
                                    if($currentItem != count($keyModel) && count($keyModel) != 1){  $sqlStatement .= $keyItem . " LIKE ". $valueItem . ""; }else{  $sqlStatement .= $keyItem . " LIKE ". $valueItem . " "; }
                                }else{
                                    if($currentItem != count($keyModel) && count($keyModel) != 1){  $sqlStatement .= $keyItem . " = '". $valueItem . "' AND "; }else{  $sqlStatement .= $keyItem . " = '". $valueItem . "' "; }
                                }
                            }
                        }else {
                            $sqlStatement = "SELECT * FROM " . $entityName;
                        }
                    }
                    else{
                        $sqlStatement = "SELECT * FROM " . $entityName;
                    }
                    if($pageNo != NULL){
                        if($perPage != NULL){
                            $startPoint = ((($pageNo * $perPage) - $perPage));
                            $sqlStatement .= " LIMIT $startPoint, $perPage";
                        }else{
                            return array(0, 'No items found. An error in Statement', null);
                        }
                    }
                }
                if($sqlStatementIn != NULL){
                    $sqlStatement = $sqlStatementIn;
                }
                $resultData = $db->GetArray($sqlStatement);
                if($db->errorMsg() == null){
                    if(count($resultData) > 0){
                        $resData = array();
                        foreach($resultData as $row){
                            array_push($resData, $this->utils->desanitizePair($row));
                        }
                        return array(1, 'Found ' . count($resData) . ' rows.', $resData);
                    }else{
                        return array(0, 'No items found.', null);
                    }
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function executeStatement ($sqlStatement){
            try{
                global $db;
                $db->debug = $this->debug;
                $resultData = $db->GetArray($sqlStatement);
                //echo $sqlStatement;
                if($db->errorMsg() == null){
                    if(count($resultData) > 0){
                        $resData = array();
                        foreach($resultData as $row){
                            array_push($resData, $this->utils->desanitizePair($row));
                        }
                        return array(1, 'Found ' . count($resData) . ' rows.', $resData);
                    }else{
                        return array(0, 'No items found.', null);
                    }
                }else{
                    return $this->errorHandling($db);
                }
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        protected function search ($searchModel){
            $dbRes = $this->prepareJoinSQL($searchModel);
            if($dbRes[0] == 1){
                $sqlStatement = $dbRes[2];
                if(isset($searchModel["keyModel"])){
                    $sqlStatement .= $this->filterSQL($searchModel)[2];
                }
                $sqlStatement .= $this->orderBySQL($searchModel);
                $dbRes = $this->executeStatement($sqlStatement);
                if($dbRes[0] == 1){
                    if(isset($searchModel["associateModel"])){
                        return array(1, 'presenting rows...', $this->associate($dbRes[2], $searchModel["associateModel"]));
                    }
                    return $dbRes;
                }
                return $dbRes;
            } 
            return $dbRes;
        }

        private function associate ($rows, $associateModel){
            $rowsRes = array();
            foreach($rows as $row){
                foreach($associateModel["entities"] as $entity){
                    $keyModel = array(
                        $this->dbTables[$entity["entityType"]] . "." . $entity["refField"]=>"='" . $row[$entity["parentField"]] . "'"
                    );
                    $entity["keyModel"] = $keyModel;
                    $dbRes = $this->search($entity);
                    if($dbRes[0] == 1){
                        $row[$entity["entityName"]] = $dbRes[2];
                    }
                }
                array_push($rowsRes, $row);
            }
            return $rowsRes;
        }

        private function makePages ($pageCount, $pageNo){
            try{
                $xitRes = array();
                for($p = 1; $p <= $pageCount; $p ++){
                    if($p == $pageNo){
                        array_push($xitRes, array("page"=>$p, "state"=>true));
                    }else{
                        array_push($xitRes, array("page"=>$p, "state"=>false));
                    }
                }
                return $xitRes;
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        private function paginateSQL ($sqlStatement, $pageNo, $perPage){
            try{
                $startPoint = ((($pageNo * $perPage) - $perPage));
                $sqlStatement = " LIMIT $startPoint, $perPage";
                return array(1, '', $sqlStatement);
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        private function filterSQL ($searchModel){
            try{
                $sqlString = "";
                if($searchModel["keyModel"] != null){
                    $sqlString = " WHERE ";
                    foreach($searchModel["keyModel"] as $columnKey=>$columnValue){
                        $sqlString .= $columnKey . "" . $columnValue . "";
                    }
                }else{
                    return array(0, "Error, need for keyModel.", $sqlString);
                }
                return array(1, "SQL String prepared.", $sqlString);
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }

        private function orderBySQL ($searchModel){
            $sqlStatement = "";
            if(isset($searchModel["orderField"]) && $searchModel["orderField"] != null){ 
                $sqlStatement .= " ORDER BY " . $searchModel["orderField"];
                if(isset($searchModel["orderOption"]) && $searchModel["orderOption"] != null){ 
                    $sqlStatement .= " " . $searchModel["orderOption"];
                }else{
                    $sqlStatement .= " " . $this->defaultOrderOption;
                }
            }
            return $sqlStatement;
        }

        private function prepareJoinSQL ($searchModel){
            try{
                global $db;
                $db->debug = $this->debug;
                $sqlString = "SELECT ";
                if(isset($searchModel["columns"])){
                    $currentColumn = 0;
                    foreach($searchModel["columns"] as $columnKey=>$columnValue){
                        if(empty($columnValue) || !isset($columnValue)) { $columnValue_ = $columnKey; } else { $columnValue_ = $columnValue; }
                        if(($currentColumn + 1) == count($searchModel["columns"])){
                            $sqlString .= $this->dbTables[$searchModel["entityType"]] . "." . $columnKey . " AS " . $columnValue_;
                        }else{
                            $sqlString .= $this->dbTables[$searchModel["entityType"]] . "." . $columnKey . " AS " . $columnValue_ . ", ";
                        }
                        $currentColumn ++;
                    }
                }else{
                    return array(0, "Error, need for primary table fields setup.");
                }
                if(isset($searchModel["joinModel"]) && $searchModel["joinModel"] != null){
                    $sqlString .= ",";
                    $currentEntity = 0;
                    foreach($searchModel["joinModel"]["entities"] as $entity){
                        $currentEntityColumn = 0;
                        foreach($entity["columns"] as $entityColumnKey=>$entityColumnValue){
                            if(empty($entityColumnValue) || !isset($entityColumnValue)) { $entityColumnValue_ = $columnKey; } else { $entityColumnValue_ = $entityColumnValue; }
                            if(($currentEntityColumn + 1) == count($entity["columns"])){
                                $sqlString .= " " . $this->dbTables[$entity["entityType"]] . "." . $entityColumnKey . " AS " . $entityColumnValue_;
                            }else{
                                $sqlString .= " " . $this->dbTables[$entity["entityType"]] . "." . $entityColumnKey . " AS " . $entityColumnValue_ . ",";
                            }
                            $currentEntityColumn ++;
                        }
                        if(($currentEntity + 1) == count($searchModel["joinModel"]["entities"])){
                            $sqlString .= " ";
                        }else{
                            $sqlString .= ", ";
                        }
                        $currentEntity ++;
                    }
                    $sqlString .= " FROM " . $this->dbTables[$searchModel["entityType"]] . " ";
                    $currentEntityColumn = 0;
                    foreach($searchModel["joinModel"]["entities"] as $entity){
                        $sqlString .= " " . $entity["type"] . " JOIN " . $this->dbTables[$entity["entityType"]] . " ON " . $this->dbTables[$searchModel["entityType"]] . "." . $entity["parentField"] . " = " . $this->dbTables[$entity["entityType"]] . "." . $entity["joinfield"];
                        $currentEntityColumn ++;
                    }
                }else{
                    $sqlString .= " FROM " . $this->dbTables[$searchModel["entityType"]] . " ";
                }
                return array(1, "SQL String prepared.", $sqlString);
            }catch(Exception $e){
                return array(500, $e->getMessage());
            }
        }
        
        protected function formatSearchKeys ($keyModel){
            if(!count($keyModel) > 0) { return $keyModel; }
            $tempModel = array();
            foreach($keyModel as $key=>$value){
                if (strpos($value, '=') !== false) {
                    $tempModel[$key] = $value;
                }else{
                    $tempModel[$key] = "='" . $value . "'";
                }
            }
            return $tempModel;
        }
    }
?>