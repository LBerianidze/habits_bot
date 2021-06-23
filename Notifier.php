<?php

include('vendor/autoload.php');
$telegram = new Longman\TelegramBot\Telegram("", "");

$sqlcon = new mysqli('localhost', '', '', '');
mysqli_set_charset($sqlcon, 'UTF8');
$all_users = $sqlcon->query("select * from TelegramDatabase");
$LanguageR = simplexml_load_file("Texts/Russian_cron.xml");
$LanguageE = simplexml_load_file("Texts/English_cron.xml");
foreach ($all_users as $user)
{
    $userid = $user["ID"];
    $uname = $user['name'];
    $ulanguage = $user['Language'];
    $ts = $user["Timestamp"];
    $user_habits = $sqlcon->query("select * from Habits where OwnerID='$userid'");
    if ($user_habits->num_rows > 0)
    {
        foreach ($user_habits as $habit)
        {
            $hid = $habit["ID"];
            $hname = $habit["Name"];
            $habitwarntime = $habit["WarnHours"];
            $habitwarmnhours = 0;
            $habitwarnminutes = 0;
            if ($habitwarntime != '0')
            {
                $exploded = explode(':', $habitwarntime);
                $habitwarmnhours = $exploded[0];
                if (count($exploded) == 2)
                {
                    $habitwarnminutes = $exploded[1];
                }
            }
            $habbitreportdays = $habit["ReportDays"];
            $habitReportTime = $habit["ReportTime"];
            $MistakePrice = $habit["Mistake_price"];
            $dtnow = new DateTime("now", new DateTimeZone($ts));
            $reporttime = new DateTime($habitReportTime, new DateTimeZone($ts));
            $weekday = null;
            if ($habit['Warned'] == '0' && $habit['WanrHours'] != '0')
            {
                if ($dtnow > $reporttime)
                {
                    $newday = new DateInterval('P1D');
                    $reporttime->add($newday);
                    $weekday = $reporttime->format('N') - 1;
                    $warnhours = new DateInterval("PT$habitwarmnhours" . 'H' . $habitwarnminutes . 'M');
                    $reporttime->sub($warnhours);
                    if ($dtnow > $reporttime && strpos($habbitreportdays, "$weekday") !== false)
                    {
                        $sqlcon->query("update Habits Set Warned='1' where ID=$hid");
                        if ($ulanguage == 0)
                        {
                            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $userid,
                                'text' => str_replace('$3', $MistakePrice, str_replace('$2', $habitReportTime, str_replace('$1', $hname, (string)$LanguageR->InformText)))]);
                        }
                        else
                        {
                            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $userid,
                                'text' => str_replace('$3', $MistakePrice, str_replace('$2', $habitReportTime, str_replace('$1', $hname, (string)$LanguageE->InformText)))]);
                        }
                    }
                }
                else
                {
                    $weekday = $reporttime->format('N') - 1;
                    $warnhours = new DateInterval("PT$habitwarmnhours" . 'H' . $habitwarnminutes . 'M');
                    $reporttime->sub($warnhours);
                    if ($dtnow > $reporttime && strpos($habbitreportdays, "$weekday") !== false)
                    {
                        $sqlcon->query("update Habits Set Warned='1' where ID=$hid");
                        if ($ulanguage == 0)
                        {
                            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $userid,
                                'text' => str_replace('$3', $MistakePrice, str_replace('$2', $habitReportTime, str_replace('$1', $hname, (string)$LanguageR->InformText)))]);
                        }
                        else
                        {
                            $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $userid,
                                'text' => str_replace('$3', $MistakePrice, str_replace('$2', $habitReportTime, str_replace('$1', $hname, (string)$LanguageE->InformText)))]);
                        }
                    }
                }
            }
            $reporttime = new DateTime($habitReportTime, new DateTimeZone($ts));
            $weekday = $reporttime->format('N') - 1;
            $reportedday = $reporttime->format('Y-m-d');
            if (strpos($habbitreportdays, "$weekday") !== false)
            {
                if ($dtnow > $reporttime)
                {
                    if ($habit['Step'] == 0)
                    {
                        if ($reportedday != $habit['CheckDate'])
                        {
                            if ($user['BonusBalance'] + $user['Balance'] >= $MistakePrice)
                            {
                                if ($user['BonusBalance'] >= $MistakePrice)
                                {
                                    $user['BonusBalance'] = $user['BonusBalance'] - $MistakePrice;
                                }
                                else if ($user['BonusBalance'] != 0)
                                {
                                    $user['Balance'] = $user['Balance'] - ($MistakePrice - $user['BonusBalance']);
                                    $user['BonusBalance'] = 0;
                                }
                                else
                                {
                                    $user['Balance'] = $user['Balance'] - $MistakePrice;
                                }
                                if ($ulanguage == 0)
                                {
                                    $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $userid,
                                        'text' => str_replace('$4', $user['Balance'], str_replace('$3', $user['BonusBalance'], str_replace('$2', $hname, str_replace('$1', $MistakePrice, (string)$LanguageR->Mistake))))]);
                                }
                                else
                                {
                                    $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $userid,
                                        'text' => str_replace('$4', $user['Balance'], str_replace('$3', $user['BonusBalance'], str_replace('$2', $hname, str_replace('$1', $MistakePrice, (string)$LanguageE->Mistake))))]);
                                }
                                $sqlcon->query("update Habits Set CheckDate='$reportedday',Step='0',Warned='0' where ID=$hid");
                                $dtnow = (new DateTime('now'))->format('Y-m-d H:i:s');
                                $sqlcon->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$userid','$uname','$hid','Ошибка по привычке\nTS $ts','$dtnow')");
                                if ($ulanguage == '0')
                                {
                                    SendNotDoneHabitsMenu($userid, $ts, $LanguageR);
                                }
                                else
                                {
                                    SendNotDoneHabitsMenu($userid, $ts, $LanguageE);
                                }
                            }
                        }
                    }
                    else
                    {
                        if ($reportedday != $habit['CheckDate'])
                        {
                            $sqlcon->query("update Habits Set CheckDate='$reportedday',Step='0',Warned='0' where ID=$hid");
                        }
                    }
                }
            }
        }
        $nb = $user['Balance'];
        $bnb = $user['BonusBalance'];
        $sqlcon->query("update TelegramDatabase set Balance='$nb',BonusBalance='$bnb' where ID=$userid");
    }
}

function SendNotDoneHabitsMenu($chat_id, $timestamp, $Language)
{
    global $sqlcon;
    $sqlresult = $sqlcon->query("SELECT `ID`,`Name`, `Mistake_price`,`ReportDays`, `ReportTime`, `WarnHours`, `WeeksCount`, `StartDate`,`Step` FROM `Habits` where OwnerID='$chat_id'");
    $ikb = new Longman\TelegramBot\Entities\InlineKeyboard();
    $added = false;
    $usertime = new DateTime('now', new DateTimeZone($timestamp));
    $todayday = $usertime->format('N') - 1;
    while ($row = $sqlresult->fetch_assoc())
    {
        if ($row['Step'] == '0')
        {
            $rtime = new DateTime($row['ReportTime'], new DateTimeZone($timestamp));
            $index = GetDay($todayday, $row['ReportDays'], $usertime > $rtime);
            $dcount = abs($index - $todayday);
            $rtime->add(new DateInterval('P' . $dcount . 'D'));

            $hours = ceil(($rtime->getTimestamp() - $usertime->getTimestamp()) / 3600);

            if ($hours <= 24)
            {
                $ikb->addRow(['text' => (string)$Language->WM . str_replace('$4', FormatWeekDays($index, $Language), str_replace('$3', $row['ReportTime'], str_replace('$2', $row['Mistake_price'], str_replace('$1', $row['Name'], (string)$Language->InfHabit)))), 'callback_data' => 'DoHabit_' . $row["ID"]]);
            }
            else
            {
                $ikb->addRow(['text' => str_replace('$4', FormatWeekDays($index, $Language), str_replace('$3', $row['ReportTime'], str_replace('$2', $row['Mistake_price'], str_replace('$1', $row['Name'], (string)$Language->InfHabit)))), 'callback_data' => 'DoHabit_' . $row["ID"]]);
            }
            $added = true;
        }
    }
    $ikb->setResizeKeyboard(true);
    if ($added)
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->HTC,
            'reply_markup' => $ikb]);
    }
    else
    {
        Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
            'text' => (string)$Language->NoCH]);
    }
}

function GetDay($index, $all, $more)
{
    $ind = -1;
    $exploded = explode(',', $all);

    if (strpos($all, "$index") !== false)
    {
        if ($more == true)
        {
            foreach ($exploded as $value)
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
        foreach ($exploded as $value)
        {
            if ($value > $index && strpos($all, $value) !== false)
            {
                $ind = $value;
                break;
            }
        }
    }
    return $ind;
}

function FormatWeekDays($numbers, $Language)
{
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
