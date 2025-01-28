<?php session_start(); if (!$_SESSION["USER"]) header("Location: ../Login.php"); ?>
<!DOCTYPE html>
<html lang="en">
<?php include ('../components/Header.php'); ?>
<style>
/* container */
.container {
  border-radius:3px !important;
  max-width:98%;
  height: 510px !important;
  margin: auto;
  position: relative;
  background-color:rgba(0, 0, 0, 0.4) !important;
  color:#fff;
}
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
.table-container{
  background-color: #fff;
  padding: 15px 15px;
  margin-left:10px;
  margin-right:10px;
  border-radius: 5px;
}
</style>
<script src="../js/Loader.js"></script>
<body>
  <div class="background-loader" id="loading-p">
    <div class="loader">
      <span class="spinner spinner1"></span>
      <br>
      <span class="loader-text" id="loader-text" style="width:100%;"></span>
      <div class="counter" style="display:none;"><h1>0</h1></div>
    </div>
  </div>
    <h4 class="TAG" id='metertag'><img src="../img/PTT.png" width="35" height="45" style="margin-top:-18px;margin-left:-10px;margin-right:10px;"/><B>CONFIG TAG : <?php echo $_POST['cboTemplate3']; ?></B></h4>
    <div class="table-container-data-scada" id="loadpage">
        <a class="btn btn-success" style="float:left;margin-right:10px;margin-left:10px;" href="./Index.php">
        <i class="fa fa-home"></i></a>
        <a class="btn btn-warning" style="float:left;margin-right:10px;" onclick="AddTagGMDR('<?php echo $_POST['cboTemplate3']; ?>')">
        <i class="fa fa-plus"></i> Add Tag</a>
        <?php
        include './auth/subClass.php';
        $SCADA = new SCADA();        
        echo $SCADA->getTagconfig($_POST['Period'],$_POST['cboTemplate3']);
        ?>
    </div>
</body>
</html>