<?php
    require("controller.php");
    session_start();

    $uname = $_SESSION['user'];
    $title = $_POST['title'];
    $rating = $_POST['rating'];
    $text = $_POST['content'];
    $suggestion = $_POST['suggestion'];
    $rid = $_GET['id'];
    addReview($rid, $uname, $rating, $title, $text, $suggestion);
    echo "<script>
            alert('Register success.');
            window.location.href='index.php';
        </script>";

?>