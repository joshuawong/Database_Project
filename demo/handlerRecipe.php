<?php
    require("controller.php");

    $title = htmlspecialchars($_POST['title']);
    $number = $_POST['numberofserving'];
    $description = $_POST['description'];
    //$image = $_FILES['fileToUpload'];
    $image = "http://localhost/demo/upload/" . $_FILES["fileToUpload"]["name"];
    $tag = array();
    $ingredient = array();
    $quantity = array();
    $unit = array();
    echo var_dump($_POST);
    function getInputType($key){
        $num = (int)($key);
        if($num > 300){
            return 2;
        }
        else if($num > 200)
            return 1;
        else
            return 0;
    }

    foreach ($_POST['tag'] as $key => $value) {
        array_push($tag, $value);
    }


    foreach ($_POST as $key => $value) {
        if($key != 'title' && $key != 'numberofserving' && $key != 'description' && $key != 'tag'){
            if(getInputType($key) == 0){
                array_push($ingredient, $value);
            }
            else if(getInputType($key) == 1){
                array_push($quantity, $value);
            }
            else{
                array_push($unit, $value);
            }
        }
        
    }
    
    if ((($_FILES["fileToUpload"]["type"] == "image/gif")
         || ($_FILES["fileToUpload"]["type"] == "image/jpeg")
         || ($_FILES["fileToUpload"]["type"] == "image/pjpeg"))
        && ($_FILES["fileToUpload"]["size"] < 200000))
    {
        if ($_FILES["fileToUpload"]["error"] > 0)
        {
            echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br />";
        }
        else
        {
            echo "Upload: " . $_FILES["fileToUpload"]["name"] . "<br />";
            echo "Type: " . $_FILES["fileToUpload"]["type"] . "<br />";
            echo "Size: " . ($_FILES["fileToUpload"]["size"] / 1024) . " Kb<br />";
            echo "Temp file: " . $_FILES["fileToUpload"]["tmp_name"] . "<br />";
            
            if (file_exists("/Applications/XAMPP/xamppfiles/htdocs/demo/upload/" . $_FILES["fileToUpload"]["name"]))
            {
                echo $_FILES["fileToUpload"]["name"] . " already exists. ";
            }
            else
            {
                move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],
                                   "/Applications/XAMPP/xamppfiles/htdocs/demo/upload/" . $_FILES["fileToUpload"]["name"]);
                echo "Stored in: " . "/Applications/XAMPP/xamppfiles/htdocs/demo/upload/" . $_FILES["fileToUpload"]["name"];
            }
        }
    }
    else
    {
        echo "Invalid file";
    }
    

    addRecipe($title, $number, $description, $tag, $ingredient, $quantity, $unit, $image);
    
?>
