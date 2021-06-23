<?php

use Longman\TelegramBot\Entities\KeyboardButton;

include('vendor/autoload.php');
include './FreekassaSender.php';
var_dump(json_decode( file_get_contents("php://input")));
$telegram = new Longman\TelegramBot\Telegram('', '');
$telegram->useGetUpdatesWithoutDatabase();
$result = $telegram->handle();
$updatetype = $result->getUpdateType();
$chat_id = 0;
if ($updatetype == 'message')
{
    $chat_id = $result->getMessage()->getChat()->id;
}
else if ($updatetype == 'callback_query')
{
    $chat_id = $result->getCallbackQuery()->getFrom()->getId();
}
$sqlcon = new mysqli('localhost', '', '', '');
mysqli_set_charset($sqlcon, 'UTF8');
$UserSql = mysqli_fetch_assoc($sqlcon->query('select ID,name,Language,Balance,Timestamp,BonusBalance,HabitName,MistakePrice,ReportTime,ReportDays,WarnHours,WeeksCount,Mode,TempTimestamp,CEHabit from TelegramDatabase where `ID`=' . $chat_id));
$Language = null;
if ($UserSql['Language'] == 0)
{
    $Language = simplexml_load_file('Texts/Russian.xml');
}
else
{
    $Language = simplexml_load_file('Texts/English.xml');
}
if ($updatetype == 'message')
{
    $text = $result->getMessage()->getText();
    $name = $result->getMessage()->getFrom()->getUsername();
    if (strpos($text, '/start') == 0 && strpos($text, '/start') !== false)
    {
            $queryresult = $sqlcon->query("select * from TelegramDatabase where `ID`='$chat_id'");
            $hasreferral = false;
            if ($queryresult->num_rows < 1)
            {
                $startarray = explode(" ", $text);
                if (count($startarray) == 2 && is_numeric($startarray[1]) && $chat_id != $startarray[1])
                {
                    $insertresult = $sqlcon->query("INSERT INTO `TelegramDatabase`(`ID`, `name`,`BonusBalance`,`Referral`) VALUES ('$chat_id', '$name','5000',$startarray[1])");
                    $hasreferral = true;
                }
                else if (is_numeric($startarray[1]) && $chat_id == $startarray[1])
                {
                    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                        'text' => (string)$Language->CheatReferral]);
                }
                else
                {
                    $insertresult = $sqlcon->query("INSERT INTO `TelegramDatabase`(`ID`, `name`) VALUES ('$chat_id', '$name')");
                }
            }
            $keyboards = [];
            if ($UserSql['Language'] == 0)
            {
                $keyboards[] = new Longman\TelegramBot\Entities\Keyboard(['Внедрить привычку',
                    'Выполнить привычки'], ['Список привычек',
                    'Баланс',
                    'Settings']);
            }
            else
            {
                $keyboards[] = new Longman\TelegramBot\Entities\Keyboard(['Implant habit',
                    'Complete habits'], ['List habit',
                    'Balance',
                    'Options']);
            }
            $keyboard = $keyboards[0];
            $keyboard->setResizeKeyboard(true);
            $keyboard->setOneTimeKeyboard(false);
            $keyboard->setSelective(false);
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => str_replace("$1", 'https://t.me/HabitBotLB_bot?start=' . $chat_id, (string)$Language->EnterMessage),
                'reply_markup' => $keyboard]);
            if ($hasreferral)
            {
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->ReferralBonus]);
            }
    }
    else if ($text == '/help')
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->HelpLongMessage]);
    }
    else if ($text == 'Settings')
    {
        $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->ChangeLanguageButton,
            'callback_data' => 'CLanguage'],
            ['text' => (string)$Language->ChangeTimeStampButton,
                'callback_data' => 'CTimeZone']]);
        $ikb->addRow(['text' => (string)$Language->LeaveMenu,
            'callback_data' => 'ExitMenu']);
        $ikb->addRow([new KeyboardButton(['text' => 'message', 'request_location' => true])]);
       $result =  Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->OptionsMenuText,
            'reply_markup' => $ikb]);
       ob_start();
       var_dump($result);
    }
    else if ($text == 'Выполнить привычки' || $text == 'Complete habits')
    {
        SendNotDoneHabitsMenu();
    }
    else if ($text == 'Баланс' || $text == 'Balance')
    {
        $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->FillBalanceButton,
            'callback_data' => 'FillBalance']]);
        $ikb->addRow(['text' => (string)$Language->ActivatePromocode,
            'callback_data' => 'PromoActivate']);
        $ikb->addRow(['text' => (string)$Language->LeaveMenu,
            'callback_data' => 'ExitMenu']);
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => str_replace('$2', $UserSql['BonusBalance'], str_replace('$1', $UserSql['Balance'], $Language->CurrentBalanceText)),
            'reply_markup' => $ikb]);
        SendNotDoneHabitsMenu();
    }
    else if ($text == 'Список привычек' || $text == 'List habit')
    {
        $sqlresult = $sqlcon->query('SELECT `ID`,`Name`, `Mistake_price`,`ReportDays`, `ReportTime`, `WarnHours`, `WeeksCount`, `StartDate`,`Step` FROM `Habits` where OwnerID=' . $chat_id);
        while ($row = $sqlresult->fetch_assoc())
        {
            $ltext = str_replace('$7', $row['StartDate'], str_replace('$6', $row['WeeksCount'], str_replace('$5', $row['WarnHours'], str_replace('$4', $row['ReportTime'], str_replace('$3', FormatWeekDays($row['ReportDays']), str_replace('$2', $row['Mistake_price'], str_replace('$1', $row['Name'], (string)$Language->ListHabitsItem)))))));
            if ($row['Step'] == '1')
            {
                $ltext .= (string)$Language->AlrCompleted;
            }
            else
            {
                $ltext .= (string)$Language->NotCompletedYet;
            }
            $ikb = new Longman\TelegramBot\Entities\InlineKeyboard();
            $ikb->addRow(['text' => (string)$Language->EditHabit,
                'callback_data' => 'EditHabit_' . $row["ID"]]);
            $ikb->addRow(['text' => (string)$Language->DeleteHabit,
                'callback_data' => 'DeleteHabit_' . $row["ID"]]);
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => $ltext,
                'reply_markup' => $ikb]);
        }
        if ($sqlresult->num_rows < 1)
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->ZeroHabits]);
        }
    }
    else if ($text == 'Внедрить привычку' || $text == 'Implant habit')
    {
        if ($UserSql['Balance'] + $UserSql['BonusBalance'] >= 50)
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->EnterHabit]);
            $sqlcon->query("Update TelegramDatabase SET Mode='1',CEHabit='-1' where `ID`='$chat_id'");
        }
        else
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->NoMoney]);
        }
    }
    else if ($UserSql['Mode'] == 1)
    {
        $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->AcceptHabit,
            'callback_data' => 'AcceptHabit']]);
        $ikb->addRow(['text' => (string)$Language->ChangeReportTime,
            'callback_data' => 'ChangeReportTime']);
        $ikb->addRow(['text' => (string)$Language->ChangeExtraReportTime,
            'callback_data' => 'ChangeExtraReportTime']);
        //$ikb->addRow([
        //  'text' => (string) $Language->ChangeHabitTime,
        //  'callback_data' => 'ChangeHabitTime']);
        $ikb->addRow(['text' => (string)$Language->ChangeMistakePrice,
            'callback_data' => 'ChangeMistakePrice']);
        $ikb->addRow(['text' => (string)$Language->CancelHabit,
            'callback_data' => 'CancelHabit']);
        $ltext = str_replace('$6', "100", str_replace('$5', "11:00", str_replace('$4', "10:00", str_replace('$3', FormatWeekDays('0,1,2,3,4,5,6'), str_replace('$2', "500", str_replace('$1', $text, (string)$Language->DefaultHabitText))))));

        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => $ltext,
            'reply_markup' => $ikb]);
        $sqlcon->query("Update TelegramDatabase SET Mode='0',HabitName='$text',MistakePrice='500',ReportDays='0,1,2,3,4,5,6',ReportTime='10:00',WarnHours='1',WeeksCount='100' where `ID`='$chat_id'");
    }
    else if ($UserSql["Mode"] == 10)
    {
        if (FormatString($text) == true)
        {
            $sqlcon->query("Update TelegramDatabase SET Mode='0',ReportTime='$text' where `ID`='$chat_id'");
            $UserSql['ReportTime'] = $text;
            if ($UserSql['CEHabit'] == -1)
            {
                SendEditHabitMenu();
            }
            else
            {
                SendExistHabitEditMenu();
            }
        }
        else
        {
            $sqlcon->query("Update TelegramDatabase SET Mode='10' where `ID`='$chat_id'");
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->DateFormatException]);
        }
    }
    else if ($UserSql["Mode"] == 11)
    {
        if (!is_numeric($text))
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->NumericFormatException]);
        }
        else
        {
            if ($text >= 50 && $text <= 50000)
            {
                $sqlcon->query("Update TelegramDatabase SET Mode='0',MistakePrice='$text' where `ID`='$chat_id'");
                $UserSql["MistakePrice"] = $text;
                if ($UserSql['CEHabit'] == -1)
                {
                    SendEditHabitMenu();
                }
                else
                {
                    SendExistHabitEditMenu();
                }
            }
            else
            {
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->PMT]);
            }
        }
    }
    else if ($UserSql["Mode"] == 12)
    {
        if (!is_numeric($text))
        {
            $sqlcon->query("Update TelegramDatabase SET Mode='12' where `ID`='$chat_id'");
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->NumericFormatException]);
        }
        else
        {
            if ($text < 3 || $text > 12)
            {
                $sqlcon->query("Update TelegramDatabase SET Mode='12' where `ID`='$chat_id'");
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->OutOfIntervalException]);
            }
            else
            {
                $sqlcon->query("Update TelegramDatabase SET Mode='0',WeeksCount='$text' where `ID`='$chat_id'");
                $UserSql["WeeksCount"] = $text;
                if ($UserSql['CEHabit'] == -1)
                {
                    SendEditHabitMenu();
                }
                else
                {
                    SendExistHabitEditMenu();
                }
            }
        }
    }
    else if ($UserSql["Mode"] == 13)
    {
        if (!CheckWarnString($text))
        {
            $sqlcon->query("Update TelegramDatabase SET Mode='13' where `ID`='$chat_id'");
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->NumericFormatException]);
        }
        else
        {
            $sqlcon->query("Update TelegramDatabase SET Mode='0',WarnHours='$text' where `ID`='$chat_id'");
            $UserSql["WarnHours"] = $text;
            if ($UserSql['CEHabit'] == -1)
            {
                SendEditHabitMenu();
            }
            else
            {
                SendExistHabitEditMenu();
            }
        }
    }
    else if ($UserSql["Mode"] == 24)
    {
        if (strlen($text) == 6)
        {
            $promos = $sqlcon->query("SELECT Code,Bonus,Used FROM PromoCodes where Code='$text'");
            if ($promos->num_rows < 1)
            {
                $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->PromoNF]);
            }
            else
            {
                $promo = mysqli_fetch_assoc($promos);
                if ($promo["Used"] == 1)
                {
                    $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                        'text' => (string)$Language->PromoIU]);
                }
                else
                {
                    $totalbonusbalance = $UserSql["BonusBalance"] + $promo["Bonus"];
                    $dtnow = (new DateTime('now', new DateTimeZone($UserSql['Timestamp'])))->format('Y-m-d H:i:s');
                    $query = "Update TelegramDatabase SET Mode='0',BonusBalance='$totalbonusbalance' where `ID`='$chat_id';";
                    $query .= "Update PromoCodes SET Used='1' where `Code`='$text';";
                    $query .= "INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$name','-1','Активирован промокод $text','$dtnow');";
                    $sqlcon->multi_query($query);
                    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                        'text' => str_replace("$1", $promo["Bonus"], (string)$Language->PromoSA)]);
                }
            }
        }
        else
        {
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->PromoLP]);
        }
    }
    else if ($UserSql["Mode"] == 26)
    {
        $sp = explode(":", $text);
        if (count($sp) != 2)
        {
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->DateFormatException]);
        }
        else if (!is_numeric($sp[0]) || !is_numeric($sp[1]) || strlen($sp[1]) != 2)
        {
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->DateFormatException]);
        }
        else if ($sp[0] < 0 && $sp[0] >= 24 && $sp[1] < 0 && sp[1] >= 60)
        {
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->DateFormatException]);
        }
        else
        {
            $query = "Update TelegramDatabase SET Mode='0' where `ID`='$chat_id';";
            $query .= "Update TelegramDatabase SET ReportTime='$text' where `ID`='$chat_id';";
            $sqlcon->multi_query($query);
            $UserSql["ReportTime"] = $text;
            SendEditDateTimeMenu();
        }
    }
    else if ($UserSql["Mode"] == 27)
    {
        if (!is_numeric($text))
        {
            $sqlcon->query("Update TelegramDatabase SET Mode='12' where `ID`='$chat_id'");
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->NumericFormatException]);
        }
        else
        {

            $id = microtime_float() . rand(1,100000);
            file_put_contents('time.txt',$id);
            $uri = FreekassaSender::RequestSender($id, $text);
            $sqlcon->query("INSERT INTO `CreatedPayments`(`OwnerID`, `ID`,`Value`) VALUES ('$chat_id', '$id','$text')");
            $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->Pay,
                'url' => $uri]]);
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->PayMenu,
                'reply_markup' => $ikb]);
        }
    }
    else if ($UserSql["Mode"] == 29)
    {
        if (is_string($text))
        {
            $hid = $UserSql['CEHabit'];
            $ts = $UserSql["Timestamp"];
            $res = mysqli_fetch_assoc($sqlcon->query("SELECT `Step` FROM `Habits` where ID='$hid'"));
            $dtnow = ((new DateTime("now", new DateTimeZone($ts)))->format('Y-m-d H:i:s'));
            $sqlcon->query("INSERT INTO `Proofs`(`HabitID`, `Date`,`Info`) VALUES ('$hid', '$dtnow','$text')");
            $sqlcon->query("Update TelegramDatabase SET Mode='30' where `ID`='$chat_id'");
            if ($res['Step'] == '0')
            {
                $sqlcon->query("UPDATE `Habits` SET `Step`='1',`Warned`='1' where `ID`='$hid'");
                $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$name','$hid','Пользователь сдал отчет по привычке.Ч.с $ts','$dtnow')");
                $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => "Завершить отчет",
                    'callback_data' => 'FinishHabit']]);
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->SendPhotos,
                    'reply_markup' => $ikb]);
            }
            else
            {
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->AlrCompleted]);
            }
        }
        else
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->FirstInfo]);
        }
    }
    else if ($UserSql["Mode"] == 30)
    {
        $photos = $result->getMessage()->getPhoto();
        $hid = $UserSql['CEHabit'];
        $maxproofid = mysqli_fetch_assoc($sqlcon->query('Select MAX(ID) AS \'ID\' from Proofs WHERE HabitID=' . $hid))['ID'];
        if (!is_null($photos))
        {
            if (!file_exists('Photos'))
            {
                mkdir("Photos", 0777);
            }
            $value = array_values(array_slice($photos, -1))[0];
            if ($value->getFileSize() <= 3000000)
            {
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->ImageUploaded]);
                $photoid = $value->getFileId();
                $path = Longman\TelegramBot\Request::GetFilePath($photoid);
                try
                {
                    $response = Longman\TelegramBot\Request::downloadtelegramfile($path, $chat_id);
                    if ($response['response_code'] == 200)
                    {
                        $resppath = $response["File"];
                        $sqlcon->query("INSERT INTO `Files`(`UserID`,`ProofID`,`Path`) VALUES ('$chat_id','$maxproofid','$resppath')");
                        SendNotDoneHabitsMenu();
                    }
                } catch (Exception $ex)
                {
                    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                        'text' => $ex->getMessage()]);
                }
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->ProofAccepted]);
                $sqlcon->query("Update TelegramDatabase SET Mode='0',CEHabit='-1' where `ID`='$chat_id'");
            }
            else
            {
                $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => "Завершить отчет", 'callback_data' => 'FinishHabit']]);
                Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                    'text' => (string)$Language->TBI,
                    'reply_markup' => $ikb]);
            }
        }
        else
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->SecondPhotos]);
        }
    }
    else
    {
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->UnknownCommandException]);
    }
}
else if ($updatetype == "callback_query")
{
    $text = $result->getCallbackQuery()->getData();
    $name = $result->getCallbackQuery()->getFrom()->getUsername();
    $messageid = $result->getCallbackQuery()->getMessage()->getMessageId();
    $result->getCallbackQuery()->answer();
    Longman\TelegramBot\Request::deleteMessage(['chat_id' => $chat_id,
        'message_id' => $messageid]);
    if ($text == "CLanguage")
    {
        $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => 'Русский',
            'callback_data' => 'RussianL'],
            ['text' => 'English',
                'callback_data' => 'EnglishL'],]);
        $ikb->addRow(['text' => (string)$Language->LeaveMenu,
            'callback_data' => 'ExitMenu']);
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->LanguageMenuText,
            'reply_markup' => $ikb]);
    }
    else if ($text == "RussianL")
    {
        $sqlcon->query("Update TelegramDatabase SET Language='0' where `ID`='$chat_id'");
        $keyboards = [];
        $keyboards[] = new Longman\TelegramBot\Entities\Keyboard(["Внедрить привычку",
            'Выполнить привычки'], ["Список привычек",
            "Баланс",
            "Settings"]);

        $keyboard = $keyboards[0];
        $keyboard->setResizeKeyboard(true);
        $keyboard->setOneTimeKeyboard(false);
        $keyboard->setSelective(false);
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->LCToRussian,
            'reply_markup' => $keyboard]);
        $uname = $UserSql['name'];
        $dtnow = (new DateTime('now', new DateTimeZone($UserSql['Timestamp'])))->format('Y-m-d H:i:s');
        $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$uname','-1','Изменение языка на русский','$dtnow')");
    }
    else if ($text == "EnglishL")
    {
        $keyboards = [];
        $keyboards[] = new Longman\TelegramBot\Entities\Keyboard(["Implant habit",
            'Complete habits'], ["List habit",
            "Balance",
            "Settings"]);
        $keyboard = $keyboards[0];
        $keyboard->setResizeKeyboard(true);
        $keyboard->setOneTimeKeyboard(false);
        $keyboard->setSelective(false);
        $sqlcon->query("Update TelegramDatabase SET Language='1' where `ID`='$chat_id'");
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->LCToEnglish,
            'reply_markup' => $keyboard]);
        $uname = $UserSql['name'];
        $dtnow = (new DateTime('now', new DateTimeZone($UserSql['Timestamp'])))->format('Y-m-d H:i:s');
        $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$uname','-1','Изменение языка на английский','$dtnow')");
    }
    else if ($text == "CTimeZone")
    {
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => $timestamp . $value]);
        $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => 'UTC−12:00',
            'callback_data' => 'TM_-12'],
            ['text' => 'UTC−11:00',
                'callback_data' => 'TM_-11'],
            ['text' => 'UTC−10:00',
                'callback_data' => 'TM_-10'],
            ['text' => 'UTC−09:00',
                'callback_data' => 'TM_-09'],]);
        $ikb->addRow(['text' => 'UTC−08:00',
            'callback_data' => 'TM_-08'], ['text' => 'UTC−07:00',
            'callback_data' => 'TM_-07'], ['text' => 'UTC−06:00',
            'callback_data' => 'TM_-06'], ['text' => 'UTC−05:00',
            'callback_data' => 'TM_-05']);
        $ikb->addRow(['text' => 'UTC−04:00',
            'callback_data' => 'TM_-04'], ['text' => 'UTC−03:00',
            'callback_data' => 'TM_-03'], ['text' => 'UTC−02:00',
            'callback_data' => 'TM_-02'], ['text' => 'UTC−01:00',
            'callback_data' => 'TM_-01']);
        $ikb->addRow(['text' => 'UTC+00:00',
            'callback_data' => 'TM_+00'], ['text' => 'UTC+01:00',
            'callback_data' => 'TM_+01'], ['text' => 'UTC+02:00',
            'callback_data' => 'TM_+02'], ['text' => 'UTC+03:00',
            'callback_data' => 'TM_+03']);
        $ikb->addRow(['text' => 'UTC+04:00',
            'callback_data' => 'TM_+04'], ['text' => 'UTC+05:00',
            'callback_data' => 'TM_+05'], ['text' => 'UTC+06:00',
            'callback_data' => 'TM_+06'], ['text' => 'UTC+07:00',
            'callback_data' => 'TM_+07']);
        $ikb->addRow(['text' => 'UTC+08:00',
            'callback_data' => 'TM_+08'], ['text' => 'UTC+09:00',
            'callback_data' => 'TM_+09'], ['text' => 'UTC+10:00',
            'callback_data' => 'TM_+10'], ['text' => 'UTC+11:00',
            'callback_data' => 'TM_+11']);
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => str_replace('$1', $UserSql['Timestamp'], (string)$Language->TimeStampMenuText),
            'reply_markup' => $ikb]);
    }
    else if ($text == "ChangeReportTime")
    {
        SendEditDateTimeMenu();
    }
    else if ($text == "ChangeDays")
    {
        SendDaysMainMenu();
    }
    else if ($text == "ChangeTime")
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->TimeFormat]);
        $sqlcon->query("Update TelegramDatabase SET Mode='26' where `ID`='$chat_id'");
    }
    else if ($text == "SetOwnDays")
    {
        SendDaysMenu();
    }
    else if ($text == 'FinishHabit')
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->ProofAccepted]);
        $sqlcon->query("Update TelegramDatabase SET Mode='0',CEHabit='-1' where `ID`='$chat_id'");
        SendNotDoneHabitsMenu();
    }
    else if ($text == "FillBalance")
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->EnterFillValue]);
        $sqlcon->query("Update TelegramDatabase SET Mode='27' where `ID`='$chat_id'");
    }
    else if (strpos($text, 'Add_') == 0 && strpos($text, 'Add_') !== false)
    {
        $array = explode("_", $text);
        if (strpos($UserSql["ReportDays"], $array[1]) === false)
        {
            $UserSql["ReportDays"] = $UserSql["ReportDays"] . ',' . $array[1];
        }
        $insert = $UserSql["ReportDays"];
        $sqlcon->query("Update TelegramDatabase SET ReportDays='$insert' where `ID`='$chat_id'");
        SendDaysMenu();
    }
    else if (strpos($text, 'DoHabit') === 0)
    {
        $hid = explode('_', $text)[1];
        $res = mysqli_fetch_assoc($sqlcon->query("SELECT `Step` FROM `Habits` where ID='$hid'"));
        $ts = $UserSql["Timestamp"];
        $dtnow = new DateTime("now", new DateTimeZone($ts));

        if ($res['Step'] == '0')
        {
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->EnterProof]);
            $sqlcon->query("Update TelegramDatabase SET Mode='29',CEHabit='$hid' where `ID`='$chat_id'");
        }
        else
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->AlrCompleted]);
        }
    }
    else if (strpos($text, 'DeleteHabit') == 0 && strpos($text, 'DeleteHabit') !== false)
    {
        $id = explode('_', $text)[1];
        $sqlcon->query("Update TelegramDatabase SET Mode='28',CEHabit='$id' where `ID`='$chat_id'");
        $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->COD,
            'callback_data' => "COD_$id"],
            ['text' => (string)$Language->CAD,
                'callback_data' => "CAD_$id"]]);
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->DeleteWarning,
            'reply_markup' => $ikb]);
    }
    else if (strpos($text, "COD") === 0)
    {
        $id = explode("_", $text)[1];
        $sqlcon->query("Delete from `Habits` where `ID`='$id'");
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->HDeleted]);
    }
    else if (strpos($text, 'Rem_') == 0 && strpos($text, 'Rem_') !== false)
    {
        $array = explode("_", $text);
        if (strpos($UserSql["ReportDays"], $array[1]) !== false)
        {
            $sparray = explode(",", $UserSql["ReportDays"]);
            if (count($sparray) != 1)
            {
                unset($sparray[array_search($array[1], $sparray)]);
                $UserSql["ReportDays"] = implode(",", $sparray);
            }
        }
        $insert = $UserSql["ReportDays"];
        $sqlcon->query("Update TelegramDatabase SET ReportDays='$insert' where `ID`='$chat_id'");
        SendDaysMenu();
    }
    else if (strpos($text, 'EditHabit_') == 0 && strpos($text, 'EditHabit_') !== false)
    {
        $hid = explode('_', $text)[1];
        $sqlresult = mysqli_fetch_assoc($sqlcon->query("SELECT `ID`,`Name`, `Mistake_price`,`ReportDays`, `ReportTime`, `WarnHours`, `WeeksCount` FROM `Habits` where ID='$hid'"));
        $habitname = $sqlresult["Name"];
        $MistakePrice = $sqlresult["Mistake_price"];
        $ReportDays = $sqlresult["ReportDays"];
        $ReportTime = $sqlresult["ReportTime"];
        $WarnHours = $sqlresult["WarnHours"];
        $WeeksCount = $sqlresult["WeeksCount"];

        $UserSql["HabitName"] = $habitname;
        $UserSql["MistakePrice"] = $MistakePrice;
        $UserSql["ReportDays"] = $ReportDays;
        $UserSql["ReportTime"] = $ReportTime;
        $UserSql["WarnHours"] = $WarnHours;
        $UserSql["WeeksCount"] = $WeeksCount;

        $sqlcon->query("Update TelegramDatabase SET CEHabit='$hid',HabitName='$habitname',MistakePrice='$MistakePrice',ReportDays='$ReportDays'," . "ReportTime='$ReportTime',WarnHours='$WarnHours',WeeksCount='$WeeksCount' where `ID`='$chat_id'");
        SendExistHabitEditMenu($hid);
    }
    else if ($text == "AcceptReportDays")
    {
        SendEditDateTimeMenu();
    }
    else if ($text == "AcceptDateTime")
    {
        if ($UserSql['CEHabit'] == -1)
        {
            SendEditHabitMenu();
        }
        else
        {
            SendExistHabitEditMenu();
        }
    }
    else if ($text == "SetDaily")
    {
        $sqlcon->query("Update TelegramDatabase SET ReportDays='0,1,2,3,4,5,6' where `ID`='$chat_id'");
        $UserSql["ReportDays"] = "0,1,2,3,4,5,6";
        SendEditDateTimeMenu();
    }
    else if ($text == "SetWorkDays")
    {
        $sqlcon->query("Update TelegramDatabase SET ReportDays='0,1,2,3,4' where `ID`='$chat_id'");
        $UserSql["ReportDays"] = "0,1,2,3,4";
        SendEditDateTimeMenu();
    }
    else if ($text == "ChangeMistakePrice")
    {
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->PricePerMistake]);
        $sqlcon->query("Update TelegramDatabase SET Mode='11' where `ID`='$chat_id'");
    }
    else if ($text == "ChangeExtraReportTime")
    {
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->ExtraReport]);
        $sqlcon->query("Update TelegramDatabase SET Mode='13' where `ID`='$chat_id'");
    }
    else if ($text == "ChangeHabitTime")
    {
        $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->HabitStoreTime]);
        $sqlcon->query("Update TelegramDatabase SET Mode='12' where `ID`='$chat_id'");
    }
    else if ($text == "AcceptHabit")
    {
        $ts = $UserSql["Timestamp"];
        $dtnow = (new DateTime("now", new DateTimeZone($ts)));
        $habitname = $UserSql["HabitName"];
        $MistakePrice = $UserSql["MistakePrice"];
        $ReportDays = $UserSql["ReportDays"];
        $ReportTime = $UserSql["ReportTime"];
        $WarnHours = $UserSql["WarnHours"];
        $WeeksCount = $UserSql["WeeksCount"];
        $Username = $UserSql['name'];
        if ($UserSql['CEHabit'] == -1)
        {
            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->HabitAdded]);
            if ($UserSql["BonusBalance"] >= 50)
            {
                $newbonusbalance = $UserSql["BonusBalance"] - 50;
                $sqlcon->query("Update TelegramDatabase SET BonusBalance='$newbonusbalance' where `ID`='$chat_id'");
            }
            else if ($UserSql["BonusBalance"] != 0)
            {
                $newbonusbalance = $UserSql["Balance"] - (50 - $UserSql["BonusBalance"]);
                $sqlcon->query("Update TelegramDatabase SET Balance='$newbonusbalance',BonusBalance='0' where `ID`='$chat_id'");
            }
            else
            {
                $newbonusbalance = $UserSql["Balance"] - 50;
                $sqlcon->query("Update TelegramDatabase SET Balance='$newbonusbalance' where `ID`='$chat_id'");
            }
            $step = 0;
            $weekday = $dtnow->format('N') - 1;
            if ($dtnow > new DateTime($ReportTime, new DateTimeZone($ts)) && strpos($ReportDays, "$weekday") !== false)
            {
                $step = 1;
            }
            $dt = $dtnow->format('Y-m-d');
            $sqlcon->query("INSERT INTO `Habits` (`ID`, `Name`,`owner_name`, `OwnerID`, `Mistake_price`,`ReportDays`,`ReportTime`, `WarnHours`, `WeeksCount`, `StartDate`, `Step`) VALUES " . "(NULL, '$habitname','$Username', '$chat_id', '$MistakePrice','$ReportDays', '$ReportTime', '$WarnHours', '$WeeksCount', '$dt',  '$step');");
            $dt = $dtnow->format('Y-m-d H:i:s');
            $maxproofid = mysqli_fetch_assoc($sqlcon->query('Select MAX(ID) AS \'ID\' from Habits WHERE OwnerID=' . $chat_id))['ID'];
            $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$Username','$maxproofid','Добавлена привычка','$dt')");
        }
        else
        {
            $hid = $UserSql["CEHabit"];
            $hresult = mysqli_fetch_assoc($sqlcon->query("select * from Habits where ID=$hid"));
            $info = '';
            if ($hresult['Mistake_price'] != $MistakePrice)
            {
                $was = $hresult['Mistake_price'];
                $info .= "Цена за ошибку по привычки изменена с $was на $MistakePrice\r";
            }
            if ($hresult['ReportDays'] != $ReportDays)
            {
                $was = $hresult['ReportDays'];
                $info .= "Дни отчета изменены с $was на $ReportDays\r";
            }
            if ($hresult['ReportTime'] != $ReportTime)
            {
                $was = $hresult['ReportTime'];
                $info .= "Время отчета изменено с $was на $ReportTime\r";
            }
            if ($hresult['WarnHours'] != $WarnHours)
            {
                $was = $hresult['WarnHours'];
                $info .= "Время предупреждения изменено с $was на $WarnHours\r";
            }
            if ($hresult['WeeksCount'] != $WeeksCount)
            {
                $was = $hresult['WeeksCount'];
                $info .= "Длительность привычки в неделях изменена с $was на $WeeksCount";
            }
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => $info]);
            $dtnow = (new DateTime('now', new DateTimeZone($UserSql['Timestamp'])))->format('Y-m-d H:i:s');
            $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$Username','$hid','$info','$dtnow')");
            $sqlcon->query("Update TelegramDatabase SET CEHabit='-1' where `ID`='$chat_id'");
            $sqlcon->query("Update Habits SET Mistake_price='$MistakePrice',ReportDays='$ReportDays',ReportTime='$ReportTime',WarnHours='$WarnHours',WeeksCount='$WeeksCount' where `ID`='$hid'");
        }
        SendNotDoneHabitsMenu();
    }
    else if (substr($text, 0, 2) === 'TM')
    {
        $habits = $sqlcon->query('SELECT `ID`, `ReportDays`, `ReportTime`, `Step`, `CheckDate` FROM `Habits` WHERE `OwnerID`=' . $chat_id);
        $ts = explode('_', $text)[1];
        if (strlen($ts) == 3 && $ts[1] == '0')
        {
            $ts = $ts[0] . $ts[2];
        }
        $needwarning = false;
        while ($row = $habits->fetch_assoc())
        {
            if ($row['Step'] == 0)
            {
                $newtime = new DateTime('now', new DateTimeZone($ts));
                $reporttime = new DateTime($row['ReportTime'], new DateTimeZone($UserSql['Timestamp']));

                if (($newtime->getTimestamp() + $newtime->getOffset()) - ($reporttime->getTimestamp() + $reporttime->getOffset()) > 0)
                {
                    $needwarning = true;
                    break;
                }
            }
        }
        if ($needwarning === FALSE)
        {
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                'text' => (string)$Language->TimeStampWasChanged . $ts]);
            $sqlcon->query("Update TelegramDatabase SET Timestamp='$ts' where `ID`='$chat_id'");
            $dtnow = (new DateTime('now', new DateTimeZone($UserSql['Timestamp'])))->format('Y-m-d H:i:s');
            $was = $UserSql['Timestamp'];
            $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$name','-1','Изменение часового пояса с $was на $ts','$dtnow')");
        }
        else
        {
            $ikb = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->CASU, 'callback_data' => 'CTMA'], ['text' => (string)$Language->CASC, 'callback_data' => 'Empty']]);
            Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id, 'text' => (string)$Language->CAS, 'reply_markup' => $ikb]);
            $sqlcon->query("Update TelegramDatabase SET TempTimestamp='$ts' where `ID`='$chat_id'");
        }
    }
    else if ($text == 'CTMA')
    {
        $temtts = $UserSql['TempTimestamp'];
        $ts = $UserSql['Timestamp'];
        $sqlcon->query("Update TelegramDatabase SET Timestamp='$temtts' where `ID`='$chat_id'");
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->TimeStampWasChanged . $temtts]);
        $dtnow = (new DateTime('now', new DateTimeZone($UserSql['Timestamp'])))->format('Y-m-d H:i:s');
        $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$name','-1','Изменение часового пояса с $ts на $temtts','$dtnow')");
        $habits = $sqlcon->query('SELECT `ID`,`Name`, `ReportDays`, `ReportTime`, `Step`, `CheckDate` FROM `Habits` WHERE `OwnerID`=' . $chat_id);
        while ($row = $habits->fetch_assoc())
        {
            if ($row['Step'] == 0)
            {
                $newtime = new DateTime('now', new DateTimeZone($temtts));
                $reporttime = new DateTime($row['ReportTime'], new DateTimeZone($UserSql['Timestamp']));
                if (($newtime->getTimestamp() + $newtime->getOffset()) - ($reporttime->getTimestamp() + $reporttime->getOffset()) > 0)
                {
                    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
                        'text' => "Провал по привычке " . $row['Name']]);
                }
            }
        }
    }
    else if ($text == 'PromoActivate')
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->PromoText]);
        $sqlcon->query("Update TelegramDatabase SET Mode='24' where `ID`='$chat_id'");
    }
}

function GetDay($index, $all, $more)
{
    $ind = -1;
    $allreportdays = explode(',', $all);

    if (strpos($all, $index) !== false)
    {
        if ($more == true)
        {
            foreach ($allreportdays as $value)
            {
                if ($value > $index && strpos($all, $value) !== false)
                {
                    $ind = $value;
                    break;
                }
            }
        }
        else
        {
            $ind = $index;
        }
    }
    else
    {
        foreach ($allreportdays as $value)
        {
            if ($value > $index && strpos($all, $value) !== false)
            {
                $ind = $value;
                break;
            }
        }
    }
    if ($ind == -1)
    {
        $ind = GetDay("0", $all, $more);
    }

    return $ind;
}

function SendNotDoneHabitsMenu()
{
    global $UserSql;
    global $chat_id;
    global $Language;
    global $sqlcon;
    $sqlresult = $sqlcon->query('SELECT `ID`,`Name`, `Mistake_price`,`ReportDays`, `ReportTime`, `WarnHours`, `WeeksCount`, `StartDate`,`Step` FROM `Habits` where OwnerID=' . $chat_id);
    $ikb = new Longman\TelegramBot\Entities\InlineKeyboard();
    $added = false;
    $usertime = new DateTime('now', new DateTimeZone($UserSql['Timestamp']));
    $todayday = $usertime->format('N') - 1;
    while ($row = $sqlresult->fetch_assoc())
    {
        if ($row['Step'] == '0')
        {
            $rtime = new DateTime($row['ReportTime'], new DateTimeZone($UserSql['Timestamp']));
            $index = GetDay($todayday, $row['ReportDays'], $usertime > $rtime);
            $dcount = abs($index - $todayday);
            $rtime->add(new DateInterval('P' . $dcount . 'D'));

            $hours = ceil(($rtime->getTimestamp() - $usertime->getTimestamp()) / 3600);
            if ($hours <= 24)
            {
                $ikb->addRow(['text' => (string)$Language->WM . str_replace('$4', FormatWeekDays($index), str_replace('$3', $row['ReportTime'], str_replace('$2', $row['Mistake_price'], str_replace('$1', $row['Name'], (string)$Language->InfHabit)))), 'callback_data' => 'DoHabit_' . $row["ID"]]);
            }
            else
            {
                $ikb->addRow(['text' => str_replace('$4', FormatWeekDays($index), str_replace('$3', $row['ReportTime'], str_replace('$2', $row['Mistake_price'], str_replace('$1', $row['Name'], (string)$Language->InfHabit)))), 'callback_data' => 'DoHabit_' . $row["ID"]]);
            }
            $added = true;
        }
    }
    $ikb->setResizeKeyboard(true);
    if ($added)
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id, 'text' => (string)$Language->HTC, 'reply_markup' => $ikb]);
    }
    else
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id, 'text' => (string)$Language->NoCH]);
    }
}

function SendExistHabitEditMenu()
{
    global $chat_id;
    global $UserSql;
    global $Language;
    $inline_keyboard = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->SHChanges, 'callback_data' => 'AcceptHabit']]);
    $inline_keyboard->addRow(['text' => (string)$Language->ChangeMistakePrice, 'callback_data' => 'ChangeMistakePrice']);
    $inline_keyboard->addRow(['text' => (string)$Language->ChangeReportTime, 'callback_data' => 'ChangeReportTime']);
    $inline_keyboard->addRow(['text' => (string)$Language->ChangeExtraReportTime, 'callback_data' => 'ChangeExtraReportTime']);
    //$inline_keyboard->addRow(['text' => (string) $Language->ChangeHabitTime, 'callback_data' => 'ChangeHabitTime']);
    $inline_keyboard->addRow(['text' => (string)$Language->CHChanges, 'callback_data' => 'CancelHabit']);
    $messagetosend = str_replace('$6', $UserSql["WeeksCount"], str_replace('$5', $UserSql["WarnHours"], str_replace('$4', $UserSql["ReportTime"], str_replace("$3", FormatWeekDays($UserSql["ReportDays"]), str_replace('$2', $UserSql["MistakePrice"], str_replace('$1', $UserSql["HabitName"], (string)$Language->ExistHabitEdit))))));
    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
        'text' => $messagetosend,
        'reply_markup' => $inline_keyboard]);
}

function SendEditDateTimeMenu()
{
    global $Language;
    global $chat_id;
    global $UserSql;
    $inline_keyboard = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->ChangeDays, 'callback_data' => 'ChangeDays'],
        ['text' => (string)$Language->ChangeTime, 'callback_data' => 'ChangeTime']]);
    $inline_keyboard->addRow(['text' => (string)$Language->AcceptDateTime, 'callback_data' => 'AcceptDateTime']);
    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
        'text' => str_replace('$2', $UserSql["ReportTime"], str_replace("$1", FormatWeekDays($UserSql["ReportDays"]), (string)$Language->DateTimeMenu)),
        'reply_markup' => $inline_keyboard]);
}

function SendEditHabitMenu()
{
    global $chat_id;
    global $UserSql;
    global $Language;
    $inline_keyboard = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->AcceptHabit, 'callback_data' => 'AcceptHabit']]);
    $inline_keyboard->addRow(['text' => (string)$Language->ChangeReportTime, 'callback_data' => 'ChangeReportTime']);
    $inline_keyboard->addRow(['text' => (string)$Language->ChangeExtraReportTime, 'callback_data' => 'ChangeExtraReportTime']);
    //$inline_keyboard->addRow(['text' => (string) $Language->ChangeHabitTime, 'callback_data' => 'ChangeHabitTime']);
    $inline_keyboard->addRow(['text' => (string)$Language->ChangeMistakePrice, 'callback_data' => 'ChangeMistakePrice']);
    $inline_keyboard->addRow(['text' => (string)$Language->CancelHabit, 'callback_data' => 'CancelHabit']);
    $messagetosend = str_replace('$6', $UserSql["WeeksCount"], str_replace('$5', $UserSql["WarnHours"], str_replace('$4', $UserSql["ReportTime"], str_replace("$3", FormatWeekDays($UserSql["ReportDays"]), str_replace('$2', $UserSql["MistakePrice"], str_replace('$1', $UserSql["HabitName"], (string)$Language->DefaultHabitText))))));
    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id, 'text' => $messagetosend, 'reply_markup' => $inline_keyboard]);
}

function SendDaysMenu()
{
    global $chat_id;
    global $UserSql;
    global $Language;
    $inline_keyboard = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->AMo, 'callback_data' => 'Add_0'], ['text' => (string)$Language->DMo, 'callback_data' => 'Rem_0']]);
    $inline_keyboard->addRow(['text' => (string)$Language->ATu, 'callback_data' => 'Add_1'], ['text' => (string)$Language->DTu, 'callback_data' => 'Rem_1']);
    $inline_keyboard->addRow(['text' => (string)$Language->AWe, 'callback_data' => 'Add_2'], ['text' => (string)$Language->DWe, 'callback_data' => 'Rem_2']);
    $inline_keyboard->addRow(['text' => (string)$Language->ATh, 'callback_data' => 'Add_3'], ['text' => (string)$Language->DTh, 'callback_data' => 'Rem_3']);
    $inline_keyboard->addRow(['text' => (string)$Language->AFr, 'callback_data' => 'Add_4'], ['text' => (string)$Language->DFr, 'callback_data' => 'Rem_4']);
    $inline_keyboard->addRow(['text' => (string)$Language->ASa, 'callback_data' => 'Add_5'], ['text' => (string)$Language->DSa, 'callback_data' => 'Rem_5']);
    $inline_keyboard->addRow(['text' => (string)$Language->ASu, 'callback_data' => 'Add_6'], ['text' => (string)$Language->DSu, 'callback_data' => 'Rem_6']);
    $inline_keyboard->addRow(['text' => (string)$Language->AcceptReportDays, 'callback_data' => 'AcceptReportDays']);
    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id, 'text' => str_replace('$1', FormatWeekDays($UserSql["ReportDays"]), (string)$Language->SDays), 'reply_markup' => $inline_keyboard]);
}

function FormatWeekDays($numbers)
{
    global $Language;
    $days = array();
    if (strpos($numbers, '0') !== false)
    {
        $days[] = (string)$Language->Mo;
    }
    if (strpos($numbers, '1') !== false)
    {
        $days[] = (string)$Language->Tu;
    }
    if (strpos($numbers, '2') !== false)
    {
        $days[] = (string)$Language->We;
    }
    if (strpos($numbers, '3') !== false)
    {
        $days[] = (string)$Language->Th;
    }
    if (strpos($numbers, '4') !== false)
    {
        $days[] = (string)$Language->Fr;
    }
    if (strpos($numbers, '5') !== false)
    {
        $days[] = (string)$Language->Sa;
    }
    if (strpos($numbers, '6') !== false)
    {
        $days[] = (string)$Language->Su;
    }
    return implode(",", $days);
}

function CheckWarnString($variable)
{
    if ($variable == '0')
    {
        return true;
    }
    $explodedstring = explode(':', $variable);
    if (count($explodedstring) > 2)
    {
        return false;
    }
    if ($explodedstring[0] > 12 || $explodedstring[0] < 0)
    {
        return false;
    }
    if ($explodedstring[1] < 0 || $explodedstring[1] > 59)
    {
        return false;
    }
    if (!is_numeric($explodedstring[0]) || !is_numeric($explodedstring[1]))
    {
        return false;
    }
    return true;
}

function SendDaysMainMenu()
{
    global $chat_id;
    global $Language;
    $inline_keyboard = new Longman\TelegramBot\Entities\InlineKeyboard([['text' => (string)$Language->Daily,
        'callback_data' => 'SetDaily']]);
    $inline_keyboard->addRow(['text' => (string)$Language->MoFr,
        'callback_data' => 'SetWorkDays']);
    $inline_keyboard->addRow(['text' => (string)$Language->OwnD,
        'callback_data' => 'SetOwnDays']);
    Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
        'text' => (string)$Language->DChangeMenu,
        'reply_markup' => $inline_keyboard]);
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return round(((float)$usec + (float)$sec));
}
$sqlcon->close();

