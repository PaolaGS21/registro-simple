<?php
class DB{
    protected static $conn;
    private function __construct(){
        try{
            self::$conn = new PDO('mysql:charset=utf8mb4;host=localhost;port=3306;dbname=usuarios','root','');
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_PERSISTENT,false);
        }
        catch(PDOException $e){
            echo "No se ha podido conectar a la base de datos";
            exit;
        }
    }
    public static function getConn(){
        if(!self::$conn){
            new DB();
        }
        return self::$conn;
    }
}

$conn = DB::getConn();


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    header("Content-Type: application/json");
    $array_devolver=[];
    $email = strtolower($_POST['email']);
    $completeName = strtolower($_POST['completeName']);
    $phoneNumber = strtolower($_POST['phoneNumber']);

    $buscar_user = $con->prepare("SELECT * FROM usuarios WHERE email = '$email' LIMIT 1");
    $buscar_user->bindParam(':email', $email, PDO::PARAM_STR);
    $buscar_user->execute();

    if($buscar_user->rowCount() == 1){
        $array_devolver['error'] = "Este mail ya existe";
        $array_devolver['is_login']= false;
    }else{
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nuevo_user = $con->prepare("INSERT INTO usuarios (email, password,completeName,phoneNumber) VALUES(:email, :password, :competeName, :phoneNumber)");
        $nuevo_user->bindParam(':email', $email, PDO::PARAM_STR);
        $nuevo_user->bindParam(':password', $password, PDO::PARAM_STR);
        $nuevo_user->bindParam(':completeName', $completeName, PDO::PARAM_STR);
        $nuevo_user->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
        $nuevo_user->execute();

        $user_id = $con->lastInsertId();
        $_SESSION['user_id']= (int) $user_id;
        $array_devolver['redirect']= ''; 
        $array_devolver['is_login']= true;
    }

    echo json_encode($array_devolver);

}else{
    exit("Salida");
}


?>