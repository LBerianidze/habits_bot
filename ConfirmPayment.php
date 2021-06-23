<?php

 include('vendor/autoload.php');
 $telegram = new Longman\TelegramBot\Telegram("", "");
 $connection = new mysqli('localhost', '', '', '');
mysqli_set_charset($sqlcon, 'UTF8');
 file_put_contents("payment.txt",file_get_contents('php://input'));
 $merchantid = $_REQUEST['MERCHANT_ORDER_ID'];
 $PaymentSql = mysqli_fetch_assoc($connection->query("select ID,OwnerID,Value from CreatedPayments where `ID`='$merchantid'"));
 $chat_id = $PaymentSql["OwnerID"];
 $UserSql = mysqli_fetch_assoc($connection->query("select ID,name,Balance from TelegramDatabase where `ID`='$chat_id'"));
 $balance = $UserSql["Balance"] + $PaymentSql["Value"];
 $value = $PaymentSql["Value"];
 $connection->query("Update TelegramDatabase SET Balance='$balance' where `ID`='$chat_id'");
 $connection->query("DELETE FROM `CreatedPayments` WHERE `CreatedPayments`.`ID` = $merchantid");
 $datetimenow = getdate()["year"] . '-' . getdate()["mon"] . '-' . getdate()["mday"];
 $connection->query("INSERT INTO `CompletedPayments`(`OwnerID`, `ID`, `Value`, `ConfirmDate`) VALUES ('$chat_id','$merchantid','$value','$datetimenow')");
 $name = $UserSql['name'];
 $mcount = $PaymentSql["Value"];
 $dtnow = (new DateTime('now'))->format('Y-m-d H:i:s');
 $connection->query("INSERT INTO `Logs`(`UserID`, `Username`, `HabitID`, `ChangeInfo`, `ChangeDateTime`) VALUES ('$chat_id','$name','-1','Баланс пополнен на $mcount руб.','$dtnow')");
 $message = Longman\TelegramBot\Request::sendMessage(['chat_id' => $chat_id,
             'text' => "Ваш баланс был пополнен на $mcount руб."]);
 