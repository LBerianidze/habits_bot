<?php
include 'check_auth.php';
$param = $_GET['Param'];
$texts = json_decode(file_get_contents('../../Texts.json'), true);
echo str_replace(array(
	"\r\n",
	"\r",
	"\n"
), "<br>", $texts[$param]);