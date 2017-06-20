<?php
  require ('controller.php');     
  $id = $_GET['id'];
  if(isRecipe($id) == true){
    showRecipeDetail($id);
  }
  else if($id == 'all' || $id == 'home'){
    echo showAllRecipe();
  }
  elseif($id == 'Soup' || $id == 'Vegan' || $id == 'Italian' || $id == 'Chinese' || $id == 'Spicy' || $id == 'Cake' || $id == 'Salad' || $id == 'Seafood'){
    echo showRecipeByTag($id);
  }
  elseif($id == 'group'){
    echo showGroup();
  }

  function isRecipe($id){
    if($id == 'all' || $id == 'home' || $id == 'group' || $id == 'Soup' || $id == 'Vegan' || $id == 'Italian' || $id == 'Chinese' || $id == 'Spicy' || $id == 'Cake' || $id == 'Salad' || $id == 'Seafood'){
      return false;
    }
    else
      return true;
  }
  


?>