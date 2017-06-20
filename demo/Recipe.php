<?php
    require('controller.php');
    session_start();
    $title = "Recipe";
    $rid = $_GET['id'];

    $content = detailTitle($rid);
    $content = $content. detailIngredient($rid);
    $content = $content. datailReview($rid);
    $content = $content. detailRelated($rid);
    if(isset($_SESSION['user'])){
        $content = $content.writeReview($rid);
    }

    include('Templates.php');
?>