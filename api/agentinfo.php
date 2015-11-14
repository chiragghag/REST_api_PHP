<?php
include_once("php_includes/db_conx.php");
//Select data from database
if(isset($_GET["email"]) && isset($_GET["sellrent"])){
$email = $_GET["email"];
$sellrent = $_GET["sellrent"];
$getData = "SELECT tu.uid, tu.email, tu.phoneno, tud.bname, tud.aname, tud.altno, tud.locality, tud.logo FROM tbl_users tu JOIN tbl_userdetails tud ON tu.uid=tud.uid where email = '$email'";
$qur = $db_conx->query($getData);

$numrows = mysqli_num_rows($qur);
if($numrows < 1){
$msg[] = array("error" => "Agent Not Found");
}

while($r = mysqli_fetch_assoc($qur)){
$countquery="SELECT COUNT(tp.pid) AS 'count' FROM `tbl_properties` tp JOIN `tbl_users` tu ON tp.uid=tu.uid WHERE tu.email='$email'";
$qur1 = $db_conx->query($countquery);
$uid = $r['uid'];
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
DATE_FORMAT(t.lastupdate,'%d %M %Y') AS \"Date\"

FROM tbl_properties t 
JOIN `tbl_users` tu ON t.uid=tu.uid
JOIN `tbl_userdetails` tud ON tud.uid=t.uid 
WHERE t.uid = '$uid'
AND t.sellrent = '$sellrent'
ORDER BY t.lastupdate DESC";
$qur2 = $db_conx->query($propData);

$numrows = mysqli_num_rows($qur2);
if($numrows < 1){
$propList = array();
}

while($r2 = mysqli_fetch_assoc($qur2)){
$propList[] = array("Cost" => $r2['Cost'],"Rent" => $r2['Rent'],"Deposite" => $r2['Deposite'],"Address" => $r2['Address'],"Locality" => $r2['Locality'],"Type" => $r2['Type'],"Floor" => $r2['Floor'],"Area" => $r2['Area'],"Brokerage" => $r2['Brokerage'],"Rescom" => $r2['R/C'],"Date" => $r2['Date'],"pid" => $r2['pid']);
}
//while($r1 = mysqli_fetch_assoc($qur1)){
$r1 = mysqli_fetch_assoc($qur1);
$msg[] = array("phoneno" => $r['phoneno'], "email" => $r['email'] ,"bname" => $r['bname'], "aname" => $r['aname'], "altno" => $r['altno'],"locality" => $r['locality'],"logo" => $r['logo'], "propertycount" => intval($r1['count']), "properties" => $propList);
}
}
else{
$msg =  array("error" =>"query parameter email or sellrent not set");
}
$json = $msg;

header('content-type: application/json');
echo json_encode($json);

@mysqli_close($db_conx);

?>