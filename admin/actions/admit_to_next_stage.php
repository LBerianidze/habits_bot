<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 16.05.2020
 * Time: 14:49
 */
include 'check_auth.php';

include('../../vendor/autoload.php');
include('DBConfig.php');
include('../../keyboards.php');
include('../../BotHelper.php');

use Longman\TelegramBot\Request;

$json = json_decode(file_get_contents('../../Texts.json'));
$chat_id = $_REQUEST['chat_id'];
$telegram = new Longman\TelegramBot\Telegram('1032111112:AAGHNnHc3cmEgFIV1EzTtoWfW4TTZxLzAq0', '@Sarafaner_bot');
$db_config = new DBConfig();
$stage = $db_config->getStage($chat_id);
if ($stage == 1)
{
    Request::sendMessage([
        'chat_id' => $chat_id,
        'text' => $json->first_done,
        'reply_markup' => moveToSecondStageKeyBoard()
    ]);
    $db_config->setValue($chat_id, 'first_done_date', getCurrentDate());
    $db_config->setLastActionType($chat_id,20);
}
else if ($stage == 2)
{
    Request::sendMessage([
        'chat_id' => $chat_id,
        'text' => $json->second_done,
        'reply_markup' => moveToThirdStageKeyBoard()
    ]);
    $db_config->setValue($chat_id, 'second_done_date', getCurrentDate());
    $db_config->setLastActionType($chat_id,21);
}
else
{
    Request::sendMessage([
        'chat_id' => $chat_id,
        'text' => $json->third_done,
        'reply_markup' => finishMarafonKeyBoard($json->chat_vip)
    ]);
    $db_config->setValue($chat_id, 'third_done_date', getCurrentDate());
    $db_config->setStage($chat_id,4);
    $db_config->setLastActionType($chat_id,22);
}
$db_config->setAccessToNextStage($chat_id, 1);
$db_config->setNotifyCount($chat_id, 0);
echo 1;