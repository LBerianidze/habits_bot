<?php

include('vendor/autoload.php');

$telegram = new Longman\TelegramBot\Telegram("", "");
$sqlcon = new mysqli("localhost", "", "", "");
mysqli_set_charset($sqlcon, 'UTF8');
$Users = $sqlcon->query("select * from TelegramDatabase");
$LanguageR = simplexml_load_file("Texts/Russian_BalanceNotifier.xml");
$LanguageE = simplexml_load_file("Texts/English_BalanceNotifier.xml");
foreach ($Users as $value)
{
    $userid = $value["ID"];
    $uname = $value['name'];
    $Language = $value['Language'];
    $ts = $value["Timestamp"];
    $totalbalance = $value['Balance'] + $value['BonusBalance'];
    $UserHabits = mysqli_fetch_assoc($sqlcon->query('SELECT SUM(Mistake_price)*3 AS Mistake_price FROM `Habits` WHERE `OwnerID`=' . $userid));
    if ($totalbalance < $UserHabits['Mistake_price'] && (new DateTime('now', new DateTimeZone($ts)))->format('H') == 9) {

        if ($Language == 0) {
            Longman\TelegramBot\Request::sendMessage([
              'chat_id' => $userid,
              'text' => str_replace('$2', $UserHabits['Mistake_price'], str_replace('$1', $totalbalance, (string) $LanguageR->InformText))]);
        } else {
            Longman\TelegramBot\Request::sendMessage([
              'chat_id' => $userid,
              'text' => str_replace('$2', $UserHabits['Mistake_price'], str_replace('$1', $totalbalance, (string) $LanguageE->InformText))]);
        }
    }
}
