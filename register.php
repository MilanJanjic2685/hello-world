<?php
session_start();

    $errors = array(
        1=>'Username or Password nisu tacni!',
        2=>'Prijavite se pre nego sto dodate ToDo',
        3=>'Username vec postoji, odaberite neko drugo!'
    );


	// provera da li je forma uredno popunjena
    if(isset($_POST['username'], $_POST['name'],$_POST['password'])){

        require 'connection.php';



        $query = dbConnect()->prepare("INSERT INTO users (username,name, password) VALUES (:username, :name,:password)");

        $query->bindParam(':username', $_POST['username']);
        
        $query->bindParam(':name', $_POST['name']);

        $query->bindParam(':password', $_POST['password']);



        if($query->execute()){

            header("Location: index.php");

        } else{

            header("Location: register.php?err=3");

        }

    }

?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Milanova ToDo lista</title>

    <!-- Bootstrap -->
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
    <link href="static/css/style.css" rel="stylesheet">

  </head>
  <body>
    
    <div class="container">
        
	<div class="row clearfix center-block">
		<div class="col-md-2 column">
		</div>
		<div class="col-md-8 column">
			<div class="page-header">
				<h1 class="text-center text-danger">
					Milanova ToDo lista
				</h1>
			</div>
		</div>
		<div class="col-md-2 column">
		</div>
	</div>        

        
        <form class="form-signin" role="form" action="register.php" method="POST">
          <h2 class="form-signin-heading text-center text-muted">Unesite vase podatke</h2><br>
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" id="inputUsername" class="form-control" placeholder="Username" name="username" required autofocus>
        <label for="inputName" class="sr-only">Ime</label>
        <input type="text" id="inputName" class="form-control" placeholder="Ime" name="name" required>        
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password" required>
        <?php if(isset($_GET['err'])){?><p class="text-danger text-center"><?=$errors[$_GET['err']]?></p><?php }?>
        <button class="btn btn-lg btn-primary btn-block btn-danger" type="submit">Registruj se</button><br>
        <a href="index.php">Prijavi se</a>
      </form>

    </div> 
    <script src="static/js/bootstrap.min.js"></script>
  </body>
</html>
