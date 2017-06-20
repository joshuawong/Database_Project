<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title;?></title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default" role="navigation">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#" style="background-color: #6e92cc; color : white">Cookzilla</a>
          </div>

          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <!-- navigation bar -->
            <ul class="nav navbar-nav">
              <li class="active" id="home"><a href="index.php")">Home</a></li>
              <li id="group"><a href="#" onclick="window.location='Group.php'" >Group</a></li>
              <li class="dropdown" id="selectTag" onclick="selectTag()">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tag <b class="caret"></b></a>
                <!-- tag dropdown -->
                <ul class="dropdown-menu">
                  <li><a href="#" onclick="recp('Italian')">Italian</a></li>
                  <li><a href="#" onclick="recp('Chinese')">Chinese</a></li>
                  <li><a href="#" onclick="recp('Cake')">Cake</a></li>
                  <li><a href="#" onclick="recp('Seafood')">Seafood</a></li>
                  <li><a href="#" onclick="recp('Vegan')">Vegan</a></li>
                  <li><a href="#" onclick="recp('Spicy')">Spicy</a></li>
                  <li><a href="#" onclick="recp('Soup')">Soup</a></li>
                  <li><a href="#" onclick="recp('Salad')">Salad</a></li>
                  <li class="divider"></li>
                  <li><a href="#" id="showAll" onclick="recp('all')">Show All</a></li>
                </ul>
              </li>
            </ul>
            <div class="col-sm-3 col-md-3" >
                <!-- search function -->
                <form class="navbar-form" role="search" method="post" id = "form">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="search" id="search-keyword">
                    <div class="input-group-btn">
                        <button id="search-btn" class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" ></i></button>
                    </div>  
                </div>
                </form>

            </div>
            <div class="col-sm-3 col-md-3" >
                <!-- advanced search function -->
                <div class="navbar-form" role="search">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button class="btn btn-default" onclick="document.getElementById('id02').style.display='block'">advanced search</button> 
                    </div>  
                </div>
                </div>

            </div>
            <!-- login function -->
            <ul class="nav navbar-nav navbar-right">
            <?php if(!isset($_SESSION['user'])){
              
              echo '
              <li>
              <a href="#" title="login" class="thickbox" onclick="document.getElementById(\'id01\').style.display=\'block\'">Login</a>
              <!-- Button to open the modal login form -->
              <!-- <button class="thickbox" onclick="">Login</button> -->

              <!-- The Modal -->
              <div id="id01" class="modal">
                <span onclick="document.getElementById(\'id01\').style.display=\'none\'" 
              class="close" title="Close Modal">&times;</span>

                <!-- Modal Content -->
                <form class="modal-content animate" action="login.php" method="post">
                  <div class="container">
                    <label><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <br>
                    <label><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required>

                    <button type="submit">Login</button>
                  </div>

                  <div class="canel-container" style="background-color:#f1f1f1">
                    <button type="button" onclick="document.getElementById(\'id01\').style.display=\'none\'" class="cancelbtn">Cancel</button>
                  </div>
                </form>
              </div>
              </li>
              <li>';
              }?>
              <div id="id02" class="modal">
                      <span onclick="document.getElementById('id02').style.display='none'" 
                    class="close" title="Close Modal">&times;</span>

                      <!-- Modal Content -->
                      <form class="modal-content animate" method="post">
                        <div class="container">
                          <label><b>Keyword</b></label>
                          <input type="text" placeholder="Enter Keyword" name="advancedkeyword" id="advancedkeyword" required>
                          <br>
                          <label><b>Rating</b></label>
                          <select id="rating" name="advancedrating">
                              <option id="5">5</option>
                              <option id="4">4</option>
                              <option id="3">3</option>
                              <option id="2">2</option>
                              <option id="1">1</option>
                          </select>

                          <button type="submit" id="advanced-btn" onclick="document.getElementById('id02').style.display='none'">Search</button>
                        </div>

                        <div style="background-color:#f1f1f1">
                          <button type="button" onclick="document.getElementById('id02').style.display='none'" class="cancelbtn">Cancel</button>
                          <button type="reset" value="Reset" class="cancelbtn">Reset</button>
                        </div>
                      </form>
                    </div>

              </li>
              <!-- Sign up function -->
              <?php if(!isset($_SESSION['user'])){
                echo '
              <li><a href="#" onclick="document.getElementById(\'id03\').style.display=\'block\'">Sign Up</a>
                    <div id="id03" class="modal">
                      <span onclick="document.getElementById(\'id03\').style.display=\'none\'" 
                    class="close" title="Close Modal">&times;</span>
                      <form class="modal-content animate" action="register.php" method="post">
                        <div class="container">
                          <label><b>Username:</b></label>
                          <input type="box" name="username" placeholder="Username" required>
                          <br>
                          <label><b>Name:</b></label>
                          <input type="text" placeholder="Lastname & Firstname" name="name" required>
                          <br>
                          <label><b>Password:</b></label>
                          <input type="password" placeholder="Password" name="password" required>
                          <br>
                          <label><b>Profile:</b></label>
                          <input type="text" name="profile" placeholder="Profile">

                          <button type="submit">Register</button>
                        </div>

                        <div style="background-color:#f1f1f1">
                          <button type="button" onclick="document.getElementById(\'id03\').style.display=\'none\'" class="cancelbtn">Cancel</button>
                          <button type="reset" value="Reset" class="cancelbtn">Reset</button>
                        </div>
                      </form>
                    </div>

              </li>';
              }?>
              <!-- post function -->
              <?php
                  if(isset($_SESSION['user'])){
                    echo '
                    <li><a href="#" onclick="document.getElementById(\'id04\').style.display=\'block\'">Post</a>
                      <div id="id04" class="modal">
                      <span onclick="document.getElementById(\'id04\').style.display=\'none\'"
                        class="close" title="Close Modal">&times;</span>
                      <form class = "modal-content animate" action = "handlerRecipe.php" method = "post"
                        enctype = "multipart/form-data">
                        <div class="container">
                        <label><b>Title:</b></label>
                        <input type="text" name="title" placeholder="Recipe Name" required>
                        <br>
                        <label><b>Number of Serving:</b></label>
                        <input type="text" name="numberofserving" placeholder="Number of Serving" required>
                        <br>
                        <label><b>Texual Description:</b></label>
                        <input type="text" name="description" placeholder="How to do it?">
                        <br>
                        <label>Tag :</label>
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

                   </li>';
              }?>
              <!-- my profile -->
              <?php
                if(isset($_SESSION['user'])){
                    echo '
                    <li class="dropdown" id="profile" onclick="selectProfile()">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">My profile <b class="caret"></b></a>
                        <!-- tag dropdown -->
                        <ul class="dropdown-menu">
                          <li><a href="#" onclick="getUserInfo(\'recipe\')">My recipe</a></li>
                          <li><a href="#" onclick="getUserInfo(\'review\')">My review</a></li>
                          <li><a href="#" onclick="getUserInfo(\'group\')">My group</a></li>
                          <li><a href="#" onclick="getUserInfo(\'event\')">My event</a></li>
                          <li><a href="#" onclick="getUserInfo(\'report\')">My report</a></li>
                          <li class="divider"></li>
                          <li><a href="#" id="showAll" onclick="getUserInfo(\'all\')">Show All</a></li>
                        </ul>
                      </li>';
                }
              ?>
              <!-- log out function -->
              <?php
                  if(isset($_SESSION['user'])){
                    echo '
                  <li><a href = "logout.php">Sign Out</a></li>';
              }?>
            </ul>
          </div><!-- /.navbar-collapse -->
        </nav>

        </div>
        <div class="Cookzilla-container" id = "showRecipe">
            <div class="left-container" id="left-container"></div>
            <div id="web-content" class="web-content">
                <?php echo $content;?>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    var modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function recp(id) {
        $('#web-content').load('data.php?id=' + id);
    }

    function selectGroup(id){
        document.getElementById('profile').setAttribute("class", "none");
        document.getElementById('home').setAttribute("class","none");
        document.getElementById('selectTag').setAttribute("class","none");
        document.getElementById('group').setAttribute("class","active");
        $('#web-content').load('data.php?id=' + id);
    }

    function selectTag(){
        document.getElementById('profile').setAttribute("class", "none");
        document.getElementById('home').setAttribute("class","none");
        document.getElementById('group').setAttribute("class","none");
        document.getElementById('selectTag').setAttribute("class","active");
    }

    function getUserInfo(id){
        window.location.href = "profile.php?id=" + id;
    }
    
    function selectProfile(){
        document.getElementById('profile').setAttribute("class", "active");
        document.getElementById('home').setAttribute("class","none");
        document.getElementById('selectTag').setAttribute("class","none");
        document.getElementById('group').setAttribute("class","none");
    }





    //......
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

//-------
    function selectedRecipe(id){
        window.location.href = 'Recipe.php?id=' + id;
    }
    $(document).ready(function(){
        $("#search-btn").click(function(event){
            event.preventDefault();
            var keyword = $("#search-keyword").val();
            if(keyword==''){
                alert("Please enter keyword.");
            }
            else{
                $.post("search.php", {search: keyword}, function(response,status){ // Required Callback Function
                    $('#web-content').html(response);
                    });
                }
            });
        $("#advanced-btn").click(function(event){
            event.preventDefault();
            var keyword = $("#advancedkeyword").val();
            var rate = $("#rating").val();
            if(keyword==''){
                alert("Please enter keyword.");
            }
            else{
                $.post("search.php", {advancedkeyword: keyword, advancedrating: rate}, function(response,status){ // Required Callback Function
                    $('#web-content').html(response);
                    });
                }
            });
    });


</script>

</html>
