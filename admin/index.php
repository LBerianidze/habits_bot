<?php
include "check_auth.php";
?>
<!DOCTYPE html>
<html lang="en" style="background-color: white;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="title icon" href="images/title-img.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/all.js" integrity="sha384-xymdQtn1n3lH2wcu0qhcdaOpQwyoarkgLVxC/wZ5q7h9gHtxICrpcaSUfygqZGOe" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Панель управления</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <button class="navbar-toggler ml-auto mb-2 bg-light" type="button" data-toggle="collapse" data-target="#myNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="myNavbar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-2 col-lg-3 col-md-4 sidebar fixed-top">
                    <a href="#" class="navbar-brand text-white d-block mx-auto text-center py-3 mb-4 bottom-border">Панель управления</a>
                    <ul class="navbar-nav flex-column mt-4 nav" role="tablist">
                        <li class="nav-item">
                            <a class="sidebar-link nav-link text-white p-3 mb-2 active" id="nav-users-tab" data-toggle="tab" href="#nav-users">
                                <i class="fas fa-users text-light fa-lg mr-3"></i>
                                Пользователи
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link nav-link text-white p-3 mb-2" id="nav-statistics-tab" data-toggle="tab" href="#nav-statistics">
                                <i class="fas fa-database text-light fa-lg mr-3"></i>
                                Статистика
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link nav-link text-white p-3 mb-2" id="nav-filtered-users-tab" data-toggle="tab" href="#nav-filtered-users">
                                <i class="fas fa-comment-dots text-light fa-lg mr-3"></i>
                                Отправка сообщений
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link nav-link text-white p-3 mb-2" id="nav-texts-tab" data-toggle="tab" href="#nav-texts">
                                <i class="fas fa-font text-light fa-lg mr-3"></i>
                                Тексты
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-xl-10 col-lg-9 col-md-8 ml-auto bg-dark fixed-top py-2 top-navbar">
                    <div class="row align-items-center">
                        <div class="col-md">
                            <ul class="navbar-nav">
                                <li class="nav-item ml-md-auto">
                                    <a href="#" class="nav-link" data-toggle="modal" data-target="#sign-out">
                                        <i class="fas fa-sign-out-alt text-danger fa-lg"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="modal fade" id="sign-out">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Желаете выйти?</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Нажмите, чтобы выйти
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Остаться</button>
                <a class="btn btn-danger" href="signout.php" role="button">Выйти</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="show-error">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="alert alert-danger text-center" role="alert">
                Пользователь не найден
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="show-user-info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Информация о пользователе</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body table-responsive">
                <div class='table-responsive'>
                    <table class="table text-start table-borderless table-sm text-nowrap" id='user-info-table'>
                        <tr>
                            <td>Telegram ID:</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td>Имя пользователя:</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td>Имя:</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td>Дата регистрации:</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td>Баланс:</td>
                            <td>1</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Количество привычек:</td>
                            <td>1</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class='table-responsive'>
                <table class="table table-hover bg-light text-center" id='habits_table'>
                    <thead class=thead-light>
                    <tr class="text-muted">
                        <th>#</th>
                        <th>Название</th>
                        <th>Цена ошибки</th>
                        <th>Дни отчета</th>
                        <th>Время сдачи</th>
                        <th>Оповещение за</th>
                        <th>Длительность(недели)</th>
                        <th>Дата начала</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer row">
<!--                <button type="button" class="btn btn-danger col-12 col-lg-3 mx-auto" id="delete_user">Удалить пользователя
                </button>
                <button type="button" class="btn btn-success col-12 col-lg-5 mx-auto my-1">Посмотреть отзывы пользователя
                </button>-->
                <button type="button" class="btn btn-success col-12 col-lg-3 mx-auto" data-dismiss="modal">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="show-send-message">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Отправить сообщение выбранным пользователям</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <textarea class="form-control" rows="30" id="send_message_body"></textarea>
            <button type="button" class="btn btn-success col-12 col-lg-5 mx-auto my-1" id="send-message">Отправить</button>
        </div>
    </div>
</div>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-users">
        <section>
            <div class="container-fluid">
                <div class="row mb-5 pt-md-5">
                    <div class="col-xl-10 col-lg-9 col-md-12 ml-auto">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-12 mb-4 mb-xl-0">
                                <div class="row">
                                    <div class='form-group col-3 col-sm-3 col-md-3 col-lg-3 col-lg-3'>
                                        <select class="form-control" onchange="changeGroupType(this.selectedIndex);" aria-expanded="true" hidden>
                                            <option name="all">Все</option>
                                            <option name="step_1">1 этап</option>
                                            <option name="step_2">2 этап</option>
                                            <option name="step_3">3 этап</option>
                                            <option name="step_vip">VIP</option>
                                        </select>
                                    </div>
                                    <h3 class="text-muted text-center mb-3 col-9 col-sm-9 col-md-9 col-lg-9 col-lg-9">
                                        Список пользователей
                                    </h3>
                                    <h6 class="text-muted col-12">Кол-во пользователей:
                                        <span id="users_total_count">1</span>
                                    </h6>
                                    <h6 class="text-muted col-12">Кол-во пользователей за вчера:
                                        <span id="users_total_count_yesterday">5</span>
                                    </h6>
                                    <h6 class="text-muted col-12">Кол-во пользователей за сегодня:
                                        <span id="users_total_count_today">5</span>
                                    </h6>
                                </div>
                                <div class='table-responsive'>
                                    <table class="table table-hover bg-light text-center" id='users_table'>
                                        <thead class=thead-light>
                                        <tr class="text-muted">
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Баланс</th>
                                            <th>Дата регистрации</th>
                                            <th>Кол-во привычек</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <nav class="row justify-content-center">
                                    <ul class="pagination justify-content-end col-4">
                                        <li class="page-item">
                                            <a class="page-link py-2 px-3" style="user-select: none">
                                                <span>«</span>
                                            </a>
                                        </li>
                                        <li class="page-item active">
                                            <a class="page-link py-2 px-3">1</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link py-2 px-3" style="user-select: none">
                                                <span>»</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="col-4 py-2">
                                        <span id='current_index_span'>1</span>
                                        <span>of</span>
                                        <span id='page_count_span'>965</span>
                                    </div>
                                </nav>
                            </div>
                            <div class="col-xl-4 col-12 align-self-start bg-light card-common rounded p-4 my-xl-5">
                                <div class="form-group row align-items-center">
                                    <label class='col-auto' for="username_input">Имя пользователя:</label>
                                    <input type="email" class="form-control col" id="username_input" aria-describedby="emailHelp" placeholder="Введите имя пользователя">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block my-1" id='get-user-info-button'>
                                        Получить пользователя
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="nav-statistics"><!-- modal -->
        <section>
            <div class="container-fluid">
                <div class="row mb-5 pt-md-5">
                    <div class="col-xl-10 col-lg-9 col-md-12 ml-auto">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-12 mb-4 mb-xl-0">
                                <h3 class="text-muted text-center mb-3">Общая статистика</h3>
                                <div class='table-responsive'>
                                    <table class="table text-start table-borderless table-sm text-nowrap" id='total_statistics'>
                                        <tr>
                                            <td>Суммарное количество пользователей:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Пользователей выпольнивших первый этап:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Пользователей выпольнивших второй этап:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Пользователей выпольнивших третий этап:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Среднее время выполнения первого этапа:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Среднее время выполнения второго этапа:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Среднее время выполнения третьего этапа:</td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td>Действия,на которых остановились пользователи и их количество:</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class='table-responsive'>
                                    <table class="table text-start table-borderless table-sm text-nowrap" id='action_types_table'>
                                        <thead class=thead-light>
                                        <tr class="text-muted">
                                            <th>ID</th>
                                            <th>Действие</th>
                                            <th>Кол-во</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="nav-filtered-users">
        <section>
            <div class="container-fluid">
                <div class="row mb-5 pt-md-5">
                    <div class="col-xl-10 col-lg-9 col-md-12 ml-auto">
                        <div class="row align-items-center">
                            <div class="col-xl-12 col-12 mb-4 mb-xl-0">
                                <div class="row">
                                    <div class='form-group col-3 col-sm-3 col-md-3 col-lg-3 col-lg-3'>
                                        <button type="submit" class="btn btn-primary btn-block my-1" id='open-send-message-button'>
                                            Отправить сообщение
                                        </button>
                                    </div>
                                    <h3 class="text-muted text-center mb-3 col-9 col-sm-9 col-md-9 col-lg-9">
                                        Отправка сообщений
                                    </h3>
                                </div>
                                <div class='table-responsive'>
                                    <table class="table table-hover bg-light text-center" id='filtered_users_table'>
                                        <thead class=thead-light>
                                        <tr class="text-muted">
                                            <th>
                                                <input type="checkbox" class="users_table_check" checked>
                                            </th>
                                            <th>Telegram ID</th>
                                            <th>Username</th>
                                            <th>Телефон</th>
                                            <th>Имя</th>
                                            <th>Дата регистрации</th>
                                            <th>Этап</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="tab-pane fade" id="nav-texts"><!-- modal -->
        <section>
            <div class="container-fluid">
                <div class="row mb-5 pt-md-5">
                    <div class="col-xl-10 col-lg-9 col-md-12 ml-auto">
                        <div class="row align-items-center">
                            <div class="col-xl-12 col-12 mb-4 mb-xl-0">
                                <h3 class="text-muted text-center mb-3">Редактор текстов</h3>
                                <div class='form-group'>
                                    <select id="jsonKeys" class="form-control" onchange="loadItemByKey();" aria-expanded="true"></select>
                                    <textarea id="jsoneditor" cols="40" rows="30" class="form-control my-1" contenteditable="true"></textarea>
                                    <button onclick="save();" class="btn btn-primary btn-lg btn-block my-1">Сохранить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
<script src="js/script.js"></script>
</body>
</html>