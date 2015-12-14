<?php
include_once("php_includes/db_conx.php");
//Select data from database
if(isset($_GET["pid"])){
$pid = $_GET["pid"];
$propData = "SELECT t.pid AS 'pid',(CASE WHEN t.cost < 100000 AND t.cost >=1 THEN CONCAT (\"@\",t.cost) ELSE
	(CASE WHEN t.cost < 300000 AND t.cost>= 100000 THEN CONCAT (\"@\",TRUNCATE (t.cost/100000,2),\"L\") ELSE
	(CASE WHEN t.cost < 10000000 AND t.cost>= 300000 THEN CONCAT (TRUNCATE (t.cost/100000,2),\"L\") ELSE 
	(CASE WHEN t.cost >= 10000000 THEN CONCAT (TRUNCATE (t.cost/10000000,2),\"Cr\") ELSE
	t.cost END)
END)END)END) AS Cost,
(CASE WHEN t.rent < 10000000 AND t.rent >= 100000 THEN CONCAT (TRUNCATE (t.rent/100000,2),\"L\") ELSE
	 (CASE WHEN t.rent >= 10000000 THEN CONCAT (TRUNCATE (t.rent/10000000,2),\"Cr\") ELSE
	 t.rent END)
END) AS 'Rent',
(CASE WHEN t.deposit < 10000000 AND t.deposit >= 100000 THEN CONCAT (TRUNCATE (t.deposit/100000,2),\"L\") ELSE
	 (CASE WHEN t.deposit >= 10000000 THEN CONCAT (TRUNCATE (t.deposit/10000000,2),\"Cr\") ELSE
	 t.deposit END)
END)
AS 'Deposite',
(CASE WHEN t.optionalinfo = '' THEN t.address  ELSE CONCAT (t.address,\" - (\", t.optionalinfo, \")\") END) AS 'Address',
t.locality AS Locality,
(CASE WHEN t.type='RH - ROW HOUSE' THEN 'RH' ELSE t.type END) AS 'Type',

CONCAT((CASE WHEN t.floor='1ST - FIRST' THEN '1st' ELSE
	(CASE WHEN t.floor='2ND - SECOND' THEN '2nd' ELSE
	(CASE WHEN t.floor='3RD - THIRD' THEN '3rd' ELSE 
	(CASE WHEN t.floor='4TH - FOURTH' THEN '4th' ELSE 
	(CASE WHEN t.floor='5TH - FIFTH' THEN '5th' ELSE 
	(CASE WHEN t.floor='6TH - SIXTH' THEN '6th' ELSE 
	(CASE WHEN t.floor='7TH - SEVENTH' THEN '7th' ELSE 
	(CASE WHEN t.floor='8TH - EIGHTH' THEN '8th' ELSE 
	(CASE WHEN t.floor='9TH - NINTH' THEN '9th' ELSE 
	(CASE WHEN t.floor='10TH - TENTH' THEN '10th' ELSE 
	(CASE WHEN t.floor='11TH - ELEVENTH' THEN '11th' ELSE 
	(CASE WHEN t.floor='12TH - TWELFTH' THEN '12th' ELSE 
	(CASE WHEN t.floor='G - GROUND' THEN 'G' ELSE 
	(CASE WHEN t.floor='H - HIGHER' THEN 'H' ELSE 
	(CASE WHEN t.floor='M - MIDDLE' THEN 'M' ELSE 
	(CASE WHEN t.floor='T - TOP' THEN 'T' ELSE 
	(CASE WHEN t.floor='L - LOWER' THEN 'L' ELSE t.floor
END)END)END)END)END)END)END)END)END)END)END)END)END)END)END)END)END),\" Flr\") AS 'Floor'

,CONCAT(t.area,\" SqFt\")AS 'Area' , 
t.directside AS 'Brokerage',
t.rescom AS 'R/C',
DATE_FORMAT(t.lastupdate,'%d %M %Y') AS \"Date\",
t.uid

FROM tbl_properties t 
WHERE t.pid = '$pid'
ORDER BY t.lastupdate DESC";
$qur2 = $db_conx->query($propData);

$numrows = mysqli_num_rows($qur2);
if($numrows < 1){
$msg[] = array("error" => "No Properties Found");
}

while($r2 = mysqli_fetch_assoc($qur2)){
$uid=$r2['uid'];
$getData = "SELECT tu.uid, tu.email, tu.phoneno, tud.bname, tud.aname, tud.altno, tud.locality FROM tbl_users tu JOIN tbl_userdetails tud ON tu.uid=tud.uid where tu.uid = '$uid'";
$qur = $db_conx->query($getData);

$countquery="SELECT COUNT(tp.pid) AS 'count' FROM `tbl_properties` tp JOIN `tbl_users` tu ON tp.uid=tu.uid WHERE tu.uid = '$uid'";
$qur1 = $db_conx->query($countquery);
$r1 = mysqli_fetch_assoc($qur1);
$r = mysqli_fetch_assoc($qur);
$agentinfo[] = array("phoneno" => $r['phoneno'], "email" => $r['email'] ,"bname" => $r['bname'], "aname" => $r['aname'], "altno" => $r['altno'],"locality" => $r['locality'], "propertycount" => intval($r1['count']));

$numrows = mysqli_num_rows($qur);
if($numrows < 1){
$agentinfo[] = array("error" => "Agent Not Found");
}

if(strtolower(trim($r2['Cost'])) == strtolower("0")){
$cost=intval($r2['Cost']);
}else{
$cost=$r2['Cost'];
}
if(strtolower(trim($r2['Rent'])) == strtolower("0")){
$Rent=intval($r2['Rent']);
}else{
$Rent=$r2['Rent'];
}
if(strtolower(trim($r2['Deposite'])) == strtolower("0")){
$Deposite=intval($r2['Deposite']);
}else{
$Deposite=$r2['Deposite'];
}

$msg[] = array("Cost" => $cost,"Rent" => $Rent,"Deposite" => $Deposite,"Address" => $r2['Address'],"Locality" => $r2['Locality'],"Type" => $r2['Type'],"Floor" => $r2['Floor'],"Area" => $r2['Area'],"Brokerage" => $r2['Brokerage'],"Rescom" => $r2['R/C'],"Date" => $r2['Date'],"pid" => $r2['pid'],"agentinfo" => $agentinfo);
}
}
else{
$msg =  array("error" =>"query parameter 'pid' not set");
}
$json = $msg;

header('content-type: application/json');
echo json_encode($json);

@mysqli_close($db_conx);

?>