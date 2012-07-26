<?php
include("db.php");

$transaction_id = $_POST['transaction_id'];
$member_id = $_POST['member_id'];

$pair_sql = "INSERT INTO `pairing` (`transaction_id`,`member_id`) VALUES ($transaction_id,$member_id) ON DUPLICATE KEY UPDATE member_id=$member_id";
if (!mysql_query($pair_sql)) {
	echo "Error ".mysql_error();
	die;
}

header ("Location: list.php"); 

?>
