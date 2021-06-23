<?php

/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 07.05.2019
 * Time: 21:54
 */
class DBConfig
{
    var $db_host = "";
    var $db_name = "";
    var $db_user = "";
    var $db_pass = "";
    var $db_con = null;

    public function __construct()
    {
        try
        {
            $this->db_con = new PDO("mysql:host={$this->db_host};dbname={$this->db_name}", $this->db_user, $this->db_pass);
            $this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db_con->exec("set names utf8");
        } catch (PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    public function addLogIp($ip)
    {
        $request = $this->db_con->prepare('INSERT INTO `logip`(`ip`) VALUES (:param1)');
        $request->execute(array(
            ':param1' => $ip));
    }

    function writeDump($item)
    {
        ob_flush();
        ob_start();
        var_dump($item);
        file_put_contents("dump.txt", ob_get_flush());
    }
}

