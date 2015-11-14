<?php
include_once("php_includes/db_conx.php");
//Select data from database
$temp="";
$city = "";
//$town = $_GET["town"];
//$getData = "SELECT DISTINCT city FROM tbl_citytown WHERE town = town";
$getData = "SELECT ";
if(isset($_GET["city"])){
$city = $_GET["city"];
$getData .= " town";	
$temp .= " WHERE city = '$city'";
}
else{
$getData .= " DISTINCT city";	
}
$getData .= " FROM tbl_citytown";
$getData .= $temp;
$qur = $db_conx->query($getData);

while($r = mysqli_fetch_assoc($qur)){
if(isset($_GET["city"])){
$msg[] = array("town" => $r['town']);
}else{
$msg[] = array("city" => $r['city']);
}
}
$json = $msg;

header('content-type: application/json');
echo json_encode($json);

@mysqli_close($db_conx);

?>