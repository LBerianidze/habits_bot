<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 18.05.2020
 * Time: 19:38
 */
include 'check_auth.php';

include "DBConfig.php";
$chat_id = $_REQUEST['chat_id'];
$type = $_REQUEST['type'];
$db_config = new DBConfig();
if ($type == 0)
{
	$user = $db_config->getUserByTelegramID($chat_id);
    $habits = $db_config->getUserHabitsByID($chat_id);
}
else
{
	$user = $db_config->getUserByUsername($chat_id);
    $habits = $db_config->getUserHabits($chat_id);
}
if($user==false)
{
	echo 0;
	exit();
}
$user['habits_count']= count($habits);
$user['habits']= $habits;
echo json_encode($user);
