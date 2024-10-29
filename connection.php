<?php
$servername = "wyqk6x041tfxg39e.chr7pe7iynqr.eu-west-1.rds.amazonaws.com";
$database = "dhvdlcoce2vbat74";
$username = "xkofzkjzzkct05up";
$password = "tafvbqjxlwugf7l3";

$conn = mysqli_connect($servername, $username, $password, $database);

if ($conn) {
    echo "👌";
}else
{
    echo "👎: " . mysqli_connect_error();
}

?>
mysql://xkofzkjzzkct05up:tafvbqjxlwugf7l3@wyqk6x041tfxg39e.chr7pe7iynqr.eu-west-1.rds.amazonaws.com:3306/dhvdlcoce2vbat74
