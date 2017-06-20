<?php
    require ('controller.php');     
    $id = $_GET['id'];
    $user = $_GET['uname'];
    showAllRecipe($id, $user);
    //showAllRecipeByUname($id, $user);
?>
