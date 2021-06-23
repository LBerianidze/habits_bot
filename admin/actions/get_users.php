<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 17.05.2020
 * Time: 19:44
 */
include 'check_auth.php';

include "DBConfig.php";
$page = $_REQUEST['page'];
$type = $_REQUEST['type'];
$where = 'where stage=' . $type;
if ($type == 0)
    $where = '';
$db_config = new DBConfig();
$count = $db_config->getUsersCount($where);
$users = $db_config->getUsers($page, $where);
$date1 = new DateTime('now');
$dt1 = $date1->format('yy-m-d');
$date1->add('P1D');
$today = $db_config->getRegisteredCountBetweenDates($dt1, $date1->format('yy-m-d H:i:s'));
$date1 = new DateTime('now');
$date1->sub(new DateInterval('P1D'));
$yesterday = $db_config->getRegisteredCountBetweenDates($date1->format('yy-m-d'), $date1->format('yy-m-d') . ' 23:59:59');

for ($i = 0; $i < count($users); $i++)
{
    $users[$i]['register_date'] = (new DateTime($users[$i]['register_date']))->format('d-m-yy');
}
$json_array = array();
$json_array[] = $users;
$json_array[] = $count;
$json_array[] = $today;
$json_array[] = $yesterday;
echo json_encode($json_array);