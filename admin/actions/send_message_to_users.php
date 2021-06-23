<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 16.05.2020
 * Time: 14:49
 */
include 'check_auth.php';

include('../../vendor/autoload.php');
include('../../keyboards.php');
include('../../BotHelper.php');

use Longman\TelegramBot\Request;

$users = $_REQUEST['users'];
$message = $_REQUEST['message'];
$users = explode(',', $users);
$telegram = new Longman\TelegramBot\Telegram('1032111112:AAGHNnHc3cmEgFIV1EzTtoWfW4TTZxLzAq0', '@Sarafaner_bot');

foreach ($users as $chat_id)
{
    Request::sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}


echo 1;