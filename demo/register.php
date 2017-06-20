<?php
    require("controller.php");
    $username = $_POST['username'];
    $name = $_POST['name'];
    $passwrod = $_POST['password'];
    $profile = $_POST['profile'];

    if(empty($profile)){
        $profile = NULL;
    }

    $result = register($username, $name, $password, $profile);
    echo $result;
    if($result == true){
        echo "<script>
                alert('Register success.');
                window.location.href='index.php';
            </script>";
    }
    else if($result == false){
        echo "<script>
                alert('User has registered.');
                window.location.href='index.php';
            </script>";
    }

?>