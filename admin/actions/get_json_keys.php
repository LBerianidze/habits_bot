<?php
	/**
	 * Created by PhpStorm.
	 * User: Luka
	 * Date: 19.06.2019
	 * Time: 11:17
	 */
	include 'check_auth.php';
	$texts = json_decode(file_get_contents('../../Texts/.json'), true);
	$keys = array();
	foreach ($texts as $index => $text)
	{
		$keys[] = $index;
	}
	echo json_encode($keys,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);