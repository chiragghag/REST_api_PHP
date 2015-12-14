<?php
include_once("php_includes/db_conx.php");
//Select data from database

$getData1 = "SELECT DISTINCT tp.type FROM tbl_properties tp WHERE tp.rescom='commercial' ORDER BY tp.type";
$qur = $db_conx->query($getData1);

$numrows = mysqli_num_rows($qur);
if($numrows < 1){
$commercialtypes = array();
}
while($r2 = mysqli_fetch_assoc($qur)){
$commercialtypes[] = array("type" => $r2['type']);
}

$getData2 = "SELECT DISTINCT tp.type FROM tbl_properties tp WHERE tp.rescom='RESIDENTIAL' ORDER BY tp.type";
$qur2 = $db_conx->query($getData2);

$numrows1 = mysqli_num_rows($qur2);
if($numrows1 < 1){
$residentialtypes = array();
}
while($r2 = mysqli_fetch_assoc($qur2)){
$residentialtypes[] = array("type" => $r2['type']);
}

$maxcost = "SELECT MAX(cost) AS 'cost' FROM tbl_properties";
$qur1 = $db_conx->query($maxcost);
$r1 = mysqli_fetch_assoc($qur1);
$msg = array("maxcost" => $r1['cost'],"commercialtypes"=> $commercialtypes, "residentialtypes"=> $residentialtypes);

$json = $msg;

header('content-type: application/json');
echo json_encode($json);

@mysqli_close($db_conx);

?>