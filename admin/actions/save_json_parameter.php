<?php
include 'check_auth.php';

if (true)
{
	$param = $_POST['Param'];
	$value = $_POST['Value'];
	$texts = json_decode(file_get_contents('../../Texts.json'), true);
	$texts[$param] = $value;
	$texts = json_encode($texts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	file_put_contents('../../Texts.json', $texts);
	echo '1';
}
else
{
	echo '0';
}
	