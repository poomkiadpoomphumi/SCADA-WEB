<?php 
session_start(); 
include './auth/subClass.php';  
if (!$_SESSION["USER"]) header("Location: ../Login.php");  
?>
<!DOCTYPE html>
<html lang="en">
<?php include ('../components/Header.php'); ?>
<script src="../js/Loader.js"></script>
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
<script>
$(document).ready(function () {
    var table = $("#example").DataTable({
      alengthMenu: [
        [10, 25, 50, 100],
        [10, 25, 50, 100],
      ],
      iDisplayLength: 25,
      searching: true,
      dom: '<"dt-buttons"Bf><"clear">lirtp',
      paging: true,
      autoWidth: true,
      buttons: ["csvHtml5", "excelHtml5", "print"],
      initComplete: function (settings, json) {
        var footer = $("#example tfoot tr");
        $("#example thead").append(footer);
      },
    });
    $("#example thead").on("keyup", "input", function () {
      table.column($(this).parent().index()).search(this.value).draw();
    });
  });
</script>
<body>
  <div class="background-loader" id="loading-p">
    <div class="loader">
      <span class="spinner spinner1"></span>
      <br>
      <div class="counter" style="display:none;"><h1>0</h1></div>
    </div>
  </div>
    <h4 class="TAG"><img src="../img/PTT.png" width="35" height="45" style="margin-top:-18px;margin-left:-10px;margin-right:10px;"/><B>SELECT TAG RTU <?php echo $_GET['RTU']; ?></B></label>
    &nbsp;<label style="color:red;font-size:20px;">(Maximum select 100 tag)*</label></label></h4>
<div class="table-container-data-scada" id="loadpage" style="background-color:#fff; margin:10px 10px; border-radius:5px; padding:20px 20px; height: 400px; overflow-y: scroll;height:auto;">
    <a class='btn btn-danger' style='float:left; margin-left:0px;' onclick='CancleAddTag()'>Cancel</a>
    <a class='btn btn-primary' style='float:left; margin-right:5px; margin-left:5px;' onclick='SubmitAddTag()'>Add</a>
    <?php
    $SCADA = new SCADA();
    echo $SCADA->GetTagRTU($_GET['RTU'], $_GET['shtag']);
    ?>
</div>

</body>
</html>