<?php
    require('connectDB.php');
    session_start();
    $user_check=$_SESSION['user'];
    $ses_sql=mysql_query("select uname from User where uname='$user_check'");
    $row = mysql_fetch_assoc($ses_sql);
    $login_session =$row['uname'];
    if(!isset($login_session)){
        mysql_close($connection); // Closing Connection
        header('Location: index.php'); // Redirecting To Home Page
    }
?>