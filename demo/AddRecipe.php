<html>
<head>
    <title>AddRecipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id = "add_recipe">
        <form action = "handlerRecipe.php " method = "post"

        enctype = "multipart/form-data">

            <label>Recipe Title  :</label><input type = "text" name = "title" class = "box"/><br /><br />
            <label>Number Of Serving  :</label><input type = "text" name = "numberofserving" class = "number" /><br/><br />
            <label>Description  :</label><input type = "text" name = "description" class = "text" /><br/><br />
            <label>Tag  :</label>
            <div id="tagSelect">
                <select name = "tag[]" id = "tag" multiple="multiple" size="8">
                    <option value="Italian">Italian</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Vegan">Vegan</option>
                    <option value="Soup">Soup</option>
                    <option value="Spicy">Spicy</option>
                    <option value="Cake">Cake</option>
                    <option value="Salad">Salad</option>
                    <option value="Seafood">Seafood</option>
                </select>
            </div>
            
            <br/><br />
            <label>Ingredient :</label><input type="button" name="Add" onclick="addIngredient()" value="Add"> 
                <ul id="ingredientlist">  
                    <li id="ingredient">
                        <input type="box" name="101" placeholder = "Ingredient"> 
                        <input type="number" name="201" placeholder ="0">
                        <select name = "301">
                            <option value="cup">cup</option>
                            <option value="gram">gram</option>
                            <option value="kilogram">kilogram</option>
                            <option value="liter">liter</option>
                            <option value="milliliter">milliliter</option>
                            <option value="ounce">ounce</option>
                            <option value="pound">pound</option>
                            <option value="tablespoon">tablespoon</option>
                            <option value="teaspoon">teaspoon</option>
                            <option value="pinch">pinch</option>
                        </select>
                    </li>
                </ul>
            <br/><br/>
            <input type="file" name="fileToUpload" id="fileToUpload">
            <br/><br/>
            <input type = "submit" value = " Add Recipe "/><br />
        </form>
    </div>

</body>
<script type="text/javascript">
    function addIngredient(){
        var ul = document.getElementById("ingredientlist");
        var li = document.getElementById("ingredient");
        var clonedli = li.cloneNode(true);
        clonedli.children[0].setAttribute("name", getIndexOfIngredient());
        clonedli.children[1].setAttribute("name", getIndexOfQuantity());
        clonedli.children[2].setAttribute("name", getIndexOfQuantity());
        ul.appendChild(clonedli);
    }

    function getIndexOfIngredient(){
        var children = document.getElementById("ingredientlist").children;
        var number = children.length+100;
        return String(number+1);
    }

    function getIndexOfQuantity(){
        var children = document.getElementById("ingredientlist").children;
        var number = children.length+200;
        return String(number+1);
    }

    function getIndexOfUnit(){
        var children = document.getElementById("ingredientlist").children;
        var number = children.length+300;
        return String(number+1);
    }
</script>
</html>












