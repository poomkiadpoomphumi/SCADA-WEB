<?php 
include './auth/subClass.php'; 
$date = new DateTime("now", new DateTimeZone('Asia/Bangkok'));
$date->setTimestamp(time());
$date->format('H:i');
$SCADA = new SCADA();
$SCADA->OpenDebug();
$TodateXToTime = '';
$cboTemplate = '';
$tabledata = '';
$iCodition = '';
$dateString = '';
//$SCADA->OpenDebug();
session_start(); if (!$_SESSION["USER"]) header("Location: ../Login.php");
$gUserName = $_SESSION["USER"];
$tmplID = isset($_POST["cboTemplate"]);
if($_GET['table'] === 'tablescada' && isset($_POST['period'])){
  if($SCADA->CheckDurationMoreThan1min($_POST['Fromdate'],$_POST['Todate']) === true && trim($_POST['period']) == '1 Minute'){
			header("Location: ./Index.php?Morethan=3days");
	}else if($SCADA->CheckDurationMoreThan($_POST['Fromdate'],$_POST['Todate']) === true){
    if($_POST['tagnamehidden'] == ''){header("Location: ./Index.php?error=et8c");}
		if(isset($_POST['id_tmp'])) { $cboTemplate = $_POST['id_tmp']; /*id template*/} 
		if(isset($_POST['iCodition'])) {$iCodition = $_POST['iCodition'];}
		$TagRTU = $_POST['tagnamehidden'];

		$TagName = $_POST['tagnamescada'] === '-- Choose a Template --' ? '' : $_POST['tagnamescada'];
		$period = $_POST['period'];
		$chtag = $_POST['addtag'];
		$FromdateXFromtime = $_POST['Fromdate'] . ' ' . $_POST['Fromtime'];
		$iDisplay = $_POST['iDisplay'];
		//if($_POST['Fromtime'] || $_POST['Totime'] > $date->format('H:i')){
			//$TodateXToTime = $_POST['Todate'] . ' ' . $date->format('H:i');
		//}else{
			$TodateXToTime = $_POST['Todate'] . ' ' . $_POST['Totime'];
		//}
    $dateString = $FromdateXFromtime ." to ". $TodateXToTime;
		if($FromdateXFromtime && $iDisplay  && $period != null){
			$tabledata = $SCADA->TABLE_SCADA($period,$FromdateXFromtime,$TodateXToTime,$cboTemplate,$iDisplay,$iCodition,$TagRTU,$chtag);
		}
  }
}
if($_GET['table'] === 'tablegmdr'){
	if($SCADA->CheckDurationMoreThan($_POST['Fromdate-gmdr'],$_POST['Todate-gmdr']) === true){
		if($_POST['cboTemplate2'] == ''){header("Location: ./Index.php?error=et8c");}
		$table = $_POST['GMDRSETTABLE'];
		$TagName = $cboTemplate  = $_POST['cboTemplate2']; //id tag
		$FromtimeGMDR = $_POST['Fromdate-gmdr'] . ' ' . $_POST['Fromtime-gmdr'];
		$ToTimeGMDR = $_POST['Todate-gmdr'] . ' ' . $_POST['Totime-gmdr'];
    $dateString = $FromtimeGMDR ." to ". $ToTimeGMDR;
		if($table && $cboTemplate && $FromtimeGMDR && $ToTimeGMDR != null){
			$tabledata = $SCADA->TABLE_GMDR(
				$table,$cboTemplate ,$FromtimeGMDR,$ToTimeGMDR
			);
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Historical Operation Data Web Application</title>
  <link rel="shortcut icon" href="../img/favicon.png" type="image/gif" sizes="16x16">
  
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../vendor/sweetalert/sweetalert2.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/Index.css" />
    <script src="../js/Main.js"></script>
    <link rel="stylesheet" type="text/css" href="../vendor/boostrap4/boostrap.min.css" />
    <script src="../vendor/boostrap4/boostrap.min.js"></script>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <script src="../js/Loader.js"></script>
    
    <link rel="stylesheet" href="../css/Table.css">
    <link rel="stylesheet" href="../vendor/bootstrapTable/bound.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    

</head>

<body>
<?php include('../components/Navbar.php'); ?>
  <br/>
  <div class="background-loader" id="loading-p">
    <div class="loader">
      <span class="spinner spinner1"></span>
      <br>
      <div class="counter" style="display:none;"><h1>0</h1></div>
    </div>
  </div>
  
  <div class="table-container-data-scada" id="loadpage" style="margin-top:-10px;">
    <div style="display: flex; justify-content: space-between; align-items: center;margin-top:-10px;">
        <div>
          <a class="btn btn-success btn-sm" style="float:left; margin-right:10px; margin-left:10px;" href="./Index.php"><i class="fas fa-home"></i></a>
          <ul class="pagination pagination-sm" style="margin-top:1px">
            <li class="page-item"><a class="page-link" style="color: #828282;" onclick="exportTableToCSV('example', '<?php echo $TagName; ?>')">CSV</a></li>
            <li class="page-item"><a class="page-link" style="color: #828282;" onclick="exportTableToExcel('example', '<?php echo $TagName; ?>')">Excel</a></li>
        </ul>
        <span style="float:left; margin-right:10px; margin-left:10px; margin-top:-12px; margin-bottom:5px; font-size:12px;">
          <?php 
          if ($TagName !== '') {
            if($_GET['table'] === 'tablescada'){
              $reportType = strtoupper($period === 'Hour' ? 'HOURLY' : ($period === 'Day' ? 'DAILY' : $period));
              $formattedDate = strtoupper($dateString);
              echo "<b>{$reportType} REPORT <span style='color: orange;'>{$formattedDate}</span> TEMPLATE : " . strtoupper($TagName) . "</b>";
            }else{
              echo "<b>" . ($_POST['GMDRSETTABLE'] === 'Hour' ? 'HOURLY' :'DIALY' ) . " REPORT <span style='color: orange;'>".strtoupper($dateString)."</span>" . " METER : " . $TagName . "</b>";
            }
          }else if ($TagName === ''){
            if($_GET['table'] === 'tablescada'){
            $period = trim($period);
                $reportType = strtoupper(strtolower($period) === 'hour' ? 'HOURLY' : (strtolower($period) === 'day' ? 'DAILY' : $period));
                $formattedDate = strtoupper($dateString);
                echo "<b>{$reportType} REPORT <span style='color: orange;'>{$formattedDate}</span></b>";
              }else{
                echo "<b>" . ($_POST['GMDRSETTABLE'] === 'Hour' ? 'HOURLY' :'DIALY' ) . " REPORT <span style='color: orange;'>".strtoupper($dateString)."</span>" . " METER : " . $TagName . "</b>";
              }
          } else {
              echo null; 
          }
          ?>
          
        </span>
        </div>
        <div>
            <input type="text" id="searchInput" placeholder="Search..." class="form-control form-control-sm" style="width: 200px; margin-right: 10px;margin-top:-24px;" onkeyup="filterTable()">
        </div>
    </div>

    <table id="example" class="table-scada table-striped table-bordered" style="width:100%;">
      <?php
        // Use Bootstrap 3 styling for the table
        echo $tabledata;
        echo $SCADA->getFooter(); 
      ?>
    </table>
  </div>

  <?php include('../components/Modal.php'); ?>
</body>
<style>
.background-loader{
  position: fixed;
  top:0px;
  z-index: 300;
  background-color: rgb(0,0,0,0.8);
  width: 100%;
  height: 100%;
}

.loader{
  position: fixed;
  z-index: 301;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 200px;
  width: 200px;
  overflow: hidden;
  text-align: center;
}

.spinner{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 303;
  border-radius: 100%;
  border-right-color: transparent !important;
}

.spinner1{
  width: 50px;
  height: 50px;
  border: 6px solid #fff;
  animation: spin 1s linear infinite;
}


@keyframes spin {
  0%{
    transform: translate(-50%,-50%) rotate(0deg);
  }
  100%{
    transform: translate(-50%,-50%) rotate(360deg);
  }
}

@keyframes negative-spin {
  0%{
    transform: translate(-50%,-50%) rotate(0deg);
  }
  100%{
    transform: translate(-50%,-50%) rotate(-360deg);
  }
}

.loader-text {
  position: relative;
  top: 75%;
  color: #fff;
  font-weight: bold;
}
</style>
</html>