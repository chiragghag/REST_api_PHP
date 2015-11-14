<?php
$db_conx = mysqli_connect("localhost", "User_name", "Password", "DB_name");
// Evaluate the connection
if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
}
?>