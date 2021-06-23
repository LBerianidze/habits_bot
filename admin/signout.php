<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 21.05.2020
 * Time: 15:51
 */
setcookie('user', '', time() - 3600, '/');
header('location: login.php');