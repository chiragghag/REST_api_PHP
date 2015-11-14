<?php
include_once("php_includes/db_conx.php");
//Select data from database
if(isset($_GET["city"]) && isset($_GET["town"]) && isset($_GET["offset"])){
$town = $_GET["town"];
$city = $_GET["city"];
if(isset($_GET["search"])){
$search = $_GET["search"];
}else{
$search = '';
}
$limit =  "50";
$offset = $_GET["offset"];
$getData = "SELECT tu.email, tu.phoneno, tud.bname, tud.aname, tud.altno, tud.locality, tud.logo FROM tbl_users tu JOIN tbl_userdetails tud ON tu.uid=tud.uid WHERE tud.city = '$city' AND tud.town = '$town' AND CONCAT_WS(tud.bname, tud.aname, tud.locality) LIKE '%$search%' LIMIT $limit OFFSET $offset";
$qur = $db_conx->query($getData);

$numrows = mysqli_num_rows($qur);
if($numrows < 1){
$msg2[] = array();
}
else {
while($r = mysqli_fetch_assoc($qur)){
$email=$r['email'];
$countquery="SELECT COUNT(tp.pid) AS 'count' FROM `tbl_properties` tp JOIN `tbl_users` tu ON tp.uid=tu.uid WHERE tu.email='$email'";
$qur1 = $db_conx->query($countquery);
//while($r1 = mysqli_fetch_assoc($qur1)){
$r1 = mysqli_fetch_assoc($qur1);

$msg[] = array("phoneno" => $r['phoneno'], "email" => $r['email'] ,"bname" => $r['bname'], "aname" => $r['aname'], "altno" => $r['altno'],"locality" => $r['locality'],"logo" => $r['logo'], "propertycount" => intval($r1['count']));
$propCount = array();
foreach ($msg as $key => $row)
{
    $propCount[$key] = $row['propertycount'];
}
array_multisort($propCount, SORT_DESC, $msg);
}

$countqueryagent="SELECT COUNT(*) AS 'count' FROM tbl_users tu JOIN tbl_userdetails tud ON tu.uid=tud.uid WHERE tud.city = '$city' AND tud.town = '$town' AND CONCAT_WS(tud.bname, tud.aname, tud.locality) LIKE '%$search%'";
$qur2 = $db_conx->query($countqueryagent);
//while($r1 = mysqli_fetch_assoc($qur1)){
$r2 = mysqli_fetch_assoc($qur2);
$msg2[] = array("agentlist" => $msg, "agentcount" => $r2['count']);
}
}
else{
$msg2 =  array("error" =>"query parameter city, town or offset not set");
}
$json = $msg2;

header('content-type: application/json');
echo json_encode($json);

@mysqli_close($db_conx);

?>