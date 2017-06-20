<?php
    require("controller.php");
    
    if(isset($_POST["search"])){
        $keyword = $_POST['search'];
        echo showRecipeForSearch($keyword);
    }
    if(isset($_POST["advancedkeyword"])){
        $keyword = $_POST['advancedkeyword'];
        $rating = $_POST['advancedrating'];
        echo searchKeyword($keyword, $rating);
    }
    ?>
