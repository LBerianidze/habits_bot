<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 20.05.2020
 * Time: 18:05
 */

include 'check_auth.php';

include "DBConfig.php";
$chat_id = $_REQUEST['chat_id'];
$db_config = new DBConfig();
$db_config->deleterUser($chat_id);
echo 1;