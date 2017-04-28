
<?php
    function dbConnect(){
        try{
            
            $host = "localhost";
            $dbname = "baza";
            $mysql = "mysql:host=$host;dbname=$dbname";
            $username = 'root';
            $password = '';
            $conn = new pdo($mysql, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;

        }   catch(PDOException $e){
            echo 'ERROR', $e->getMessage();
        }
    }
?>

