<?php
include(__DIR__ .'/subClass.php');
// Define allowed origins
$allowed_origins = ['https://pmis.pipeline.pttplc.com', 'https://www.pttplc.com'];
// Get the origin of the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
// Check if the origin is allowed
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://pmis.pipeline.pttplc.com"); // Default origin
}
// Set other CORS headers
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
// Process the POST request
http_response_code(200);

$SCADA = new SCADA();
$SCADA->OpenDebug();

session_start();

$data = json_decode(file_get_contents("php://input"), true);
print_r($data);
//Login SSO Microsoft
if (isset($data['user_sso'])) {
    // Set the session variable
    $_SESSION["USER"] = $data['user_sso'];
    $_SESSION["LOGIN_SESSION"] = "LOGIN_SSO";
    http_response_code(200); // Success
    echo json_encode(["message" => "Session set successfully."]);
} else if (isset($_POST['username']) && isset($_POST['password'])) {
    //Login ESS
    $_SESSION["LOGIN_SESSION"] = "LOGIN_ESS";
    $SCADA->CheckLogin($_POST['username'], $_POST['password']);
}
if(isset($_POST['tmplID'])){
	foreach($SCADA->genTagList($_POST['tmplID']) as $key => $value){
		echo $value['TAGNAME']." (".$value['TYPE_VALUE'].")"."\n";
	}
}
if(isset($_POST['tmp_id']) && isset($_POST['tmp_owner']) && isset($_POST['tmp_name'])){
	$SCADA->UpdateTamplate($_POST['tmp_id'],$_POST['tmp_name'],$_POST['checkflag']);
}
if(isset($_POST['roles'])){ echo $SCADA->checkRoles($_POST['roles']);}
if(isset($_POST['tmpname']) && isset($_POST['selected_tag'])){
	$SCADA->SaveNewTamplate($_POST['tmpname'],$_POST['selected_tag']);
}
if(isset($_POST['id'])){ $SCADA->DeleteTemplate($_POST['id']); }
if(isset($_POST['meter_name']) && isset($_POST['desription']) && isset($_POST['maintenance'])){
	$SCADA->EditMeter(
		$_POST['meter_name'],$_POST['desription'],$_POST['maintenance'],
		$_POST['check_db'],$_POST['check_flag'],$_POST['check_rtu']
	);
}
if(isset($_POST['fc_name'])){ $SCADA->DeleteMeter($_POST['fc_name']); }
if(isset($_POST['fc_name']) && isset($_POST['tagname']) && isset($_POST['table'])){
	$SCADA->DeleteTag($_POST['fc_name'],$_POST['tagname'],$_POST['table']);
}
if(isset($_POST['table']) && isset($_POST['meter_name']) && 
	isset($_POST['tag_name']) && isset($_POST['tag_header']) && 
	isset($_POST['description']) && isset($_POST['Precision']) && isset($_POST['Sort_order'])){
	$SCADA->updateTag($_POST['table'],$_POST['meter_name'],$_POST['tag_name'],$_POST['tag_header'],
	$_POST['description'],$_POST['Precision'],$_POST['Sort_order'],$_POST['check_meter'],$_POST['oldtag']);
}
if(isset($_POST['Username']) && isset($_POST['groupid']) && isset($_POST['keepuser'])){
	$SCADA->UpdateUserManage($_POST['Username'],$_POST['groupid'],$_POST['keepuser']);
}
if(isset($_POST['user']) && isset($_POST['group'])){
	$SCADA->DeleteUser($_POST['user'],$_POST['group']);
}
if(isset($_POST['Username']) && isset($_POST['groupid'])){
	$SCADA->InsertNewUser($_POST['Username'],$_POST['groupid']);
}
if(isset($_POST['meter_name']) && isset($_POST['description']) && isset($_POST['maintenance']) 
	&& isset($_POST['check_db']) &&  isset($_POST['check_flag']) && isset($_POST['check_rtu'])){
	$SCADA->InsertNewMeter($_POST['meter_name'], $_POST['description'], $_POST['maintenance'], 
  $_POST['check_db'], $_POST['check_flag'], $_POST['check_rtu']);
}
if(isset($_POST['meter_name']) && isset($_POST['tag_name']) && isset($_POST['tag_header']) && 
isset($_POST['desription']) && isset($_POST['Precision']) && isset($_POST['Sort_order']) && isset($_POST['table'])){
	$SCADA->InsertNewTagGMDR($_POST['meter_name'],$_POST['tag_name'],$_POST['tag_header'],
  $_POST['desription'],$_POST['Precision'],$_POST['Sort_order'],$_POST['check_meter'],$_POST['table']);
}
if(isset($_POST['Permiss'])){
  if($SCADA->checkRoles($_POST['Permiss']) == 'error'){
    echo "";
  }else{
    echo "success";
  }
}
?>