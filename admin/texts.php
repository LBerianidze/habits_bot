<?php
include "check_auth.php";
?>
<!DOCTYPE html>
<html lang="en" style="background-color: #eee;">
<head>
	<meta charset="utf-8">
    <link rel="icon" href="https://sarafan.school/files/favicon_314993.png?1563653034" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="title icon" href="images/title-img.png">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
	<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/all.js" integrity="sha384-xymdQtn1n3lH2wcu0qhcdaOpQwyoarkgLVxC/wZ5q7h9gHtxICrpcaSUfygqZGOe" crossorigin="anonymous"></script>
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
	<title>Панель управления</title>
</head>
<body>

<!-- navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
	<button class="navbar-toggler ml-auto mb-2 bg-light" type="button" data-toggle="collapse" data-target="#myNavbar">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="myNavbar">
		<div class="container-fluid">
			<div class="row">
				<!-- sidebar -->
				<div class="col-xl-2 col-lg-3 col-md-4 sidebar fixed-top">
					<a href="#" class="navbar-brand text-white d-block mx-auto text-center py-3 mb-4 bottom-border">Панель управления</a>
					<ul class="navbar-nav flex-column mt-4">
						<li class="nav-item">
							<a href="index.php" class="nav-link text-white p-3 mb-2 sidebar-link">
								<i class="fas fa-users text-light fa-lg mr-3"></i>
								Пользователи
							</a>
						</li>
						<li class="nav-item">
							<a href="statistics.php" class="nav-link text-white p-3 mb-2 sidebar-link">
								<i class="fas fa-database text-light fa-lg mr-3"></i>
								Статистика
							</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link text-white p-3 mb-2 current">
								<i class="fas fa-font text-light fa-lg mr-3"></i>
								Тексты
							</a>
						</li>
					</ul>
				</div>
				<!-- end of sidebar -->

				<!-- top-nav -->
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
				<!-- end of top-nav -->
			</div>
		</div>
	</div>
</nav>
<!-- end of navbar -->

<!-- modal -->
<div class="modal fade" id="sign-out">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Want to leave?</h4>
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
<!-- end of modal -->

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
							<button onclick="save();" class="btn btn-primary btn-lg btn-block my-1">Сохранить</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
<script src="js/script.js"></script>
</body>

</html>