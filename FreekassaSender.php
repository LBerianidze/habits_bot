<?php

 /*
  * To change this license header, choose License Headers in Project Properties.
  * To change this template file, choose Tools | Templates
  * and open the template in the editor.
  */

 /**
  * Description of Free-kassa_sender
  *
  * @author Luka
  */
 $configs = array('secret' => 'fyhaoudc',
     'ShopID' => '104284');

 class FreekassaSender
 {

    public static function RequestSender($id, $value)
    {
       global $configs;
       $shopid = $configs['ShopID'];
       $secret = $configs['secret'];
       $hash   = md5("$shopid:$value:$secret:$id");
       return "http://www.free-kassa.ru/merchant/cash.php?m=$shopid&oa=$value&o=$id&s=$hash&lang=ru&i=&em=";
    }

 }
 