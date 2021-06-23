<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $login = $_REQUEST['login'];
    $password = $_REQUEST['password'];
    if ($login == 'admin' && $password == 'ubKwp4d4JGNXWjj5')
    {
        include 'DBConfig.php';
        //$db_config  = new DBConfig();
        //$db_config->addLogIp($_SERVER['REMOTE_ADDR']);
        $cookie = md5('adminqHq54gQ5M;[ubKwp4d4JGNXWjj5]8hB5F{Q1JxTraaj5BAEJxHYmmfSGH9DWC7byu4FpTs3RevZ3jk-4QR(gt-12}');
        setcookie('user', $cookie, time() + 86400, '/');
        header("HTTP/1.1 301 Moved Permanently");
        header('location: index.php');

    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_COOKIE['user']))
    {
        $cookie = md5('adminqHq54gQ5M;[ubKwp4d4JGNXWjj5]8hB5F{Q1JxTraaj5BAEJxHYmmfSGH9DWC7byu4FpTs3RevZ3jk-4QR(gt-12}');
        $saved = $_COOKIE['user'];
        if ($cookie == $saved)
        {
            header("HTTP/1.1 301 Moved Permanently");
            header('location: index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="title icon" href="images/title-img.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/all.js"
            integrity="sha384-xymdQtn1n3lH2wcu0qhcdaOpQwyoarkgLVxC/wZ5q7h9gHtxICrpcaSUfygqZGOe"
            crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Авторизация</title>
</head>
<body>
<div class="wrapper">
    <div id="formContent">
        <form method='post'>
            <input type="text" id="login" name="login" placeholder="Логин">
            <input type="password" class="form-control" id="password" name="password" placeholder="Пароль">
            <input type="submit" value="Войти">
        </form>
        <div id="formFooter"></div>
    </div>
</div>
</body>
</html>