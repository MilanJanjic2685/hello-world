<?php
session_start();

    if(!isset($_POST['username']))
    {
        header("location:index.php?err=2");
    }
    
    function fetch_login_credentials($username,$password)
    {
        $query = dbConnect()->prepare("SELECT username,name, password FROM users WHERE username=:username AND password=:password");
        $query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
        $query->execute();

        if($row = $query->fetch()){
           return array(
                'username'=>$row['username'],
                'name'=> $row['name']
            );
        }  else {
            return null;
            
        }        
    }
    
    function fetch_credentials($username)
    {
        $query = dbConnect()->prepare("SELECT name FROM users WHERE username=:username");
        $query->bindParam(':username', $username);
        $query->execute();
        
        if($row = $query->fetch()){
            return $row['name'];
        } 
    }
    
    function fetch_todos($username)
    {
            $query = dbConnect()->prepare("SELECT id,title,datum, grupa FROM todos WHERE username=:username");
            $query->bindParam(':username', $username);
            $query->execute();             
            $todos = array();
            while($row = $query->fetch()){
                array_push($todos, array('id'=>$row['id'],'title'=>$row['title'],'datum'=>$row['datum'], 'grupa'=>$row['grupa']));
            }        
            return $todos;        
    }

    if(isset($_POST['username'], $_POST['password'])){
        require 'connection.php';
        
        $credentials = fetch_login_credentials($_POST['username'], $_POST['password']);
        if(isset($credentials))
        {
            $_SESSION['username'] = $credentials['username'];
            $_SESSION['name'] = $credentials['name'];
             $_SESSION['todos'] = fetch_todos($credentials['username']);
        } else {
            //print_r($_POST);
            //print_r($credentials);
            //header("Location:index.php?err=1");
        }
        
            
    } 
    else if(isset($_POST['username'])){
        require 'connection.php';
        
       $username = $_POST['username'];
       $name = fetch_credentials($username);
       if(isset($name)) {
           $_SESSION['name'] = $name;
           $_SESSION['username'] = $username;            
       } else {
           header("Location:index.php?err=1");
       }

        // Dodavanje nove obaveze
        if(!isset($_POST['id']))
        {
            $makerValue = $_POST['Make'];

            $maker = mysql_real_escape_string($_POST['selected_text']); 
            $query = dbConnect()->prepare("INSERT INTO todos (title,username, datum, grupa) VALUES (:title,:username,:datum,:grupa)");
            $query->bindParam(':title', $_POST['todo']);
            $query->bindParam(':username', $_POST['username']);
            $query->bindParam(':datum', $_POST['datum']);
            $query->bindParam(':grupa', $maker);
            $query->execute();
            $_SESSION['msg'] = 'Obaveza uspesno dodata na listu!';
        }
        // Brisanje obaveze
        else {
            $query = dbConnect()->prepare("DELETE FROM todos WHERE username=:username AND id=:id");
            $query->bindParam(':username', $_POST['username']);
            $query->bindParam(':id', $_POST['id']);
            $query->execute();
            $_SESSION['msg'] = 'Obaveza uspesno obrisana sa liste!';
        }
        $_SESSION['todos'] = fetch_todos($username);
    }
    
    
$username = isset($_SESSION['username'])?$_SESSION['username']:'guest';
$name = isset($_SESSION['name'])?$_SESSION['name']:'guest';
$todos = isset($_SESSION['todos'])?$_SESSION['todos']:array();
$msg = isset($_SESSION['msg'])?$_SESSION['msg']:null;
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Milanova ToDo lista</title>

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
                                    <h1 class="text-left text-danger">Cao <?php print $name;?>!</h1>
                            <a href="logout.php" class="text-center">Odjavi se</a>
			</div>
		</div>
		<div class="col-md-2 column">
		</div>
	</div>   
        
	<div class="row clearfix center-block">
		<div class="col-md-2 column">
		</div>
		<div class="col-md-8 column">
                    <?php if(isset($msg)){?><p class="text-danger text-center"><?=$msg?></p><?php }?>
                            <div class="panel panel-danger">
                              <div class="panel-heading ">Vase obaveze</div>

                        <?php if(isset($todos)) {?>
                         
                         
                        <table class="table">
                                <thead>
                                  <tr>
                                    <th>Obaveza</th>
                                    <th>Grupa</th>
                                    <th>Datum</th>
                                    <th>Izaberite opciju</th>

                                  </tr>
                                </thead>
                                <tbody>
                                  <?php foreach($todos as $todo) { ?>
                                  <tr>
                                    <td><?=$todo['title']?></td>
                                    <td><?=$todo['grupa']?></td>
                                    <td><?=$todo['datum']?></td>
                                    <td>
                                          <form action="todo.php" method="POST">
                                             <input type="hidden" name="username" value="<?=$username?>"/>
                                            <input type="hidden" name="id" value="<?=$todo['id']?>"/>
                                            <button class="btn btn-sm btn-danger" type="submit">Obrisi obavezu</button>
                                          </form>
                                    </td>
                                 
                                  </tr>
                                  <?php } ?>
                                  <tr>
                                      <form action="todo.php" method="POST">
                                        <td>
                                             
                                            <input type="text" size="40" placeholder="Upisite obavezu" name="todo"/>
                                             <input type="hidden" name="username" value="<?=$username?>"/>
                                        </td>
                                        <td>
                                         
                                                    <select id="cmbMake" name="Make"     onchange="document.getElementById('selected_text').value=this.options[this.selectedIndex].text">
                                                       <option value="0">Izaberite grupu</option>
                                                       <option value="1">Fakultet</option>
                                                       <option value="2">Posao</option>
                                                       <option value="3">Porodica</option>
                                                       <option value="4">Privatno</option>
                                                       <option value="5">Drugo</option>
                                                  </select>
                                                <input type="hidden" name="selected_text" id="selected_text" value="" />
                                        </td>
                                        <td>
                                          <input type="text" size="8" placeholder="Upisite vreme" name="datum"/>
                                        </td>
                                        <td><button class="btn btn-sm btn-success btn-lg" type="submit">Dodaj novu obavezu</button></td>
                                       </form>
                                  </tr>                                  
                                </tbody>
                              </table>
                            
                            <?php } ?>
                            </div>    
		</div> 
		<div class="col-md-2 column">
		</div>
	</div>              

    </div> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
  </body>
</html>
