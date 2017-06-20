<?php
    // execute SQL statement
    function executeSQL($sql){
        require('connectDB.php');
        mysql_connect($host, $user) or die(mysql_error());
        mysql_select_db($database); 
        $data = mysql_query($sql) or die(mysql_error());;
        return $data;
        mysql_close();
    }

    // add recipe function
    function addRecipe($title, $number, $description, $tag, $ingredient,$quantity, $unit, $image){
        if(empty($title)){
            echo "Recipe Title cannot be empty.";
        }
        else if(empty($number)){
            echo "Number of serving cannot be empty.";
        }
        else if(empty($description)){
            echo "Description cannot be empty.";
        }
        else if(empty($_POST['tag'])){
            echo "Add at least one tag.";
        }
        else{
            $mysqli = new mysqli("localhost", "root", "", "cookzilla");
            
            $query = $mysqli -> prepare("SELECT * FROM Recipe WHERE title = '$title' AND textual_description = '$description'");
            $query ->execute();
            $result = $query -> get_result();
            $row = $result -> fetch_assoc();
            $row_cnt = $result -> num_rows;
            $query -> close();

            //if($row == 0){
            if($row_cnt == 0){
                $query = $mysqli->prepare("SELECT rid FROM Recipe order by rid DESC LIMIT 1");
                $query -> execute();
                $result = $query -> get_result();
                $rid_array = array();
                while($row = $result->fetch_assoc()){
                    array_push($rid_array, $row['rid']);
                }
                $rid = $rid_array[0] + 1;
                $query -> close();

                $query = $mysqli -> prepare("INSERT INTO Recipe VALUES ('$rid', '$title', '$number', '$description')");
                $query -> execute();
                $query -> close();

                $len = sizeof($ingredient);
                for($i = 0; $i < $len; $i++){
                    $query = $mysqli -> prepare ("SELECT iid FROM Ingredient WHERE iname = '$ingredient[$i]'");
                    $query -> execute();
                    $result = $query -> get_result();
                    $res = array();
                    while($row = $result -> fetch_assoc()){
                        array_push($res, $row['rid']);
                    }
                    $query -> close();
                    $query = $mysqli -> prepare ("INSERT INTO Contains VALUES ('$rid', '$iid[$i]', '$quantity[$i]', '$unit[$i]')");
                    $query -> execute();
                    $query -> close();
                }
                //get pid
                $query = $mysqli->prepare("SELECT pid FROM Recipe_Pic order by pid DESC LIMIT 1");
                $query -> execute();
                $result = $query -> get_result();
                $pid_array = array();
                while($row = $result->fetch_assoc()){
                    array_push($pid_array, $row['pid']);
                }
                $pid = $pid_array[0] + 1;
                $query -> close();
                
                //insert picture
                
                $query = $mysqli -> prepare("INSERT INTO Recipe_Pic VALUES ('$pid','$rid', 'PIC', '$image')");
                $query -> execute();
                $query -> close();
                // insert tag
                $len = sizeof($tag);
                $tag_array = array();
                for($i = 0; $i < 8; $i++){
                    $tag_array[$i] = 0;
                }
                for($i = 0; $i < $len; $i++){
                    if($tag[$i] == 'Italian'){
                        $tag_array[0] = 1;
                    }
                    else if($tag[$i] == 'Chinese'){
                        $tag_array[1] = 1;
                    }
                    else if($tag[$i] == 'Vegan'){
                        $tag_array[2] = 1;
                    }
                    else if($tag[$i] == 'Soup'){
                        $tag_array[3] = 1;
                    }
                    else if($tag[$i] == 'Spicy'){
                        $tag_array[4] = 1;
                    }
                    else if($tag[$i] == 'Cake'){
                        $tag_array[5] = 1;
                    }
                    else if($tag[$i] == 'Salad'){
                        $tag_array[6] = 1;
                    }
                    else if($tag[$i] == 'Seafood'){
                        $tag_array[7] = 1;
                    }
                }
                
                $query = $mysqli -> prepare("INSERT INTO Tag VALUES ('$rid','$tag_array[0]','$tag_array[1]','$tag_array[2]','$tag_array[3]','$tag_array[4]','$tag_array[5]','$tag_array[6]','$tag_array[7]')");
                $query -> execute();
                $query -> close();
                $mysqli -> close();

                // add related
                // according to tag
                // find all recipes with same tag
                $relatedTitle = array();
                for($i = 0; $i < sizeof($tag); $i++){
                    // $data4 = executeSQL("SELECT * FROM Tag WHERE $tag[$i] = 1");
                    // while($record = mysql_fetch_array($data4)){
                    //     if(!in_array($record[0], $relatedTitle)){
                    //         array_push($relatedTitle, $record[0]);
                    //     }
                    // }
                    $query = $mysqli -> prepare ("SELECT * FROM Tag WHERE $tag[$i] = 1");
                    $query -> execute();
                    $result = $query -> get_result();
                    $res = array();
                    while($row = $result -> fetch_assoc()){
                        if(!in_array($row['rid'], $relatedTitle)){
                            array_push($relatedTitle, $row['rid']);
                        }
                    }
                    $query -> close();
                }
                // all all these title to related table
                foreach ($relatedTitle as $key => $value) {
                    if($rid != $value){
                        // executeSQL("INSERT INTO Related VALUES ('$rid', '$value')");
                        $query = $mysqli -> prepare("INSERT INTO Related VALUES ('$rid', '$value')");
                        $query -> execute();
                        $query -> close();
                        $mysqli -> close();
                        // executeSQL("INSERT INTO Related VALUES ('$value', '$rid')");
                        $query = $mysqli -> prepare("INSERT INTO Related VALUES ('$value', '$rid')");
                        $query -> execute();
                        $query -> close();
                        $mysqli -> close();
                    }
                }

                header("location: index.php?id=all");
            }
            else{
                header("location: index.php?id=all");
            }
        }
    }

    // render recipe title
    function showRecipeTitle($recipe,$photo){
        $content = 
            '<h3 class = "recipeTitle" style = "cursor:pointer; height:52px" onclick="selectedRecipe('.$recipe[0].')">'.$recipe[1].'</h3>'
                    .'<h4 class = "numberofserving"> <p class="recipeTitle">Number of serving: '.$recipe[2].'</p></h4>'
                    .'<img class="recipe_pic img-thumbnail" style="width:250px;height:250px;cursor:pointer" src="'.$photo[3].'" onclick="selectedRecipe('.$recipe[0].')">';
        return $content;
    }

    function renderRecipe($recipes,$photos){
        $defaultPic = "defaultRecipeImage.png";
        $content = "";
        for($i = 0; $i < sizeof($recipes); $i++){
            
            if($i % 3 == 0){
                $content = $content.'<div class = "row">';
            }
            $content = $content.'<div class = "col-sm-4">';

            // three case: 
            // get number of image of specifed recipe
            $rid = $recipes[$i];
            $images = array();
            foreach ($photos as $key => $value) {
                if($value[1] == $rid[0]){
                    array_push($images, $value);
                }
            }
            $row = sizeof($images);
            // 1 image
            if($row != 0){
                $content = $content. showRecipeTitle($recipes[$i], $images[0]);
            }
            // no image
            else{
                $content = $content.showRecipeTitleWithDefaultImage($recipes[$i], $defaultPic);
            }
            $content = $content.'</div>'; 
            if($i % 3 == 2){
                $content = $content. '</div>';
            }
        }
        return $content;
    }



    // render all recipe
    function showAllRecipe(){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $content = "";
        // get recipes data
        $query = $mysqli->prepare("SELECT * FROM Recipe");
        $query -> execute();
        $result = $query -> get_result();
        $recipes = array();
        while($row = $result->fetch_assoc()){
            $recipe = array();
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
            array_push($recipes, $recipe);
        }
        $query -> close();
        // get photos data
        $query = $mysqli->prepare("SELECT * FROM Recipe_Pic");
        $query -> execute();
        $result = $query -> get_result();
        $photos = array();
        while($row = $result -> fetch_assoc()){
            $photo = array();
            array_push($photo, $row['pid']);
            array_push($photo, $row['rid']);
            array_push($photo, $row['pname']);
            array_push($photo, $row['pic']);
            array_push($photos, $photo);
        }
        $query -> close();
        // render recipes 
        $content = renderRecipe($recipes, $photos);
        $mysqli -> close();
        return $content;
    }

    // show user review recipe
    function showUserViewRecipe($user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Recent_View WHERE uname = '$user' ORDER BY viewtime");
        $query -> execute();
        $result = $query -> get_result();
        $row_cnt = $result -> num_rows;
        if($row_cnt < 10){
            $titles = array();
            while($row = $result -> fetch_assoc()){
                $title = array();
                array_push($title, $row['uname']);
                array_push($title, $row['rid']);
                array_push($titles, $title);
            }
        }
        else{
            $titles = array();
            for($i = 0; $i < 10; $i++){
                $record = $result -> fetch_assoc();
                array_push($title, $record['uname']);
                array_push($title, $record['rid']);
                array_push($titles, $title);
            }
        }
        $query -> close();
        
        $recipes = array();
        $photos = array();
        foreach ($titles as $key => $value) {
            
            $query = $mysqli -> prepare ("SELECT * FROM Recipe WHERE rid = '$value[1]'");
            $query -> execute();
            $result = $query -> get_result();
            while($row = $result -> fetch_assoc()){
                $recipe = array();
                array_push($recipe, $row['rid']);
                array_push($recipe, $row['title']);
                array_push($recipe, $row['number_of_serving']);
                array_push($recipe, $row['textual_description']);
                array_push($recipes, $recipe);
            }
            $query -> close();

            $query = $mysqli -> prepare ("SELECT * FROM Recipe_Pic WHERE rid = '$value[1]'");
            $query -> execute();
            $result = $query -> get_result();
            while($row = $result -> fetch_assoc()){
                $photo = array();
                array_push($photo, $row['pid']);
                array_push($photo, $row['rid']);
                array_push($photo, $row['pname']);
                array_push($photo, $row['pic']);
                array_push($photos, $photo);
            }
            $query -> close();
        }
        $mysqli -> close();
        $content = renderRecipe($recipes, $photos);
        return $content;
    } 

    // show recipe by selecting tag
    function showRecipeByTag($keyword){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $content = "";
        
        $query = $mysqli -> prepare ("SELECT * FROM Recipe WHERE rid IN (SELECT rid FROM Tag WHERE $keyword = 1)");
        $query -> execute();
        $result = $query -> get_result();
        $recipes = array();
        while($row = $result -> fetch_assoc()){
            $recipe = array();
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
            array_push($recipes, $recipe);
        }
        $query -> close();
        
        //$data = executeSQL("SELECT * FROM Recipe WHERE rid IN (SELECT rid FROM Tag WHERE $keyword = 1)");
        //$recipes = array();
        //while($record = mysql_fetch_array($data)){
        //    $recipe = array();
         //   array_push($recipe, $record[0]);
         //   array_push($recipe, $record[1]);
        //    array_push($recipe, $record[2]);
        //    array_push($recipe, $record[3]);
        //    array_push($recipes, $recipe);
       // }
        
        $photos = array();
        
        foreach ($recipes as $key => $value) {
            $query = $mysqli -> prepare ("SELECT * FROM Recipe_Pic WHERE rid = '$value[0]'");
            $query -> execute();
            $result = $query -> get_result();
            while($row = $result -> fetch_assoc()){
                $photo = array();
                array_push($photo, $row['pid']);
                array_push($photo, $row['rid']);
                array_push($photo, $row['pname']);
                array_push($photo, $row['pic']);
                array_push($photos, $photo);
            }
            $query -> close();
            //$data2 = executeSQL("SELECT * FROM Recipe_Pic WHERE rid = '$value[0]'");
            //while($record = mysql_fetch_array($data2)){
             //   $photo = array();
             //   array_push($photo, $record[0]);
             //   array_push($photo, $record[1]);
             //   array_push($photo, $record[2]);
              //  array_push($photo, $record[3]);
              //  array_push($photos, $photo);
            //}
        }
        $mysqli -> close();
        $content = renderRecipe($recipes, $photos);
        return $content;
    }

    function showRecipeForSearch($keyword){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare ("SELECT * FROM Recipe WHERE title LIKE '%$keyword%'");
        $query -> execute();
        $result = $query -> get_result();
        $recipes = array();
        while($row = $result -> fetch_assoc()){
            $recipe = array();
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
            array_push($recipes, $recipe);
        }
        $query -> close();
        
        //$data = executeSQL("SELECT * FROM Recipe WHERE title LIKE '%$keyword%'");
        //$recipes = array();
        //while($record = mysql_fetch_array($data)){
         //   $recipe = array();
         //   array_push($recipe, $record[0]);
         //   array_push($recipe, $record[1]);
          //  array_push($recipe, $record[2]);
          //  array_push($recipe, $record[3]);
         //   array_push($recipes, $recipe);
        //}
        
        
        $photos = array();
        foreach ($recipes as $key => $value) {
            
            $query = $mysqli -> prepare ("SELECT * FROM Recipe_Pic WHERE rid = '$value[0]'");
            $query -> execute();
            $result = $query -> get_result();
            while($row = $result -> fetch_assoc()){
                $photo = array();
                array_push($photo, $row['pid']);
                array_push($photo, $row['rid']);
                array_push($photo, $row['pname']);
                array_push($photo, $row['pic']);
                array_push($photos, $photo);
            }
            $query -> close();

            //$data2 = executeSQL("SELECT * FROM Recipe_Pic WHERE rid = '$value[0]'");
            //while($record = mysql_fetch_array($data2)){
            //    $photo = array();
            //    array_push($photo, $record[0]);
             //   array_push($photo, $record[1]);
             //   array_push($photo, $record[2]);
             //   array_push($photo, $record[3]);
             //   array_push($photos, $photo);
           // }
        }
        $content = renderRecipe($recipes, $photos);
        $mysqli -> close();
        return $content;
    }

    // user sign up
    function register($username, $name, $password, $profile){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $ifexist = $mysqli -> prepare("SELECT uname FROM  User WHERE uname = '$username'");
        $ifexist ->execute();
        $result = $ifexist -> get_result();
        //$row = $result -> fetch_assoc();
        $row_cnt = $result -> num_rows;
        $ifexist -> close();
        if(row_cnt >= 1){
            $mysqli -> close();
            return false;
        }
        else{
            $query = $mysqli -> prepare("INSERT INTO User VALUES ('$username', '$name', '$password', '$profile')");
            $query -> execute();
            $query -> close();
            $mysqli -> close();
            return true;
        }

        //$ifexist = executeSQL("SELECT uname FROM  User WHERE uname = '$username'");
        //if(mysql_num_rows($ifexist) >= 1){
        //    return false;
        //}
        //else{
         //   $data = executeSQL("INSERT INTO User VALUES ('$username', '$name', '$password', '$profile')");
        //return true;
        //}
    }

    function addReview($rid, $uname, $rating, $title, $text, $suggestion){
        //executeSQL("INSERT INTO Review VALUES ('$rid', '$uname', '$rating','$title', '$text', '$suggestion')");
        
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("INSERT INTO Review VALUES ('$rid', '$uname', '$rating','$title', '$text', '$suggestion')");
        $query->execute();
        $query->close();
        $mysqli->close();
    }
    
    function searchTitle($keyword){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT rid FROM Recipe WHERE title LIKE '%$keyword%'");
        $titleArray = array();
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $title = array();
            array_push($title, $row['rid']);
            array_push($titleArray, $title);
        }
        $query->close();
        $mysqli->close();
        return $titleArray;

        //$data = executeSQL("SELECT rid FROM Recipe WHERE title LIKE '%$keyword%'");
        //$titleArray = array();
        //while($record = mysql_fetch_array($data)){
        //    $title = array();
        //    array_push($title, $record['rid']);
         //   array_push($titleArray, $title);
        //}
        return $titleArray;
    }


    function averageRate($rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT avgRating FROM (SELECT rid, AVG(rating) as avgRating FROM Review GROUP BY rid) as A WHERE A.rid = '$rid'");
        $query -> execute();
        
        $rating = 0;
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $rating = $row['avgRating'];
        }
        $query -> close();
        $mysqli -> close();
        
        //$data = executeSQL("SELECT avgRating FROM (SELECT rid, AVG(rating) as avgRating FROM //Review GROUP BY rid) as A WHERE A.rid = '$rid'");
        //$rating = 0;
        //while($record = mysql_fetch_array($data)){
         //   $rating = $record[0];
       // }
        return $rating;
    }

    function searchKeyword($keyword, $rating, $tag){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        if($tag == "All"){
            $query = $mysqli -> prepare("SELECT * FROM Recipe WHERE Recipe.rid IN (SELECT rid FROM (SELECT rid, AVG(rating) as avgRate FROM Review GROUP BY rid) as A WHERE avgRate >= $rating) AND (title LIKE '%$keyword%' OR textual_description LIKE '%$keyword%');");
            $query -> execute();
            $result = $query -> get_result();
            $recipes = array();
            while($row = $result -> fetch_assoc()){
                $recipe = array();
                array_push($recipe, $row['rid']);
                array_push($recipe, $row['title']);
                array_push($recipe, $row['number_of_serving']);
                array_push($recipe, $row['textual_description']);
                array_push($recipes, $recipe);
            }
            $query -> close();
            //$data = executeSQL("SELECT * FROM Recipe WHERE Recipe.rid IN (SELECT rid FROM (SELECT rid, AVG(rating) as avgRate FROM Review GROUP BY rid) as A WHERE avgRate >= $rating) AND (title LIKE '%$keyword%' OR textual_description LIKE '%$keyword%');");
            
            //$recipes = array();
            //while($record = mysql_fetch_array($data)){
            //    $recipe = array();
            //    array_push($recipe, $record[0]);
            //    array_push($recipe, $record[1]);
            //    array_push($recipe, $record[2]);
            //    array_push($recipe, $record[3]);
            //    array_push($recipes, $recipe);
            //}
        }
        else{
            $query_1 = $mysqli -> prepare("SELECT rid FROM Tag WHERE $tag = 1");
            $query_1 -> execute();
            $result_1 = $query_1 -> get_result();
            $recipes = array();
            while($record = $result_1 -> fetch_assoc()){
                $recipe = array();
                $query_2 = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = $record[0]");
                $query_2 -> execute();
                $result_2 = $query_2 -> get_result();
                while($row = $result_2 -> fetch_assoc()){
                    array_push($recipe, $row['rid']);
                    array_push($recipe, $row['title']);
                    array_push($recipe, $row['number_of_serving']);
                    array_push($recipe, $row['textual_description']);
                    array_push($recipes, $recipe);
                }
                $query_2 -> close();
            }
            $row_cnt = $result_1 -> num_rows;
            $query_1 -> close();

            //$data = executeSQL("SELECT rid FROM Tag WHERE $tag = 1");
            //$recipes = array();
            //while($record = mysql_fetch_array($data)){
            //    $recipe = array();
            //    $data1 = executeSQL("SELECT * FROM Recipe WHERE rid = $record[0]");
            //    while($row = mysql_fetch_array($data1)){
            //        array_push($recipe, $row[0]);
            //        array_push($recipe, $row[1]);
            //        array_push($recipe, $row[2]);
            //        array_push($recipe, $row[3]);
            //        array_push($recipes, $recipe);
             //   }
           // }
           // $row = mysql_num_rows($data);
        }
        
        $photos = array();
        foreach ($recipes as $key => $value) {
            $query = $mysqli -> prepare("SELECT * FROM Recipe_Pic WHERE rid = '$value[0]'");
            $query -> execute();
            $result = $query -> get_result();
            while($row = $result -> fetch_assoc()){
                $photo = array();
                array_push($photo, $row['pid']);
                array_push($photo, $row['rid']);
                array_push($photo, $row['pname']);
                array_push($photo, $row['pic']);
                array_push($photos, $photo);
            }
            $query -> close();

            //$data2 = executeSQL("SELECT * FROM Recipe_Pic WHERE rid = '$value[0]'");
            //while($record = mysql_fetch_array($data2)){
            //    $photo = array();
            //    array_push($photo, $record[0]);
            //    array_push($photo, $record[1]);
            //    array_push($photo, $record[2]);
             //   array_push($photo, $record[3]);
             //   array_push($photos, $photo);
           // }
        }
        $mysqli -> close();
        $content = renderRecipe($recipes, $photos);
        return $content;
    }

    // to be deleted
    function showGroup(){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Groups");
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $group = array();
            array_push($group, $row['gname']);
            array_push($group, $row['gdescription']);
            array_push($groups, $group);
        }
        $query -> close();
        
        
        //$data = executeSQL("SELECT * FROM Groups");
        //$groups = array();
        //while ($record = mysql_fetch_array($data)) {
        //       $group = array();
         //      array_push($group, $record[0]);
        //       array_push($group, $record[1]);
        //       array_push($groups, $group);
       //}
        
        
        $content = "<div class = 'row'>";
        $content = $content. "<div class = 'col-sm-12'>";

        for($i = 0; $i < sizeof($groups); $i++){
            $gname = replaceWhitespace($groups[$i][0]);
            $content = $content.
                        '<h3 style="cursor:pointer" class = "groupName" id = "'.$gname.'"">'.$groups[$i][0].'</h3>'
                        .'<h4 class = "groupDescription"> <p><i>'.$groups[$i][1].'</i></p></h4>';
        }
        $content = $content .'</div>'; 
        $content = $content. '</div>';
        $mysqli->close();
        return $content;
    }

    // redirect to index page
    function selectHome(){
        showAllRecipe();
    }

    function showRecipeIngredient($ingredients){
        $content = 
        '<div class = "row">
        <div id = "ingredient" class = "col-sm-3">
            <h4>Ingredient: </h4><br>
            <table class = "table table-inverse"> 
            <thead>
            <tr>
              <th>Ingredient</th>
              <th>Quentity</th>
              <th>Unit</th>
            </tr>
            </thead><tbody>';

        for($i = 0; $i < sizeof($ingredients); $i++){
            $content = $content. 
            '<tr>
                <td>'.$ingredients[$i][1].'</td><td>'.$ingredients[$i][2].'</td><td>'.$ingredients[$i][3].'</td>
            </tr>';
        }
        $content = $content.'</tbody></table>';
        return $content;
    }

    // show recipe detail !! most important one

    // A group of function used for showing recipe details
    // include title, ingredient, review, writeReview

    function detailTitle($rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = '$rid'");
        $query -> execute();
        $result = $query -> get_result();
        $recipe = array();
        while($row = $result -> fetch_assoc()){
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
        }
        $query -> close();

        //$data = executeSQL("SELECT * FROM Recipe WHERE rid = '$rid'");
        //$recipe = array();
        //while ($record = mysql_fetch_array($data)) {
        //       array_push($recipe, $record[0]);
        //       array_push($recipe, $record[1]);
        //       array_push($recipe, $record[2]);
        //       array_push($recipe, $record[3]);
        //}

        
        $query = $mysqli -> prepare("SELECT * FROM Recipe_Pic WHERE rid = '$recipe[0]'");
        $query -> execute();
        $result = $query -> get_result();
        $photos = array();
        while($row = $result -> fetch_assoc()){
            $photo = array();
            array_push($photo, $row['pid']);
            array_push($photo, $row['rid']);
            array_push($photo, $row['pname']);
            array_push($photo, $row['pic']);
            array_push($photos, $photo);
        }
        $query -> close();

        //$data2 = executeSQL("SELECT * FROM Recipe_Pic WHERE rid = '$recipe[0]'");
        //while($record = mysql_fetch_array($data2)){
        //    $photo = array();
        //    array_push($photo, $record[0]);
        //    array_push($photo, $record[1]);
        //    array_push($photo, $record[2]);
        //    array_push($photo, $record[3]);
        //    array_push($photos, $photo);
        //}

        //get rating
        $rating = averageRate($recipe[0]);

        // render
        $content = 
        '<div class = "row">
        <div id= "recipeDetailTitle" class="col-sm-8">
            <h3 class = "recipeHeader" style = "cursor:pointer">'.$recipe[1].'</h3>
            <p> Number of serving: '.$recipe[2].'</p>
            <span class="stars">'.number_format($rating,1).'</span>
            <p class = "recipeDescription"> Description: '.$recipe[3].'</p>';

        $content = $content.'<p>Related Recipe: ';
        
        $query = $mysqli -> prepare("SELECT Recipe.rid, title FROM Recipe WHERE Recipe.rid IN (SELECT rid_2 AS rid FROM Related WHERE rid_1 = '$rid')");
        $query -> execute();
        $result = $query -> get_result();
        $relateds = array();
        while($row = $result -> fetch_assoc()){
            $related = array();
            array_push($related, $row['rid']);
            array_push($related, $row['title']);
            array_push($relateds, $related);
        }
        $query -> close();
        
        
        
        //$data2 = executeSQL("SELECT Recipe.rid, title FROM Recipe WHERE Recipe.rid IN (SELECT rid_2 AS rid FROM Related WHERE rid_1 = '$rid')");
        //$relateds = array();
       // while($record = mysql_fetch_array($data2)){
        //    $related = array();
        //    array_push($related, $record[0]);
        //    array_push($related, $record[1]);
         //   array_push($relateds, $related);
        //}
        foreach ($relateds as $key => $value) {
            $content = $content. '<a href = "#" onclick="selectedRecipe('.$value[0].')" class="label label-primary" style="margin-right:5px;">'.$value[1].'&nbsp&nbsp&nbsp</a>';
        }
        $content = $content. '</p></div>';

        $content = $content.'<div id= "recipeDetailImage" class="col-sm-4">
            <img class="recipe_pic img-thumbnail" style="width:250px;height:250px;cursor:pointer" src="'.$photos[0][3].'">
        </div>';

        $mysqli -> close();
        return $content;

    }

    function detailIngredient($rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = '$rid'");
        $query -> execute();
        $result = $query -> get_result();
        $recipe = array();
        while($row = $result -> fetch_assoc()){
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
        }
        $query -> close();
        
        
        
        //$data1 = executeSQL("SELECT * FROM Recipe WHERE rid = '$rid'");
        //$recipe = array();
        //while ($record = mysql_fetch_array($data1)) {
         //  array_push($recipe, $record[0]);
         //  array_push($recipe, $record[1]);
         //  array_push($recipe, $record[2]);
         //  array_push($recipe, $record[3]);
       //}

        $query_1 = $mysqli -> prepare("SELECT * FROM Contains WHERE rid = '$recipe[0]'");
        $query_1 -> execute();
        $result_1 = $query_1 -> get_result();
        $ingredients = array();
        while($row_1 = $result_1 -> fetch_assoc()){
            $ingredient = array();
            array_push($ingredient, $row_1['rid']);
            $temp_row = $row_1['rid'];
            $query_2 = $mysqli -> prepare("SELECT iname FROM Ingredient WHERE iid = '$temp_row'");
            $query_2 -> execute();
            $result_2 = $query_2 -> get_result();
            $iname = array();
            while($row_2 = $result_2 -> fetch_assoc()){
                array_push($iname, $row_2['iname']);
            }
            $query_2 -> close();
            array_push($ingredient, $iname[0]);
            array_push($ingredient, $row_1['quantity']);
            array_push($ingredient, $row_1['unit']);
            array_push($ingredients, $ingredient);
        }
        $query_1 -> close();
        $mysqli -> close();
        
        
        //$data2 = executeSQL("SELECT * FROM Contains WHERE rid = '$recipe[0]'");
        //$ingredients = array();
        //while($record = mysql_fetch_array($data2)){
        //    $ingredient = array();
        //    array_push($ingredient, $record[0]);
        //    $data3 = executeSQL("SELECT iname FROM Ingredient WHERE iid = '$record[1]'");
        //    $iname = array();
         //   while($row = mysql_fetch_array($data3)){
        //        array_push($iname, $row[0]);
        //    }
        //    array_push($ingredient, $iname[0]);
        //    array_push($ingredient, $record[2]);
        //    array_push($ingredient, $record[3]);
        //    array_push($ingredients, $ingredient);
       // }
        $content = showRecipeIngredient($ingredients);
        return $content;
    }

    function datailReview($rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = '$rid'");
        $query -> execute();
        $result = $query -> get_result();
        $recipe = array();
        while($row = $result -> fetch_assoc()){
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
        }
        $query -> close();
        //$data1 = executeSQL("SELECT * FROM Recipe WHERE rid = '$rid'");
        //$recipe = array();
        //while ($record = mysql_fetch_array($data1)) {
        //   array_push($recipe, $record[0]);
        //   array_push($recipe, $record[1]);
        //   array_push($recipe, $record[2]);
        //   array_push($recipe, $record[3]);
        //}

        $query_2 = $mysqli -> prepare("SELECT * FROM Review WHERE rid = '$recipe[0]'");
        $reviews = array();
        $query_2 -> execute();
        $result_2 = $query_2 -> get_result();
        while($row_2 = $result_2 -> fetch_assoc()){
            $review = array();
            array_push($review, $row_2['rid']);
            array_push($review, $row_2['uname']);
            array_push($review, $row_2['rating']);
            array_push($review, $row_2['title']);
            array_push($review, $row_2['text']);
            array_push($review, $row_2['suggestion']);
            array_push($reviews, $review);
        }
        $query_2 -> close();
        
        //$data2 = executeSQL("SELECT * FROM Review WHERE rid = '$recipe[0]'");
        //$reviews = array();
        //while($record = mysql_fetch_array($data2)){
        //    $review = array();
        //    array_push($review, $record[0]);
       //     array_push($review, $record[1]);
        //    array_push($review, $record[2]);
        //    array_push($review, $record[3]);
         //   array_push($review, $record[4]);
        //    array_push($review, $record[5]);
        //    array_push($reviews, $review);
       // }

        $content = '<div class = "col-sm-8"><h4>Review:</h4><br>';

        for($i = 0; $i < sizeof($reviews); $i++){
            $content = $content. 
            '<div class = "col-sm-5.5 review-content">
                <p>Title: '.$reviews[$i][3].'<br><span class="stars">'.number_format($reviews[$i][2],1).'</sppan><br>User: '.$reviews[$i][1].'</p>
                 <p class="review-description">Description: '.$reviews[$i][4].'</p>
                 <p>Suggestion: '.$reviews[$i][5].'</p>
            </div>';
        }
        $content = $content.'</div></div>';
        $mysqli -> close();
        return $content;
    }

    function datailReviewProfile($rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query_1 = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = '$rid'");
        $query_1 -> execute();
        $recipe = array();
        $result_1 = $query_1 -> get_result();
        while($row = $result_1 -> fetch_assoc()){
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
        }
        $query_1 -> close();
        
        //$data1 = executeSQL("SELECT * FROM Recipe WHERE rid = '$rid'");
        //$recipe = array();
        //while ($record = mysql_fetch_array($data1)) {
        //   array_push($recipe, $record[0]);
       //    array_push($recipe, $record[1]);
        //   array_push($recipe, $record[2]);
       //    array_push($recipe, $record[3]);
        //}

        $query_2 = $mysqli -> prepare("SELECT * FROM Review WHERE rid = '$recipe[0]'");
        $reviews = array();
        $query_2 -> execute();
        $result_2 = $query_2 -> get_result();
        while($row_2 = $result_2 -> fetch_assoc()){
            $review = array();
            array_push($review, $row_2['rid']);
            array_push($review, $row_2['uname']);
            array_push($review, $row_2['rating']);
            array_push($review, $row_2['title']);
            array_push($review, $row_2['text']);
            array_push($review, $row_2['suggestion']);
            array_push($reviews, $review);
        }
        $query_2 -> close();
        
        
        //$data2 = executeSQL("SELECT * FROM Review WHERE rid = '$recipe[0]'");
        //$reviews = array();
        //while($record = mysql_fetch_array($data2)){
        //    $review = array();
        //    array_push($review, $record[0]);
        //    array_push($review, $record[1]);
        //    array_push($review, $record[2]);
        //    array_push($review, $record[3]);
        //    array_push($review, $record[4]);
        //    array_push($review, $record[5]);
        //    array_push($reviews, $review);
        //}

        $content = '<div class = "col-sm-8"><h4>Review:</h4><br>';

        for($i = 0; $i < sizeof($reviews); $i++){
            $rating = number_format($reviews[$i][2],1)/5;
            $widthX = $rating * 80;
            $content = $content. 
            '<div class = "col-sm-5.5 review-content">
                <p>Title: '.$reviews[$i][3].'<br><span class="stars"><span style="width:'.$widthX.'px"></span></span>User: '.$reviews[$i][1].'</p>
                 <p class="review-description">Description: '.$reviews[$i][4].'</p>
                 <p>Suggestion: '.$reviews[$i][5].'</p>
            </div>';
        }
        $content = $content.'</div></div>';
        $mysqli -> close();
        return $content;
    }

    function detailTitleProfile($rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = '$rid'");
        $query -> execute();
        $result = $query -> get_result();
        $recipe = array();
        while($row = $result -> fetch_assoc()){
            array_push($recipe, $row['rid']);
            array_push($recipe, $row['title']);
            array_push($recipe, $row['number_of_serving']);
            array_push($recipe, $row['textual_description']);
        }
        $query -> close();
        
        
        //$data = executeSQL("SELECT * FROM Recipe WHERE rid = '$rid'");
        //$recipe = array();
        //while ($record = mysql_fetch_array($data)) {
        //       array_push($recipe, $record[0]);
        //       array_push($recipe, $record[1]);
        //       array_push($recipe, $record[2]);
         //      array_push($recipe, $record[3]);
         //  }

        
        $query -> $mysqli -> prepare("SELECT * FROM Recipe_Pic WHERE rid = '$recipe[0]'");
        $query -> execute();
        $result = $query -> get_result();
        $photos = array();
        while($row = $result -> fetch_assoc()){
            $photo = array();
            array_push($photo, $row['pid']);
            array_push($photo, $row['rid']);
            array_push($photo, $row['pname']);
            array_push($photo, $row['pic']);
            array_push($photos, $photo);
        }
        $query -> close();
        
        
        
        //$photos = array();
        //$data2 = executeSQL("SELECT * FROM Recipe_Pic WHERE rid = '$recipe[0]'");
        //while($record = mysql_fetch_array($data2)){
        //    $photo = array();
        //    array_push($photo, $record[0]);
        //    array_push($photo, $record[1]);
        //    array_push($photo, $record[2]);
        //    array_push($photo, $record[3]);
        //    array_push($photos, $photo);
       // }

        //get rating
        $rating = averageRate($recipe[0])/5;
        $widthX = $rating * 80;
        // render
        $content = 
        '<div class = "row">
        <div id= "recipeDetailTitle" class="col-sm-8">
            <h3 class = "recipeHeader" style = "cursor:pointer">'.$recipe[1].'</h3>
            <p> Number of serving: '.$recipe[2].'</p>
            <span class="stars"><span style="width:'.$widthX.'px"></span></span>
            <p class = "recipeDescription"> Description: '.$recipe[3].'</p>';

        $content = $content.'<p>Related Recipe: ';
        
        $query = $mysqli -> prepare("SELECT Recipe.rid, title FROM Recipe WHERE Recipe.rid IN (SELECT rid_2 AS rid FROM Related WHERE rid_1 = '$rid')");
        $query -> execute();
        $result = $query -> get_result();
        $relateds = array();
        while($row = $result -> fetch_assoc()){
            $related = array();
            array_push($related, $row['rid']);
            array_push($related, $row['title']);
            array_push($relateds, $related);
        }
        $query -> close();
        
        //$data2 = executeSQL("SELECT Recipe.rid, title FROM Recipe WHERE Recipe.rid IN (SELECT rid_2 AS rid FROM Related WHERE rid_1 = '$rid')");
        //$relateds = array();
        //while($record = mysql_fetch_array($data2)){
        //    $related = array();
        //    array_push($related, $record[0]);
        //    array_push($related, $record[1]);
        //    array_push($relateds, $related);
        //}
        foreach ($relateds as $key => $value) {
            $content = $content. '<a href = "#" onclick="selectedRecipe('.$value[0].')" class="label label-primary" style="margin-right:5px;">'.$value[1].'&nbsp&nbsp&nbsp</a>';
        }
        $content = $content. '</p></div>';

        $content = $content.'<div id= "recipeDetailImage" class="col-sm-4">
            <img class="recipe_pic img-thumbnail" style="width:250px;height:250px;cursor:pointer" src="'.$photos[0][3].'">
        </div>';

        
        $mysqli -> close();
        return $content;
    }

    // profile used function groups
    function userDetailReview($user, $rid){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Review WHERE uname = '$user' AND rid = '$rid'");
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $review = array();
            array_push($review, $row['rid']);
            array_push($review, $row['uname']);
            array_push($review, $row['rating']);
            array_push($review, $row['title']);
            array_push($review, $row['text']);
            array_push($review, $row['suggestion']);
            array_push($reviews, $review);
        }
        $query -> close();
        
        
        
        
        //$data = executeSQL("SELECT * FROM Review WHERE uname = '$user' AND rid = '$rid'");
        //$reviews = array();
        //while($record = mysql_fetch_array($data)){
        //    $review = array();
        //    array_push($review, $record[0]);
         //   array_push($review, $record[1]);
         //   array_push($review, $record[2]);
        //    array_push($review, $record[3]);
        //    array_push($review, $record[4]);
        //    array_push($review, $record[5]);
         //   array_push($reviews, $review);
        //}

        $content = '<div>';

        for($i = 0; $i < sizeof($reviews); $i++){
            $content = $content. 
            '<div>
                <h2>'.$reviews[$i][3].'</h2><p><br>Rate:'.number_format($reviews[$i][2],1).'/5.0<br></p>
                 <p>Description: '.$reviews[$i][4].'</p>
                 <p>Suggestion: '.$reviews[$i][5].'</p>
            </div>';
        }
        $content = $content.'</div>';
        $mysqli -> close();
        return $content;
    }

    function showGroupDetail($gname){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $gname = replaceDot($gname);
        
        $query = $mysqli -> prepare("SELECT * FROM Groups WHERE gname = '$gname'");
        $group = array();
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            array_push($group, $row['gname']);
            array_push($group, $row['gdescription']);
        }
        $query -> close();
        
        
        //$data = executeSQL("SELECT * FROM Groups WHERE gname = '$gname'");
        //$group = array();
        //while($record = mysql_fetch_array($data)){
        //    array_push($group, $record[0]);
        //    array_push($group, $record[1]);
        //}

        $query = $mysqli -> prepare("SELECT * FROM Member WHERE gname = '$gname'");
        $members = array();
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $member = array();
            array_push($member, $row['gname']);
            array_push($member, $row['uname']);
            array_push($members, $member);
        }
        $query -> close();
        
        //$data1 = executeSQL("SELECT * FROM Member WHERE gname = '$gname'");
        //$members = array();
        //while($record = mysql_fetch_array($data1)){
        //    $member = array();
         //   array_push($member, $record[0]);
         //   array_push($member, $record[1]);
         //   array_push($members, $member);
       // }


        $content = '<div>';
        $content = $content.
        '<div class= "row">
            <h2 class="col-sm-12">'.$group[0].'</h2>
            <div class="col-sm-12 row">
            <h4 class="col-sm-6">'.$group[1].'</h4>
            <div class="col-sm-5">
            <h4> Group Member</h4>
            <ul>';
        // group member
        foreach ($members as $key => $value) {
            $uname = replaceWhitespace($value[1]);
            $content = $content.'<li>'.$value[1].'</li>';
        }
        $content = $content.'</ul></div>';
        // $content = $content.showGroupEvent($events);
        
        $content = $content.'</div></div></div>';
        $mysqli -> close();
        
        return $content;
    }

    function showGroupEvent($gname){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        
        // group event
        $query = $mysqli -> prepare("SELECT * FROM Event WHERE gname = '$gname'");
        $events = array();
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $event = array();
            array_push($event, $row['gname']);
            array_push($event, $row['ename']);
            array_push($event, $row['description']);
            array_push($events, $event);
        }

        //$data2 = executeSQL("SELECT * FROM Event WHERE gname = '$gname'");
        //$events = array();
        //while($record = mysql_fetch_array($data2)){
        //    $event = array();
        //    array_push($event, $record[0]);
         //   array_push($event, $record[1]);
         //   array_push($event, $record[2]);
        //    array_push($events, $event);
        //}
        // render event
        $content = '<div>';
        $content = '<br><h4>Group Event</h4><ul>';
        foreach ($events as $key => $value) {
            $id = replaceWhitespace($value[1]);

            // render report picture
            
            


            $content = $content.'<li style="cursor:pointer" onclick="redirectToEvent(\''.$id.'\')">'.$value[1].'</li>';

            
        }
        $content = $content.'</ul>';
        $content = $content.'</div></div>';
        $mysqli -> close();
        return $content;
    }

    // join group button
    function joinGroupBTN($gname){
        $gname = replaceWhitespace($gname);
        $content = '<input type = "button" id = "'.$gname.'" class = "join" onclick="joinGroup(\''.$gname.'\')" value="JOIN">';
        return $content;
    }

    function userDetailEvent($ename){
        //showEvent
        return showEvent($ename);
    }  

    function writeReview($rid){
        $content = 
        '<button class="btn btn-default" onclick="document.getElementById(\'addReview\').style.display=\'block\'">Add Review</button>

        <div class="modal" id="addReview">
        <span onclick="document.getElementById(\'addReview\').style.display=\'none\'" 
                    class="close" title="Close Modal">&times;</span>

        <form class="modal-content animate" id= "postReview" action = "post.php?id='.$rid.'" method = "post">
            <div class="container" style="width:800px">
                <h3>Create New Review</h3>
                <label>Title: </label>
                <input class="form-control" style="width:250px" type = "text" placeholder="Title" name = "title"><br>
                <label>Rating: </label>
                <input class="form-control" style="width:250px" type = "number" placeholder="0" name = "rating" min="1" max="5"><br>
                <label>Content: </label>
                <input class="form-control" style="width:250px; height: 100px"  type = "text" name = "content"><br>
                <label>Suggestion: </label>
                <input class="form-control" style="width:250px; height:150px" type = "text" name = "suggestion"><br>
            </div>
            <div style="background-color:#f1f1f1">
                          <button class="btn btn-default" type="button" onclick="document.getElementById(\'addReview\').style.display=\'none\'" class="cancelbtn" style="margin-left:14px">Cancel</button>
                          <button class="btn btn-default" type="reset" value="Reset" class="cancelbtn">Reset</button>
                          <button type="submit" id="advanced-btn" class="btn btn-success submitBTN" onclick="document.getElementById(\'addReview\').style.display=\'none\'" >Post Recipe</button>
                        </div>
        </form>
        </div>';
        return $content;
    }

    function showUserRecipe($user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM User_Recipe WHERE uname = '$user'");
        $recipeId = array();
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $recipe = array();
            array_push($recipe, $row['uname']);
            array_push($recipe, $row['rid']);
            array_push($recipeId, $recipe);
        }
        $query -> close();

        //$data = executeSQL("SELECT * FROM User_Recipe WHERE uname = '$user'");
       // $recipeId = array();
        //while($record = mysql_fetch_array($data)){
            // echo $record[0].'and'.$record[1];
        //    $recipe = array();
        //    array_push($recipe, $record[0]);
        //    array_push($recipe, $record[1]);
        //    array_push($recipeId, $recipe);
       // }

        $recipes = array();
        $photos = array();
        foreach ($recipeId as $key => $value) {
            $query_1 = $mysqli -> prepare("SELECT * FROM Recipe WHERE rid = '$value[1]'");
            $recipe = array();
            $photo = array();
            $query_1 -> execute();
            $result_1 = $query_1 -> get_result();
            while($row_1 = $result_1 -> fetch_assoc()){
                array_push($recipe, $row_1['rid']);
                array_push($recipe, $row_1['title']);
                array_push($recipe, $row_1['number_of_serving']);
                array_push($recipe, $row_1['textual_description']);
                array_push($recipes, $recipe);
            }
            $query_1 -> close();
            $query_2 = $mysqli -> prepare("SELECT * FROM Recipe_Pic");
            $query_2 -> execute();
            $result_2 = $query_2 -> get_result();
            while($row_2 = $result_2 -> fetch_assoc()){
                $photo = array();
                array_push($photo, $row_2['pid']);
                array_push($photo, $row_2['rid']);
                array_push($photo, $row_2['pname']);
                array_push($photo, $row_2['pic']);
                array_push($photos, $photo);
            }
            $query_2 -> close();
            
            
            
            //$data1 = executeSQL("SELECT * FROM Recipe WHERE rid = '$value[1]'");
            //$recipe = array();
            //$photo = array();
            //while($record = mysql_fetch_array($data1)){
             //   // echo $record[0].' and '.$record[1];
             //   array_push($recipe, $record[0]);
             //   array_push($recipe, $record[1]);
             //   array_push($recipe, $record[2]);
             //   array_push($recipe, $record[3]);
             //   array_push($recipes, $recipe);
            //}
            //$data2 = executeSQL("SELECT * FROM Recipe_Pic");
           // while($record = mysql_fetch_array($data2)){
            //    $photo = array();
            //   array_push($photo, $record[0]);
             //   array_push($photo, $record[1]);
             //   array_push($photo, $record[2]);
              //  array_push($photo, $record[3]);
              //  array_push($photos, $photo);
            //}
        }

        $content = '<div id = "UserProfile"><h3>My Recipe</h3>';

        // for($i = 0; $i < sizeof($recipes); $i++){
        //     $content = $content.
        //         '<li><h4 style = "cursor:pointer" onclick = "showUserReviewDetail(\''.$recipes[$i][0].'\', \'recipe\')">'.$recipes[$i][1].'</h4></li>';
        // }

        $content = $content.renderRecipe($recipes, $photos);

        $content = $content.'</div>';
        $mysqli -> close();
        return $content; 
    }

    function showUserReview($user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $reviewName = array();
        $query = $mysqli -> prepare("SELECT * FROM Review WHERE uname = '$user'");
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            $review = array();
            array_push($review, $row['rid']);
            array_push($review, $record['rating']);
            array_push($reviewName, $review);
        }
        $query -> close();
        
        //$data = executeSQL("SELECT * FROM Review WHERE uname = '$user'");
        //$reviewName = array();
        //while($record = mysql_fetch_array($data)){
        //    $review = array();
        //    array_push($review, $record[0]);
        //    array_push($review, $record[3]);
        //    array_push($reviewName, $review);
        //}
        // echo $reviewName[0][0];
        $content = '<div id = "UserProfile"><h3>My Review</h3><ul>';

        for($i = 0; $i < sizeof($reviewName); $i++){
            $rid = $reviewName[$i][0];
            
            $query = $mysqli -> prepare("SELECT title FROM Recipe WHERE rid = '$rid'");
            $query -> execute();
            $result = $query -> get_result;
            $title = array();
            while($row = $result -> fetch_assoc()){
                array_push($title, $row['title']);
            }
            $query -> close();

            //$data1 = executeSQL("SELECT title FROM Recipe WHERE rid = '$rid'");
            //$title = array();
            //while($record = mysql_fetch_array($data1)){
             //   array_push($title, $record[0]);
            //}
            $content = $content.
            '<li><h3 onclick="selectedRecipe('.$rid.')" style="cursor:pointer;">'.$title[0].'</h3>';
            $content = $content.
                '<p class="btn btn-link" style = "cursor:pointer" onclick = "showUserReviewDetail(\''.$reviewName[$i][0].'\', \'review\')">'.$reviewName[$i][1].'<p></li>';
        }

        $content = $content.'</ul></div>';
        $mysqli -> close();
        return $content;
    }

    function showUserGroup($user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Member WHERE uname = '$user'");
        $query -> execute();
        $result = $query -> get_result();
        $groupName = array();
        while($row = $result -> fetch_assoc()){
            $group = array();
            array_push($group, $row['gname']);
            array_push($group, $row['uname']);
            array_push($groupName, $group);
        }
        $query -> close();

        //$data = executeSQL("SELECT * FROM Member WHERE uname = '$user'");
        //$groupName = array();
        //while($record = mysql_fetch_array($data)){
        //    $group = array();
        //    array_push($group, $record[0]);
        //    array_push($group, $record[1]);
        //    array_push($groupName, $group);
        //}

        $content = '<div id = "UserProfile"><h3>My Groups</h3><ul>';

        for($i = 0; $i < sizeof($groupName); $i++){
            $name = $groupName[$i][0];
            
            $query = $mysqli -> prepare("SELECT * FROM Groups WHERE gname = '$name'");
            $query -> execute();
            $result = $query -> get_result();
            $group = array();
            while($row = $result -> get_result()){
                array_push($group, $record['gname']);
                array_push($group, $record['gdescription']);
            }
            $query -> close();
            
            //$data1 = executeSQL("SELECT * FROM Groups WHERE gname = '$name'");
            //$group = array();
            //while($record = mysql_fetch_array($data1)){
            //    array_push($group, $record[0]);
            //    array_push($group, $record[1]);
            //}
            $gname = replaceWhitespace($group[0]);
            $content = $content.
                '<li class = "groupName" style = "cursor:pointer" onclick="showUserGroupDetail(\''.$gname.'\', \'group\')"><h4>'.$group[0].'</h3></li>';
        }

        $content = $content.'</ul></div>';
        $mysqli -> close();
        return $content;
    }

    function showUserEvent($user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM EventMember WHERE uname = '$user'");
        $query -> execute();
        $result = $query -> get_result();
        $eventName = array();
        while($row = $result -> fetch_assoc()){
            $event = array();
            array_push($event, $rpw['ename']);
            array_push($event, $row['gname']);
            array_push($eventName, $event);
        }
        $query -> close();
        
        //$data = executeSQL("SELECT * FROM EventMember WHERE uname = '$user'");
        //$eventName = array();
        //while($record = mysql_fetch_array($data)){
        //    $event = array();
        //    array_push($event, $record[0]);
        //    array_push($event, $record[1]);
        //    array_push($eventName, $event);
        //}

        $content = '<div id = "UserProfile"><h3>My Events</h3><ul>';

        for($i = 0; $i < sizeof($eventName); $i++){
            $new = replaceWhitespace($eventName[$i][0]);
            $content = $content.
                '<li><h4 style = "cursor:pointer" onclick="showUserEventDetail(\''.$new.'\', \'event\')">'.$eventName[$i][0].'</h4>'
                .'<p>'.$eventName[$i][1].'</p></li>';
        }

        $content = $content.'</ul></div>';
        $mysqli -> close();
        return $content;
    }

    function replaceWhitespace($string){
        $new = str_replace(" ", ".", $string);
        return $new;
    }

    function replaceDot($string){
        $new = str_replace(".", " ", $string);
        return $new;
    }

    function showUserReport($user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Report WHERE uname = '$user'");
        $query -> execute();
        $result = $query -> get_result();
        $reportName = array();
        while($row = $result -> fetch_assoc()){
            $report = array();
            array_push($report, $row['ename']);
            array_push($report, $row['gname']);
            array_push($report, $row['uname']);
            array_push($reportName, $report);
        }
        
        
        //$data = executeSQL("SELECT * FROM Report WHERE uname = '$user'");
        //$reportName = array();
        //while($record = mysql_fetch_array($data)){
        //    $report = array();
        //    array_push($report, $record[0]);
        //    array_push($report, $record[1]);
         //   array_push($report, $record[3]);
         //   array_push($reportName, $report);
       // }

        $content = '<div><h3>My Report</h3><ul>';

        for($i = 0; $i < sizeof($reportName); $i++){
            $id = replaceWhitespace($reportName[$i][0]);
            $content = $content.
                '<li><h4 onclick-"showUserReportDetail(\''.$id.'\')">'.$reportName[$i][0].'</h4>'
                .'<p>Group: '.$reportName[$i][1].'</p>'
                .'<p>Report Content: '.$reportName[$i][2].'</p></li>';
        }

        $content = $content.'</ul></div>';
        $mysqli -> close();
        return $content;
    }

    function showUserInformation($user){
        $content = showUserRecipe($user);
        $content = $content.showUserReview($user);
        $content = $content.showUserGroup($user);
        $content = $content.showUserEvent($user);
        $content = $content.showUserReport($user);
        return $content;
    }

    function joinGroup($gname, $user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("INSERT INTO Member VALUES ('$gname', '$user')");
        $query -> execute();
        $query -> close();
        $mysli -> close();
        //executeSQL("INSERT INTO Member VALUES ('$gname', '$user')");
    }

    function rsvpEvent($ename, $gname, $uname){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("INSERT INTO EventMember VALUES('$ename', '$gname', '$uname')");
        $query -> execute();
        $query -> close();
        $mysli -> close();
        //executeSQL("INSERT INTO EventMember VALUES('$ename', '$gname', '$uname')");
    }
    

    function showEvent($ename){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Event WHERE ename = '$ename'");
        $query -> execute();
        $result = $query -> get_result();
        $event = array();
        while($row = $result -> fetch_assoc()){
            array_push($event, $row['gname']);
            array_push($event, $row['ename']);
            array_push($event, $row['description']);
        }
        $query -> close();
        
        //$data = executeSQL("SELECT * FROM Event WHERE ename = '$ename'");
        //$event = array();
        //while($record = mysql_fetch_array($data)){
        //    array_push($event, $record[0]);
        //    array_push($event, $record[1]);
        //    array_push($event, $record[2]);
       // }

        $eid = $event[1];
        $content = '<div class="row">';

        $content = $content.
            '<div class= "col-sm-12">
            <h2>'.$event[1].'</h2>
            </div>
            <div class="col-sm-6">
             <h4>'.$event[0].'</h4>
             <p>'.$event[2].'<p>
             </div>';


        //event member
        $query = $mysqli -> prepare("SELECT * FROM EventMember WHERE ename = '$eid'");
        $query -> execute();
        $result = $query -> get_result();
        $members = array();
        while($row = $result -> fetch_assoc()){
            array_push($members, $row['uname']);
        }
        $query -> close();
        
        
        
        //$data1 = executeSQL("SELECT * FROM EventMember WHERE ename = '$eid'");
        //$members = array();
        //while($record = mysql_fetch_array($data1)){
        //    array_push($members, $record[2]);
        //}

        $content = $content.
        '<div class="col-sm-6">
        <h4>Event Member</h4><ul>';

        foreach ($members as $key => $value) {
            $content = $content.'<li>'.$value.'</li>';
        }

        $content = $content.'</ul></div>';

        // event report
        $query = $mysqli -> prepare("SELECT * FROM Report WHERE ename = '$eid'");
        $query -> execute();
        $result = $query -> get_result();
        $reports = array();
        while($row = $result -> fetch_assoc()){
            $report = array();
            array_push($report, $row['ename']);
            array_push($report, $row['gname']);
            array_push($report, $row['uname']);
            array_push($report, $row['report_text']);
            array_push($reports, $report);
        }
        $query -> close();
        
        //$data2 = executeSQL("SELECT * FROM Report WHERE ename = '$eid'");
        //$reports = array();
        //while($record = mysql_fetch_array($data2)){
        //    $report = array();
        //    array_push($report, $record[0]);
        //    array_push($report, $record[1]);
        //    array_push($report, $record[2]);
         //   array_push($report, $record[3]);
         //   array_push($reports, $report);
       // }

        $content = $content.
        '<div class="col-sm-12">
        <h4>Event Report</h4><ul>';
        foreach ($reports as $key => $value) {
            $content = $content.'<div class="col-sm-6"><p>Reported by: '.$value[2].'</p><li style="list-style-type:none" class="col-sm-6">'.$value[3];
            // report picture
            $query = $mysqli -> prepare("SELECT * FROM Report_Pic WHERE ename = 'ename'");
            $images = array();
            $query -> execute();
            $result = $query -> get_result();
            $row_cnt = $result -> num_rows;
            if($row_cnt > 0){
                while($row = $result -> fetch_assoc()){
                    $image = array();
                    array_push($image, $row['pid']);
                    array_push($image, $row['gname']);
                    array_push($image, $row['uname']);
                    array_push($image, $row['ename']);
                    array_push($image, $row['pname']);
                    array_push($image, $row['pic']);
                    array_push($images, $image);
                }

            
            
            
            //$data3 = executeSQL("SELECT * FROM Report_Pic WHERE ename = 'ename'");
            //$images = array();
            //$row = mysql_num_rows($data3);
            //if($row > 0){
               // while($record = mysql_fetch_array($data3)){
               //     $image = array();
               //     array_push($image, $record[0]);
                //    array_push($image, $record[1]);
                //    array_push($image, $record[2]);
                //    array_push($image, $record[3]);
                //    array_push($image, $record[4]);
                //    array_push($image, $record[5]);
                //    array_push($images, $image);
               // }
                foreach ($images as $key => $value) {
                    $content = $content.
                    '<image class="img-thumbnail" style="width:250px;height:250px;" src="'.$value[5].'">';
                }

            }
            $query -> close();


            $content = $content.'</li></div>';
        }
        $content = $content.'</ul></div>';

        $content = $content.'</div>';
        $mysqli -> close();
        return $content;
    }

    function addReport($user){
        $content = '<br><div id="writeReport"><h4>Write Report</h4>';
        $content = $content.
        '<form id = "addReport" method = "post" action="reportHandler.php">
            <label>Report Text:</label><br><input class="form-control type = "text" style="width: 100%; height: 100px" name = "text"><br>
            <input class="form-control" type="file" name="fileToUpload" id="fileToUpload">
            <button class="btn btn-success">Submit Report</button>
        </form>';
        $content = $content.'</div>';
        return $content;
    }


    function RSVP($ename, $user){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Event WHERE ename = '$ename'");
        $event = array();
        $query -> execute();
        $result = $query -> get_result();
        while($row = $result -> fetch_assoc()){
            array_push($event, $row['gname']);
            array_push($event, $record['ename']);
        }
        $query -> close();
        
        //$data = executeSQL("SELECT * FROM Event WHERE ename = '$ename'");
        //$event = array();
        //while($record = mysql_fetch_array($data)){
        //    array_push($event, $record[0]);
        //    array_push($event, $record[1]);
        //}


        $gname = replaceWhitespace($event[0]);
        $ename = replaceWhitespace($event[1]);
        $content = '<div>';
        $content = $content.
        '<button id = "rsvp" onclick="registerEvent(\''.$ename.'\',\''.$gname.'\',\''.$user.'\')">RSVP</button>
        ';
        $content = $content.'</div>';
        $mysqli -> close();
        return $content;
    }

    function isEventMember($uname, $ename){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM EventMember WHERE uname = '$uname' AND ename = '$ename'");
        $query -> execute();
        $result = $query -> get_result();
        $row_cnt = $result -> num_rows;
        $query -> close();
        $mysqli->close();
        if($row_cnt == 1){
            return true;
        }
        else{
            return false;
        }
        
        //$data = executeSQL("SELECT * FROM EventMember WHERE uname = '$uname' AND ename = '$ename'");
       // $row = mysql_num_rows($data);
        //if($row == 1){
        //    return true;
        //}
        //else{
        //    return false;
       // }
    }

    function isMember($uname, $gname){
        $mysqli = new mysqli("localhost", "root", "", "cookzilla");
        $query = $mysqli -> prepare("SELECT * FROM Member WHERE uname = '$uname' AND gname = '$gname'");
        $query -> execute();
        $result = $query -> get_result();
        $row_cnt = $result -> num_rows;
        $query -> close();
        $mysqli->close();
        if($row_cnt == 1){
            return true;
        }
        else{
            return false;
        }
        
        //$data = executeSQL("SELECT * FROM Member WHERE uname = '$uname' AND gname = '$gname'");
        //$row = mysql_num_rows($data);
        //if($row == 1){
        //    return true;
       // }
        //else{
        //    return false;
       // }
    }

    function addGroupBTN(){
        $content = 
        '<button class = "addGroupBtn btn btn-default" onclick="document.getElementById(\'myModal\').style.display=\'block\'">Create Group</button>';
        $content = $content.
        '<!-- The Modal -->
              <div id="myModal" class="modal">
                <span onclick="document.getElementById(\'id01\').style.display=\'none\'" 
              class="close" style="color:red" title="Close Modal">&times;</span>

                <!-- Modal Content -->

                <form class="modal-content animate" action="addGroup.php" method="post">
                  <div class="container">
                    <h3>Create New Group</h3>
                    <label><b>Group Name</b></label>
                    <input class="form-control" style="width:250px" type="text" placeholder="Group Name" name="gname" required>
                    <br>
                    <label><b>Group Description</b></label>
                    <input class="form-control" style="width:250px;height:150px" type="text" name="gdescription" required>
                    <br>
                    
                  </div>

                  <div class="canel-container form-bottom" style="background-color:#f1f1f1">
                    <button type="button" style="margin-left:14px" onclick="document.getElementById(\'myModal\').style.display=\'none\'" class="cancelbtn btn btn-default">Cancel</button>
                    <button type="reset" value="Reset" class="cancelbtn btn btn-default">Reset</button>
                    <button type="Submit" class="btn btn-success submitBTN">Create</button>
                  </div>
                </form>
              </div>
              </li>
        ';
        return $content;
    }
?>


















