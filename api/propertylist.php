<?php
include_once("php_includes/db_conx.php");
//Select data from database
if(isset($_GET["sellrent"]) && isset($_GET["city"]) && isset($_GET["town"]) && isset($_GET["offset"])){
$sellrent = $_GET["sellrent"];
$town = $_GET["town"];
$city = $_GET["city"];
if(isset($_GET["rescom"])){
$rescom = $_GET["rescom"];
$rescomquery = " AND t.rescom ='$rescom' ";
}
else{
$rescomquery = " ";
}

if(isset($_GET["type"])){
$type = $_GET["type"];
$typequery = " AND t.type ='$type' ";
}
else{
$typequery = " ";
}

if(isset($_GET["costmin"])){
$costmin = $_GET["costmin"];
if(strtolower(trim($sellrent)) == strtolower("sell"))
$costminquery = " AND t.cost > '$costmin' ";
else
$costminquery = " AND t.rent > '$costmin' ";
}
else{
$costminquery = " ";
}

if(isset($_GET["costmax"])){
$costmax = $_GET["costmax"];
if(strtolower(trim($sellrent)) == strtolower("sell"))
$costmaxquery = " AND t.cost < '$costmax' ";
else
$costmaxquery = " AND t.rent < '$costmax' ";
}
else{
$costmaxquery = " ";
}

if(isset($_GET["sort"])){
$sort = $_GET["sort"];
if(strtolower(trim($sellrent)) == strtolower("sell")){
if(strtolower(trim($sort)) == strtolower("desc"))
$sortquery = " t.cost DESC, ";
else
$sortquery = " t.cost, ";
}
else{
if(strtolower(trim($sort)) == strtolower("desc"))
$sortquery = " t.rent DESC, ";
else
$sortquery = " t.rent, ";
}
}
else{
$sortquery = " ";
}

if(isset($_GET["search"])){
$search = $_GET["search"];
}else{
$search = '';
}
$limit = "50";
$offset = $_GET["offset"];
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
WHERE t.city = '$city'
AND t.town = '$town'
AND t.sellrent = '$sellrent'
$rescomquery
$typequery
$costminquery
$costmaxquery
AND CONCAT_WS(t.locality, t.type, t.rescom, t.floor) LIKE '%$search%'
ORDER BY $sortquery t.lastupdate DESC LIMIT $limit OFFSET $offset";
$qur2 = $db_conx->query($propData);

$numrows = mysqli_num_rows($qur2);
if($numrows < 1){
$msg2[] = array();
}
else{
while($r2 = mysqli_fetch_assoc($qur2)){
$msg[] = array("Cost" => $r2['Cost'],"Rent" => $r2['Rent'],"Deposite" => $r2['Deposite'],"Address" => $r2['Address'],"Locality" => $r2['Locality'],"Type" => $r2['Type'],"Floor" => $r2['Floor'],"Area" => $r2['Area'],"Brokerage" => $r2['Brokerage'],"Rescom" => $r2['R/C'],"Date" => $r2['Date'],"pid" => $r2['pid']);
}

$countqueryproperty="SELECT COUNT(*) AS 'count' FROM tbl_properties t 
WHERE t.city = '$city'
AND t.town = '$town'
AND t.sellrent = '$sellrent'
$rescomquery
$typequery
$costminquery
$costmaxquery
AND CONCAT_WS(t.locality, t.type, t.rescom, t.floor) LIKE '%$search%'";
$qur2 = $db_conx->query($countqueryproperty);
//while($r1 = mysqli_fetch_assoc($qur1)){
$r2 = mysqli_fetch_assoc($qur2);
$msg2[] = array("propertylist" => $msg, "propertycount" => $r2['count']);

}
}
else{
$msg2 =  array("error" =>"query parameter city, town, sellrent or offset not set");
}
$json = $msg2;

header('content-type: application/json');
echo json_encode($json);

@mysqli_close($db_conx);

?>