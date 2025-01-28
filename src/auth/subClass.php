<?php
set_time_limit(300);
include(__DIR__ .'/DataTableScada.php');

class SCADA extends DataTableScada
{
    public $gConn;
    public $MNEMO;
    public $RowCount;
    private $Meter;
    private $thead; 
    private $REF_TAG;
    private $gGroupId;
    private $gUserName;
    private $tableGMDR;
    private $tableName;
    private $dtResultGMDR;
    private $dtResultSCADA;
    private $drResultGMDR;
    private $TempGMDR = "";
    private $ColumnDisplay;
    private $drResultSCADA;
    private $TempSCADA = "";
    private $tagdata = array();
    private $newarr = array();
    private $newarrGMDR = array();
    private $GMDRHeader = array();
    public $ColumnVisibility = [];
    private $dbSource = 'pmisdwh-scan.pttplc.com:1521/PMISHS';
    private $dbUser = 'chonburi';
    private $dbPassword = 'chonburi';
    public static function OpenDebug(){
        error_reporting(-1);
        ini_set('display_errors', 'On');
      }
    public function openDB()
    {
        $this->gConn = oci_connect(
            $this->dbUser,
            $this->dbPassword,
            $this->dbSource //'(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST='.$dbSource.')(PORT=1521))(CONNECT_DATA=(SERVICE_NAME='.$dbName.')))'
        ) or die("Can't connect database");
    }
    private function closeDT($dtResult)
    {
        return oci_free_statement($this->dtResult);
    }
    private function closeDB()
    {
        return oci_close($this->gConn);
    }
    public function execu($sql)
    {
        SCADA::openDB();
        $tempstr = substr($sql, 0, 6);
        $checkDrop = strstr($sql, "drop");
        
        if ($this->gConn == "") {
            $this->dtResult = null;
            echo "Database connection hasn't been initialized";
        } else if ($tempstr == "SELECT" AND $checkDrop) {
            $this->dtResult = null;
            echo "Illegal DROP statement";
        } else {
            $this->dtResult = oci_parse($this->gConn, $sql);
            $oci_execute_result = oci_execute($this->dtResult);
            if (!$oci_execute_result) {
                $log_filepath = dirname($_SERVER['SCRIPT_FILENAME']) . '../../log/' . basename($_SERVER['SCRIPT_FILENAME']) . '.' . date('Y-m-d') . '.txt';
                file_put_contents($log_filepath, '[' . date('Y-m-d H:i:s') . '] ' . $sql . "\n", FILE_APPEND);
                header("Location: ../src/Index.php?missing=error");
            }
        }
        return $this->dtResult;
    }
    public function fetch_row($dtResult)
    {
        return oci_fetch_array($dtResult);
    }
    

    
    public function checkRolesDisplay()
    {
        $this->gUserName = $_SESSION["USER"];
        //$this->gGroupId = $_SESSION["GROUP"];
        $sql = "select * from SYS_USERS where USER_NAME = '$this->gUserName'";
        $dt = SCADA::execu($sql);
    
        $dr = SCADA::fetch_row($dt);
        //check with unit code
        //if ($_SESSION['USER'] === '') {
            //if (SCADA::login($username, $password)) {
                //if (SCADA::isPersonelUnderUnit($username, '80000512')) {
                    //$_SESSION['USER'] = $username;
                    //$_SESSION['GROUP'] = '2';
                //} else if (SCADA::isPersonelUnderUnit($username, '80000510')) {
                    //$_SESSION['USER'] = $username;
                    //$_SESSION['GROUP'] = '1';
                //} else if (substr($username, 0, 2) === "cg" || substr($username, 0, 2) === "ch") {
                    //echo $_SESSION['USER'] = $username;
                    //$_SESSION['GROUP'] = '1';
                //} else {
                    //header("Location: ../../Login.php?Insuff=Insuff");
                    //exit();
                //}
            //} else {
                //header("Location: ../../Login.php?Incor=Incor");
                //exit();
            //}
        //}
        if (!$dr) {
            // User not found in the database
            $this->closeDT($dt);
            return "error";
        }
    
        // If the user is found, process their group ID
        $this->groupId = $dr["GROUP_ID"];
        $this->closeDT($dt);
        $_SESSION["GROUP"] = $this->gGroupId;
    
        return $dr;
    }


    
    private function loadUserData($userName)
    {
        $sql = "select * from SYS_USERS where USER_NAME ='$userName'";
        $dt = SCADA::execu($sql);
        if ($dr = SCADA::fetch_row($dt)) {
            $this->groupId = $dr["GROUP_ID"];
        }
        $this->closeDT($dt);
        return ($this->groupId);
    }
    public function loadComboSCADA($sql, $fieldDesc, $fieldValue, $tempSelectVal)
    {
        $this->TempSCADA = "<option value='' selected>-- Choose a Template --</option>";
        $this->dtResultSCADA = SCADA::execu($sql);
        while ($this->drResultSCADA = SCADA::fetch_row($this->dtResultSCADA)) {
            $value = htmlspecialchars($this->drResultSCADA[$fieldValue]);
            $desc = htmlspecialchars($this->drResultSCADA[$fieldDesc]);
            $selected = ($value === $tempSelectVal) ? ' selected' : ''; 
            $this->TempSCADA .= "<option value='$value'$selected>$desc</option>";
        }
        SCADA::closeDT($this->dtResultSCADA);
        return ($this->TempSCADA);
    }
    public function loadComboNullSCADA($sql, $fieldDesc, $fieldValue, $tempSelectVal)
    {
        return ("<option value=''></option>" . SCADA::loadComboSCADA($sql, $fieldDesc, $fieldValue, $tempSelectVal));
    }
    
    public function loadComboNullSCADAGetTemplate($sql, $fieldDesc, $fieldValue, $tempSelectVal)
    {
        $valueArray = []; // Initialize an array to store the values
        $this->dtResultSCADA = SCADA::execu($sql); // Execute the query
        while ($this->drResultSCADA = SCADA::fetch_row($this->dtResultSCADA)) {
            $value = htmlspecialchars($this->drResultSCADA[$fieldValue]); // Get the value
            $desc = htmlspecialchars_decode(htmlspecialchars($this->drResultSCADA[$fieldDesc])); // Decode entities
            $owner = $this->drResultSCADA['TMPL_OWNER'];
            $tmplId = (int)$this->drResultSCADA['TMPL_ID']; // Get TMPL_ID as an integer
            
            // Add the value to the array with owner information and TMPL_ID as key for sorting
            $valueArray[$tmplId] = [
                'value' => $value,
                'desc' => $desc,
                'owner' => $owner
            ];
        }
        SCADA::closeDT($this->dtResultSCADA); // Close the database result
        // Sort the array by keys (TMPL_ID) in descending order
        krsort($valueArray, SORT_NUMERIC);
        // Reformat the array after sorting
        $sortedArray = [];
        foreach ($valueArray as $tmplId => $data) {
            $sortedArray[$data['value']] = $data['desc'] . "/" . $data['owner'];
        }
        return $sortedArray; // Return the sorted array
    }



    public function loadComboGMDR($sql, $fieldDesc, $fieldValue)
    {
        $this->TempGMDR = "<option value='' selected>-- Please choose a Meter Name --</option>";
        $this->TempGMDR .= "<option value='ALLTAG'>ALL TAG</option>";
        $this->dtResultGMDR = SCADA::execu($sql);
        while ($this->drResultGMDR = SCADA::fetch_row($this->dtResultGMDR)) {
            $value = htmlspecialchars($this->drResultGMDR[$fieldValue]);
            $desc = htmlspecialchars($this->drResultGMDR[$fieldDesc]);
            $this->TempGMDR .= "<option value='$value'>$desc</option>";
        }
        SCADA::closeDT($this->dtResultGMDR);
        return ($this->TempGMDR);
    }
    public function loadComboNullGMDR($sql, $fieldDesc, $fieldValue)
    {
        return ("<option value=''></option>" . SCADA::loadComboGMDR($sql, $fieldDesc, $fieldValue));
    }
    public function loadComboRTU($sql, $fieldDesc, $fieldValue)
    {
        
        $TempRTU = "<option value='' selected>-- Please choose RTU --</option>";
        $dtResultRTU = SCADA::execu($sql);
        $n = 0;
        while ($drResultRTU = SCADA::fetch_row($dtResultRTU)) {
          $TempRTU .= '<option value="' . htmlspecialchars($drResultRTU[$fieldValue]) . '">' . htmlspecialchars($drResultRTU[$fieldDesc]) . '</option>';
        }
        SCADA::closeDT($dtResultRTU);
        return ($TempRTU);
    }
    public function loadComboNullRTU($sql, $fieldDesc, $fieldValue)
    {
        return ("<option value=''></option>" . SCADA::loadComboRTU($sql, $fieldDesc, $fieldValue));
    }
    public function loadComboTagconfig($sql, $fieldDesc, $fieldValue)
    {
        $TempTagconfig = "<option value='' selected>-- Please choose an Meter Name --</option>";
        $dtResultTagconfig = SCADA::execu($sql);
        $n = 0;
        while ($drResultGMDR = SCADA::fetch_row($dtResultTagconfig)) {
          $TempTagconfig .= '<option value=' . htmlspecialchars($drResultGMDR["$fieldValue"]) . '>' . htmlspecialchars($drResultGMDR["$fieldDesc"]) . '</option>';
        }
        SCADA::closeDT($dtResultTagconfig);
        return ($TempTagconfig);
    }
    public function loadComboNullTagconfig($sql, $fieldDesc, $fieldValue)
    {
        return ("<option value=''></option>" . SCADA::loadComboTagconfig($sql, $fieldDesc, $fieldValue));
    }
    public static function CheckDurationMoreThan($start,$end){
        $start_date = new DateTime($start);
        $end_date = new DateTime($end);
        $interval = $start_date->diff($end_date);
        $days = $interval->days;
        if ($days <= 35) {  return true; } else { header("Location: ./Index.php?Morethan=35days"); }
    }
    public static function CheckDurationMoreThan1min($start,$end){
        $start_date = new DateTime($start);
        $end_date = new DateTime($end);
        $interval = $start_date->diff($end_date);
        $days = $interval->days;
        if ($days >= 3) {  return true; } else { return false; }
    }
    public function genTagList($tmplID)
    {
        SCADA::openDB();
        $valtmp = intval($tmplID);
        $resMeter = SCADA::execu("SELECT TEMPLATE_DETAILS.*,TYPE_VAL.*
                                  FROM TYPE_VAL
                                  INNER JOIN TEMPLATE_DETAILS
                                  ON TYPE_VAL.CODE_TYPE = TEMPLATE_DETAILS.CODE_TYPE
                                  WHERE TMPL_ID = $valtmp ORDER BY COL_ID");
        while ($rowMeter = SCADA::fetch_row($resMeter)) {
            $e = array();
            $e['TAGNAME'] = trim($rowMeter['TAGNAME']);
            $e['CODE_TYPE'] = trim($rowMeter['CODE_TYPE']);
            $e['TYPE_VALUE'] = trim($rowMeter['TYPE_VALUE']);
            $this->tagdata[] = $e;
        }
        return $this->tagdata;
    }


    public function login($username, $password)
    {
        $client = new SoapClient("http://plwebapp2.ptt.corp/ADHelper/ws.asmx?WSDL");
        $params = array(
            'username' => $username,
            'password' => $password
        );
        $result = $client->__soapCall("login", array('parameters' => $params));
        return $result->loginResult;
    }

    public function isPersonelUnderUnit($username, $unitcode)
    {
        $params = array(
            'fn' => 'isPersonelUnderUnit',
            'code' => $username,
            'unitcode' => $unitcode
        );
        $json = file_get_contents('http://plwebapp2.ptt.corp/ADHelper/php/json.php?' . http_build_query($params));
        return json_decode($json);
    }
    public function CheckLogin($username, $password)
    {
        SCADA::openDB();
        $_SESSION['USER'] = '';
        $_SESSION['GROUP'] = '';
        $sql = "SELECT * FROM SYS_USERS WHERE USER_NAME = :bv_username";
        $stid = oci_parse($this->gConn, $sql);
        oci_bind_by_name($stid, ":bv_username", $username);
        oci_execute($stid);
        if ($row = oci_fetch_assoc($stid)) {
            if (md5($password) == $row['PASSWORD'] || SCADA::login($username, $password)) {
                $_SESSION['USER'] = $row['USER_NAME'];
                $_SESSION['GROUP'] = $row['GROUP_ID'];
            }
        }
         if ($_SESSION['USER'] === '') {
            if (SCADA::login($username, $password)) {
                if (SCADA::isPersonelUnderUnit($username, '80000512')) {
                    $_SESSION['USER'] = $username;
                    $_SESSION['GROUP'] = '2';
                } else if (SCADA::isPersonelUnderUnit($username, '80000510')) {
                    $_SESSION['USER'] = $username;
                    $_SESSION['GROUP'] = '1';
                } else if (substr($username, 0, 2) === "cg" || substr($username, 0, 2) === "ch") {
                    echo $_SESSION['USER'] = $username;
                    $_SESSION['GROUP'] = '1';
                } else {
                    header("Location: ../../Login.php?Insuff=Insuff");
                    exit();
                }
            } else {
                header("Location: ../../Login.php?Incor=Incor");
                exit();
            }
        }
        $sql = "INSERT INTO LOGIN_USER_LOG (USER_NAME) VALUES (:bv_username)";
        $stid = oci_parse($this->gConn, $sql);
        oci_bind_by_name($stid, ":bv_username", $username);
        oci_execute($stid);
        header("Location: ../Index.php");
    }
    public function getMNEMO($cboTemplate, $TagRTU, $chtag = null) {
    {
        if($chtag === 'Add' || $cboTemplate === '') {
            $e = 1;
            foreach($TagRTU as $key => $value) {
                if($key === $key){
                    $splitkey = explode("/", $key);               
                    $this->MNEMO .= "($this->tableName.MNEMO = '" . $splitkey[0] . "' AND TYPE_VAL.TYPE_VALUE = '" . trim($value) . "')";
                    if ($e < count($TagRTU))
                        $this->MNEMO .= " OR ";
                    $e++;
                }
            }
            return $this->MNEMO; 
        }else if($chtag === '' && $cboTemplate !== ''){   
            $n = 1;
            $GetTag = SCADA::genTagList($cboTemplate);
            foreach ($GetTag as $key => $value) {
                $this->MNEMO .= "($this->tableName.MNEMO = '" . $value['TAGNAME'] . "' AND $this->tableName.CODE_TYPE = " . $value['CODE_TYPE'] . ")";
                if ($n < count($GetTag))
                    $this->MNEMO .= " OR ";
                $n++;
            }
            return $this->MNEMO;  
        }
    }
    }
    public function TABLE_SCADA(
        $period = null,$FromdateXFromtime = null,$TodateXToTime = null,$cboTemplate,
        $iDisplay = null,$iCodition = null,$TagRTU = null, $chtag = null
    ) {
        switch (trim($period)) {
            case "1 Minute":
                $this->tableName = "ARCH_1MN";
                break;
            case "10 Minute":
                $this->tableName = "ARCH_10MN";
                break;
            case "Hour":
                $this->tableName = "ARCH_HOUR";
                break;
            case "Day":
                $this->tableName = "ARCH_DAY";
                break;
        }
        
        $sqlORA = 'SELECT ' . $this->tableName . '.*,TYPE_VAL.*,DUMP_UNIT_HIST.* 
        FROM ' . $this->tableName . ' 
        INNER JOIN TYPE_VAL
        ON ' . $this->tableName . '.CODE_TYPE = TYPE_VAL.CODE_TYPE
        INNER JOIN DUMP_UNIT_HIST 
        ON ' . $this->tableName . '.MNEMO = DUMP_UNIT_HIST.TAGNAME
        WHERE (' . SCADA::getMNEMO($cboTemplate,$this->ConvertData($TagRTU),$chtag) . ') AND 
        ("DATE" BETWEEN TO_TIMESTAMP(' . "'$FromdateXFromtime'" . ', ' . "'YYYY-MM-DD HH24:MI'" . ') AND 
        TO_TIMESTAMP(' . "'$TodateXToTime'" . ', ' . "'YYYY-MM-DD HH24:MI'" . '))';
        if(SCADA::GetHeaderLook(SCADA::ConvertData($TagRTU)) == true ){
            return SCADA::getLookTag($TagRTU,SCADA::execu($sqlORA),$iDisplay);
        }
        
    }
    private function GetHeaderLook($arr){
        $e = 1;
        $str = "";
        foreach($arr as $key => $value) {
            if($key === $key){
                $splitkey = explode("/", $key);
                $tag[] = $splitkey[0];               
                    $str .= "(TAGNAME = '" . $splitkey[0] . "')";
                if ($e < count($arr)) $str .= " OR ";
                $e++;
            }
        }
        $sql = SCADA::execu("SELECT * FROM DUMP_UNIT_HIST
        WHERE $str");
        while ($row = SCADA::fetch_row($sql)) { 
            $this->thead[$row['TAGNAME']] = $row['DESCRIPTION'] ." => ". $row['UNIT']; 
        }
        foreach($tag as $keyTag => $valueTag) {
            if(!isset($this->thead[$valueTag])) 
                $this->thead[$valueTag] = '<a style="color:#FF8C00;">No Description</a>'." => ";
        }
        return true;
    }
    private static function ConvertData($string) {
        // Remove HTML tags and unnecessary whitespace
        $str = trim(preg_replace('/<br\s*\/?>/', ' ', $string));
        // Break the string into individual elements by spaces
        $array = explode(" ", $str);
        $new_array = array();
        if (is_array($array) && !empty($array)) {
            for ($i = 0; $i < count($array); $i += 2) {
                if (isset($array[$i]) && isset($array[$i + 1])) { // Check both key and value
                    $key = $array[$i];
                    $value = str_replace(["(", ")"], "", $array[$i + 1]);
                    // Construct key-value pair
                    $new_array[$key . "/" . $value] = $value;
                }
            }
            return $new_array;
        }
    
        return []; // Return an empty array if input is invalid
    }



    private function getLookTag($string,$data,$iDisplay){  
        $null = array();    $raw = array();
        $arr = SCADA::ConvertData($string);       
        while ($row = SCADA::fetch_row($data)) {
            $key = $row['MNEMO']." => ".trim($row['TYPE_VALUE']);
            if (!isset($raw[$key]))  $raw[$key] = []; 
            $raw[$key][self::DateFormat($row['DATE'])] = $row['VALUE'];
        }
        foreach($raw as $key1 => $value1){ 
            foreach($value1 as $k => $v){ 
                $null[$k] = null; 
            } 
        }
        foreach($arr as $tag => $val){
            $splt = explode("/",$tag);
            $keystr = trim($splt[0])." => ".trim($splt[1]);
            if(isset($raw[$keystr]))
                $this->newarr[$keystr] = $raw[$keystr];
            else
                $this->newarr[$keystr] = $null;
        }
        $table = "<thead><tr><th id='first'>DATE TIME</th>";
        // Sort each array in $this->newarr by time
        foreach ($this->newarr as $tag => &$array) {
            ksort($array); // Sorts the array by its keys (time)
        }
        unset($array); // Break the reference
        foreach ($this->newarr as $tag => $array) {
            $spl = explode(" => ", $tag);
        
            foreach ($this->thead as $key => $value) {
                $val = explode(" => ", $value);
        
                if ($spl[0] == $key) {
                    $id = htmlspecialchars($spl[0]) . '-HEADER';
                    $commonHtml = "<th id='$id'>" . $spl[0] . "</br>" . $val[1] . "<a style='color:#1E90FF;'> (" . $spl[1] . ")</a>";
        
                    switch ($iDisplay) {
                        case "Tagname":
                            $table .= $commonHtml . "</th>";
                            break;
        
                        case "Description":
                            $table .= "<th ids='" . htmlspecialchars($val[0]) . "-HEADER'>" . $val[0] . "</br>" . $val[1] . "<a style='color:#1E90FF;'> (" . $spl[1] . ")</a></th>";
                            break;
        
                        case "Tagnameanddescription":
                            $table .= $commonHtml . "</br>" . $val[0] . "</th>";
                            break;
                    }
                }
            }
        }
        $table .= "</tr></thead><tbody id='rowdata'>".SCADA::CreateTbody($this->newarr)."</tbody>";
        return $table;
    }
    
    private function CheckEmptyArray($arr)
    {
        foreach ($arr as $key => $value) {
          foreach ($value as $nestedKey => $nestedValue) {
              if (is_array($nestedValue) && empty($nestedValue)) { return 1; } else { return 0; }
          }
      }
    }
    
    private function CheckEmptyArrayScada($data){ foreach ($data as $key => $value) { if (is_array($value) && empty($value)) { return 1; }else{ return 0;} } }
    
    private function CreateTbody($arr)
    {
        $tr = "";
        $r = 0;
        if(SCADA::CheckEmptyArrayScada($arr) === 1){
          return "<tr><td colspan='" . (count($arr)+1) . "'>No data in table.</td></tr>";
        }else{
          // Retrieve all datetime keys from $arr to create rows
          $datetimeKeys = array_keys(reset($arr));
  
          foreach ($datetimeKeys as $datetime) {
              $tr .= "<tr role='row' id='" . ++$r . "'><td>" . htmlspecialchars($datetime) . "</td>";
      
              // Loop through each column's data
              foreach ($arr as $keyCol => $valueCol) {
                  $keyTag = explode(" => ", $keyCol)[0];
                  // Add key tag to the array only if it doesn't exist
                  if (!in_array($keyTag, $this->ColumnVisibility)) {
                      $this->ColumnVisibility[] = $keyTag;
                  }
                  $value = $valueCol[$datetime] ?? 0;
      
               // Check if the value is numeric and format it
                if (is_numeric($value)) {
                    // Round the value and convert to an integer if it has no decimal part
                    $formattedValue = (float)$value == round($value) ? (int)$value : (float)$value;
                    // Use the formatted value for display
                    $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>" . htmlspecialchars($formattedValue) . "</td>";
                } else {
                    // If value is null or '0', display 0
                    $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>0</td>";
                }
              }
              $tr .= "</tr>";
          }
      
          $this->RowCount = $r;
        return $tr;
        }
    }



    private function SETDUPLICATE($e)
    {
        return array_unique($e);
    }
    
    private function getTdDuplicate($operat)
    {
        $tr = '';
        static $callCount = 0; 
        $callCount++;
        if ($operat == true) {
            foreach ($this->GMDRHeader as $keyCol => $valueCol) {
                $tr .= "<td style='background-color:#E74C3C;'></td>";
            }
        } else {
            foreach ($this->GMDRHeader as $keyCol => $valueCol) {
                $tr .= "<td></td>";
            }
        }
        return $tr;
    }
    
    private  function CreateTbodyGMDR($arr, $Meter)
    {
        $tr = "";
        $n = 0;
        $duplicates = array();
        // Remove HTML tags
        $cleanText = strip_tags($Meter);
        // Trim whitespace
        $cleanText = trim($cleanText);
        if ($cleanText === 'ALLTAG') {
            foreach ($arr as $key => $value) {
                foreach ($value as $keyCol => $valueCol) {
                    if ($n++ === 1) { // Assuming $n is defined elsewhere in your code
                        foreach ($valueCol as $datetime => $valuex) {
                            foreach ($arr as $key1 => $value1) {
                                foreach ($value1 as $tag => $valueGMDR) {
                                    foreach ($valueGMDR as $date => $valueGMDR) {
                                        if ($date === $datetime) {
                                            // Check if valueGMDR is not null or zero
                                            if ($valueGMDR !== null && $valueGMDR != 0) {
                                                $duplicates[$datetime][] = $valueGMDR; // Collect duplicates
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // Check if $duplicates is not an array or is an empty array
                        if (!is_array($duplicates) || empty($duplicates)) {
                            // Handle case when duplicates is not an array or empty
                            $tr .= "<tr><td colspan='" . count($this->GMDRHeader) . "'>No data in table.</td></tr>"; // Adjust colspan as needed
                        } else {
                            // Flatten the array to find duplicate values
                            $values = array_map(function($item) {
                                return $item[0];
                            }, $duplicates);
                            
                            // Find duplicate values
                            $duplicatesCheck = array_filter(array_count_values($values), function($count) {
                                return $count > 1;
                            });
                            
                            // Generate table rows
                            foreach ($duplicates as $date => $val) {
                                $value = $val[0];
                                $isDuplicate = array_key_exists($value, $duplicatesCheck);
                                
                                $tr .= "<tr><td>" . htmlspecialchars($date) . "</td>"; // Escape for safety
                                $tr .= $this->getTdDuplicate($isDuplicate); // Call method with duplicate check
                                $tr .= "</tr>"; // Close the row here
                            }
                        }
                    }
                }
                
            }
        }else {
            if (!is_array($arr) || empty($arr) || !array_filter($arr) || SCADA::CheckEmptyArray($arr) === 1) {
                   return "<tbody><tr><td colspan='" . (count($this->GMDRHeader) + 1) . "'>No data in table.</td></tr></tbody>";
            } else {
              foreach ($arr as $key => $value) {
                  foreach ($value as $keyCol => $valueCol) {
                      if ($n++ == 1) {
                          foreach ($valueCol as $datetime => $valuex) {
                          
                              $tr .= "<tr><td>" . $datetime . "</td>";
                              foreach ($arr as $key1 => $value1) {
                                  foreach ($value1 as $tag => $value_GMDR) {
                                    if(isset($value_GMDR[$datetime])){
                                       foreach ($value_GMDR as $date => $valueGMDR) {
                                          if ($date === $datetime) {
                                              $tr .= "<td>" . $valueGMDR . "</td>";
                                          }
                                      }
                                    }else{
                                      $tr .= "<td style='background-color:#E74C3C;color:#fff;'>N/A</td>";
                                    }
                                  }
                              }
                              $tr .= "</tr>";
                          }
                      }
                  }
               }
            }
            $this->getFooter();
        }

        return $tr;
    }

    private function ConvertFloatingPoint($val = null, $tag = null)
    {
        $col = $this->GMDRHeader;
        for ($b = 0; $b < count($col); $b++) {
            if ($tag === $col[$b]['MNEMO']) {
                $value = number_format(floatval($val), $col[$b]['PRECISION'], '.', '');
            }
        }
        return $value;
    }


    private function GetHeaderGMDR($Meter, $REF)
    {
        // Remove HTML tags
        $cleanText = strip_tags($Meter);
        // Optionally, you can trim the text to remove any leading or trailing whitespace
        $cleanText = trim($cleanText);
        if ($cleanText == 'ALLTAG') {
            $reqheader = SCADA::execu("SELECT * FROM $this->REF_TAG WHERE MONITOR_FLAG = 1");
        } else {
            $reqheader = SCADA::execu('SELECT * FROM ' . $REF . ' WHERE FC_NAME = ' . "'$Meter'" . '');
        }
        while ($row = SCADA::fetch_row($reqheader)) {
            $e = array();
            if ($cleanText == 'ALLTAG') {
                $e['TAG_HEADER'] = trim($row['FC_NAME']) . "<BR><p class=thder >" . trim($row['TAG_HEADER']) . "</p>";
            } else {
                $e['TAG_HEADER'] = trim($row['TAG_HEADER']);
            }
            $e['MNEMO'] = trim($row['TAGNAME']);
            $e['DESCRIPTION'] = trim($row['DESCRIPTION']);
            $e['PRECISION'] = trim($row['PRECISION']);
            $e['SORT_ORDER'] = trim($row['SORT_ORDER']);
            $this->GMDRHeader[] = $e;
        }
        function SortHeaderGMDRCallBack($a, $b)
        {
            if (isset($a) && isset($b) &&
                $a !== null && $b !== null
            ) return intval($a["SORT_ORDER"]) - intval($b["SORT_ORDER"]);
        }
        if (isset($this->GMDRHeader) && $this->GMDRHeader !== null) usort($this->GMDRHeader, "SortHeaderGMDRCallBack");
        return ($this->GMDRHeader);
    }
    private static function DateFormat($date)
    {
        $date = DateTime::createFromFormat('d-M-y h.i.s.u A', $date);
        return $date->format('d-m-y') . ' ' . date("H:i", strtotime($date->format('h.i A')));
    }
    public function TABLE_GMDR(
        $table = null,
        $Meter = null,
        $FromtimeGMDR = null,
        $ToTimeGMDR = null
    ) {
        // Remove HTML tags
        $cleanText = strip_tags($Meter);
        
        // Optionally, you can trim the text to remove any leading or trailing whitespace
        $cleanText = trim($cleanText);

        $trimMeter = $cleanText;
        $sqlORA = '';
        switch ($table) {
            case "Hour":
                $this->tableGMDR = 'HOURLY_GMDR';
                $this->REF_TAG = 'REF_HOURLY_GMDR_TAG';
                break;
            case "Day";
                $this->tableGMDR = 'DAILY_GMDR';
                $this->REF_TAG = 'REF_DAILY_GMDR_TAG';
                break;
        }
        $this->Meter = $trimMeter;
        if ($trimMeter == 'ALLTAG') {
            $sqlORA = '
                SELECT ' . $this->tableGMDR . '.*
                FROM ' . $this->tableGMDR . '
                WHERE ("DATE" BETWEEN TO_DATE(' . "'$FromtimeGMDR'" . ', ' . "'YYYY-MM-DD HH24:MI'" . ') 
                AND TO_DATE(' . "'$ToTimeGMDR'" . ', ' . "'YYYY-MM-DD HH24:MI'" . ')) 
                AND (MNEMO in (SELECT TAGNAME FROM ' . $this->REF_TAG . ' WHERE MONITOR_FLAG = 1)) ORDER BY "DATE"';
        } else {
            
            $sqlORA = '
                SELECT ' . $this->tableGMDR . '.*
                FROM ' . $this->tableGMDR . '
                WHERE ' . $this->tableGMDR . '.FC_NAME = ' . "'$trimMeter'" . '
                AND ("DATE" BETWEEN TO_DATE(' . "'$FromtimeGMDR'" . ', ' . "'YYYY-MM-DD HH24:MI'" . ') 
                AND TO_DATE(' . "'$ToTimeGMDR'" . ', ' . "'YYYY-MM-DD HH24:MI'" . '))';
        }
        $getHeaderdata = SCADA::GetHeaderGMDR($trimMeter, $this->REF_TAG);
        $getBodydata = SCADA::CreateTableGMDR(SCADA::execu($sqlORA), $trimMeter);
        $Table = "
                    <thead>
                        <tr>
                            <th id='first'>DATE TIME</th>";
        for ($i = 0; $i < count($getHeaderdata); ++$i) {
            $Table .= "<th >" . $getHeaderdata[$i]['TAG_HEADER'] . "</th>";
        }
        $Table .= "</tr></thead><tbody>$getBodydata</tbody>";
        return $Table;
    }
    
    private function CreateTableGMDR($res, $Meter)
    {
        $raw_data = array();
        $arr = array();
        $n = 0;
        $col = $this->GMDRHeader;
    
        function SortGMDRCallBack($a, $b)
        {
            if (isset($a) && isset($b) &&
                $a !== null && $b !== null
            ) return intval($a["SORT_ORDER"]) - intval($b["SORT_ORDER"]);
        }
    
        if (isset($col) && $col !== null) usort($col, "SortGMDRCallBack");
    
        // Fetch rows and store data by MNEMO and DATE
        while ($row = SCADA::fetch_row($res)) {
            if (!isset($raw_data[$row['MNEMO']])) $raw_data[$row['MNEMO']] = [];
            $raw_data[$row['MNEMO']][self::DateFormat($row['DATE'])] = $this->ConvertFloatingPoint($row['VALUE'], $row['MNEMO']);
        }
    
        // Sort each inner array by date keys
        foreach ($raw_data as $k => &$v) {
            // Sort keys of the inner array
            uksort($v, function($a, $b) {
                $dateA = strtotime($a);
                $dateB = strtotime($b);
                return $dateA - $dateB; // Sort in ascending order
            });
        }
    
        // Initialize the first row of the result array
        foreach ($raw_data as $k => $v) {
            if ($n++ == 1) {
                foreach ($v as $k2 => $v2) {
                    $arr[$k2] = null;
                }
            }
        }
    
        // Fill newarrGMDR with sorted data
        for ($u = 0; $u < count($col); $u++) {
            if (isset($raw_data[$col[$u]['MNEMO']])) {
                $this->newarrGMDR[$col[$u]['SORT_ORDER']][$col[$u]['MNEMO']] = $raw_data[$col[$u]['MNEMO']];
            } else {
                $this->newarrGMDR[$col[$u]['SORT_ORDER']][$col[$u]['MNEMO']] = $arr;
            }
        }
        return $this->CreateTbodyGMDR($this->newarrGMDR, $Meter);
    }

    
    private static function CheckdataNull($arr){
        foreach ($arr as $key => $value) {
           if(empty($value)){
                return false;   
           }else{
                return true;
           }
        }
    }
    public function getFooter()
    {
        $tr = "";
        if (self::CheckdataNull($this->newarr) === true) {
            $tr .= "<div id='foot'>
                <tr id='mma'>
                <td>MIN</td>";
            foreach ($this->newarr as $Minkey => $Minvalue) {
              $keyTag = explode(" => ", $Minkey)[0];
              if (is_numeric(min($Minvalue))) {
                $formattedValue = (float)min($Minvalue) == round(min($Minvalue)) ? (int)min($Minvalue) : (float)min($Minvalue);
                $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>" . $formattedValue . "</td>";
              }else{
                $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>" . 0 . "</td>";
              }
            }
            $tr .= "</tr>";
            $tr .= "<tr id='mma'>
            <td>MAX</td>";
            foreach ($this->newarr as $Maxkey => $Maxvalue) {
            $keyTag = explode(" => ", $Maxkey)[0];
              if (is_numeric(max($Maxvalue))) {
                $formattedValue = (float)max($Maxvalue) == round(min($Maxvalue)) ? (int)max($Maxvalue) : (float)max($Maxvalue);
                $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>" . $formattedValue . "</td>";
              }else{
                $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>" . 0 . "</td>";
              }
            }
            $tr .= "</tr>";
            $tr .= "<tr id='mma'>
                        <td>AVG</td>";
            foreach ($this->newarr as $Avgkey => $Avgvalue) {
            $keyTag = explode(" => ", $Avgkey)[0];
                $tr .= "<td class='" . htmlspecialchars($keyTag) . "'>" . array_sum($Avgvalue) / count($Avgvalue) . "</td>";
            }
            $tr .= "</tr>
            </div>";
            return $tr;
        }
        if ($this->newarrGMDR !== null && $this->Meter != 'ALLTAG') {
            $tr .= "<div id='foot'>
            <tr id='mma'>
                    <td>MAX</td>";
            foreach ($this->newarrGMDR as $Maxkey => $Maxvalue) {
                foreach ($Maxvalue as $Maxtag => $MaxtagValue) {
                    if (!empty($MaxtagValue)) $tr .= "<td>" . max($MaxtagValue) . "</td>";
                }
            }
            $tr .= "</tr>";
            $tr .= "<tr id='mma'>
                        <td>MIN</td>";
            foreach ($this->newarrGMDR as $Minkey => $Minvalue) {
                foreach ($Minvalue as $Mintag => $MintagValue) {
                    if (!empty($MintagValue)) $tr .= "<td>" . min($MintagValue) . "</td>";
                }
            }
            $tr .= "</tr>";
            $tr .= "<tr id='mma'>
                        <td>AVG</td>";
            foreach ($this->newarrGMDR as $Avgkey => $Avgvalue) {
                foreach ($Avgvalue as $Avgtag => $AvgtagValue) {
                    if (!empty($AvgtagValue)) $tr .= "<td>" . array_sum($AvgtagValue) / count($AvgtagValue) . "</td>";
                }
            }
            $tr .= "</tr></div>";
            if (
                !empty($MaxtagValue) &&
                !empty($MaxtagValue) &&
                !empty($AvgtagValue)
            )
                return $tr;
        }
    }

    public function SetTextField($array){
        $decode_array = json_decode($array,true);
        $tag = '';
        foreach($decode_array as $key => $value){	
            foreach($value as $tagname => $checked){
                if($checked === 'Checked'){
                    if($key === 'CurrentTag'){
                        $tag .= $tagname." (Current)".'<br />';
                    }
                    if($key === 'AverageTag'){
                        $tag .= $tagname." (Average)".'<br />';
                    }
                    if($key === 'MinTag'){
                        $tag .= $tagname." (Min)".'<br />';
                    }
                    if($key === 'MaxTag'){
                        $tag .= $tagname." (Max)".'<br />';
                    }
                    if($key === 'DiffIndexTag'){
                        $tag .= $tagname." (Diff&nbspIndex)".'<br />';
                    }
                    if($key === 'NotusedTag'){
                        $tag .= $tagname." (Not&nbspUsed)".'<br />';
                    }
                    if($key === 'IntegratedTag'){
                        $tag .= $tagname." (Integrated)".'<br />';
                    }
                }
            }
        }
        return nl2br($tag);
    }
public function SaveNewTamplate($name, $tag) {
    $name = trim($name);
    $code_type = '';
    $n = 1;
    $owner = $_SESSION['USER'];
    $arrtag = $tag;
    // Use regex to match tags with their statuses
    preg_match_all('/(\S+)\s+\((\S+)\)/', $arrtag, $matches);
    // Prepare the new array
    $new_array = array();
    if (count($matches[0]) > 0) {
        // Loop through the matches to construct the new array
        foreach ($matches[1] as $index => $value) {
            $key = $matches[2][$index]; // Current, Average, etc.
            // Construct the new key-value pair as per the desired format
            $new_array[$value . "/" . $key] = $value;
        }
    }
  
    $checktag = SCADA::fetch_row(SCADA::execu("SELECT TMPL_DESC FROM TEMPLATES WHERE TMPL_DESC = '$name'"));

    if ($name === $checktag['TMPL_DESC']) {
        header("Location: ../Index.php?tmpduplicate=error");
    } else {
        $date = date("d-M-y");
        $GEN_ID = SCADA::fetch_row(SCADA::execu("SELECT MAX(TMPL_ID)+1 AS MAX_ID FROM TEMPLATES"));
        SCADA::execu("INSERT INTO TEMPLATES (TMPL_ID, TMPL_DESC, TMPL_OWNER, PUBLIC_FLAG) 
        VALUES ('$GEN_ID[0]', '$name', '$owner', '')");
        foreach ($new_array as $key => $value) {
            $TAG = explode("/", $key);
            // Map code type based on the status (the second part of the key)
            $code_type_map = [
                'Current' => 1,
                'Average' => 2,
                'Min' => 3,
                'Max' => 4,
                'Diff Index' => 5,
                'Not Used' => 6,
                'Integrated' => 7,
            ];
            
            $code_type = $code_type_map[$TAG[1]] ?? null; // Look up by status
            SCADA::execu("INSERT INTO TEMPLATE_DETAILS 
            (TMPL_ID, COL_ID, TAGNAME, CODE_TYPE, PRECISION, ADD_DATE) 
            VALUES ($GEN_ID[0], " . $n++ . ", '$value', $code_type, ' ', '$date')");
        }
        header("Location: ../Index.php?save=success&nametmp=$name");
    }
}
    public function DeleteTemplate($id) {
        SCADA::execu("DELETE FROM TEMPLATES WHERE TMPL_ID = $id");
        SCADA::execu("DELETE FROM TEMPLATE_DETAILS WHERE TMPL_ID = $id");
    }
    public function DeleteMeter($name){
        SCADA::execu("DELETE FROM REF_FC_NAME WHERE FC_NAME = '$name'");
    }
    public function DeleteTag($fc_name,$tagname,$table){
        SCADA::execu("DELETE FROM $table WHERE TAGNAME = '$tagname' AND FC_NAME = '$fc_name'");
    }
    public function DeleteUser($username,$group){
        SCADA::execu("DELETE FROM SYS_USERS WHERE USER_NAME = '$username'");
    }
    public function EditMeter($meter_name,$desription,$maintenance,$check_db,$check_flag,$check_rtu){
        $check_db === 'true' ? $check_db = 1 : $check_db = 0;
        $check_flag === 'true' ? $check_flag = 1: $check_flag = 0;
        $check_rtu === 'true' ? $check_rtu = 1: $check_rtu = 0;
        SCADA::execu("UPDATE REF_FC_NAME SET 
            FC_NAME = '$meter_name',FC_DESC = '$desription',REMARK = '$maintenance',CHECKTIME_DB = $check_db,
            CHECKTIME_FLAG = $check_flag ,CHECKTIME_RTU = $check_rtu WHERE FC_NAME = '$meter_name'
        ");
    }
    public function UpdateTamplate($id,$val,$flag){
        $pubflag = "";
        $flag === 'true' ? $pubflag = "Y" : $pubflag = null;
        SCADA::execu("UPDATE TEMPLATES SET TMPL_DESC = '$val', PUBLIC_FLAG = '$pubflag' WHERE TMPL_ID = $id");
    }
    public function updateTag(
        $table,$meter_name,$tag_name,$tag_header,$description,
        $Precision,$Sort_order,$check_meter,$keeptag){
        $check_meter === 'true' ? $check_meter = 1: $check_meter = 0;
        SCADA::execu("UPDATE $table SET TAGNAME = '$tag_name', DESCRIPTION = '$description', 
        PRECISION = $Precision, SORT_ORDER = $Sort_order,TAG_HEADER = '$tag_header', 
        MONITOR_FLAG = $check_meter WHERE FC_NAME = '$meter_name' AND TAGNAME = '$keeptag'");
    }
    public function UpdateUserManage($username,$groupid,$keepuser){
        self::CheckUserGroupPTT($groupid);
        SCADA::execu("UPDATE SYS_USERS SET USER_NAME = '$username',GROUP_ID = $groupid WHERE USER_NAME = '$keepuser'");
    }
    public static function CheckUserGroupPTT($GroupID){
        $Group = '';
        $GroupID == 1 ? $Group = 'PTT Users' : $Group;
        $GroupID == 2 ? $Group = 'PTT Admins' : $Group;
        $GroupID == 3 ? $Group = 'SCADA History Users' : $Group;
        $GroupID == 4 ? $Group = 'SCADA History Admins' : $Group;
        $GroupID == 5 ? $Group = 'GMDR Users' : $Group;
        $GroupID == 6 ? $Group = 'GMDR Admins' : $Group;
        return $Group;
    }
    public function InsertNewUser($username,$groupid){
        SCADA::execu("INSERT INTO SYS_USERS(USER_NAME,GROUP_ID)VALUES('$username',$groupid)");
    }
    public function InsertNewMeter(
        $meter_name,$description,$maintenance,$check_db,$check_flag,$check_rtu
    ){
        $check_db == 'true' ? $check_db = 1: $check_db = 0;
        $check_flag == 'true' ? $check_flag = 1: $check_flag = 0;
        $check_rtu == 'true' ? $check_rtu = 1: $check_rtu = 0;
        SCADA::execu("INSERT INTO REF_FC_NAME(FC_NAME,FC_DESC,REMARK,CHECKTIME_FLAG,CHECKTIME_RTU,CHECKTIME_DB)
        VALUES('$meter_name','$description','$maintenance',$check_flag,$check_rtu,$check_db)");
    }
    public function InsertNewTagGMDR(
        $meter_name,$tag_name,$tag_header,$desription,
        $Precision,$Sort_order, $check_meter,$table){
        $check_meter == 'true' ? $check_meter = 1 : $check_meter = 0;
        SCADA::execu("INSERT INTO $table(FC_NAME,TAGNAME,DESCRIPTION,PRECISION,SORT_ORDER,TAG_HEADER,MONITOR_FLAG)
        VALUES('$meter_name','$tag_name','$desription','$Precision',$Sort_order,'$tag_header',$check_meter)");
    }
};
?>