<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 17.05.2020
 * Time: 19:44
 */
//include 'check_auth.php';

include "DBConfig.php";
$type = $_REQUEST['type'];
$where = 'where last_action_type='.$type;

$db_config = new DBConfig();
$count = $db_config->getUsersCount($where);
$users =$db_config->getFilteredUsers($where);
$json_array = array();
$json_array[] = $users;
$json_array[] = $count;
echo json_encode($json_array);