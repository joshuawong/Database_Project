<?
session_start();
require_once('connectDB.php');
if (isset($_POST['username']) and isset($_POST['password'])){
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    mysql_connect($host, $user) or die(mysql_error());
    mysql_select_db($database);
    $data = mysql_query("SELECT * FROM User WHERE uname = '$username' AND password = '$password'") or die("Failed to query database".mysql_error());
    $rows = mysql_num_rows($data);
    if($rows == 1){
        $_SESSION["user"] = $username;
        header("location: index.php");
    }
    else{
        echo "<script>
        alert('Fail to login.');
        window.location.href='index.php';
        </script>";
    }
    mysql_close();
}

?>
