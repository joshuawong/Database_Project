<?php
    require('controller.php');
    session_start();

    $page = $_GET['id'];
    $user = $_SESSION['user'];
    if($page == 'recipe'){
        $content = showUserRecipe($user);
    }
    elseif($page == 'review'){
        $content = showUserReview($user);
    }
    elseif($page == 'group'){
        $content = showUserGroup($user);
    }
    elseif($page == 'event'){
        $content  = showUserEvent($user);
    }
    elseif($page == 'report'){
        $content = showUserReport($user);
    }
    elseif($page == 'all'){
        $content = showUserInformation($user);
    }

    $title = "Profile";
    include('Templates.php');
?>
<script type="text/javascript">
  document.getElementById('home').setAttribute("class","none");
  document.getElementById('profile').setAttribute("class","active");

  //onclick function: show Recipe detail
  function showUserRecipeDetail(user){
      recipeId = document.getElementById("")
      document.getElementById('UserProfile').innerHTML = "<?php showUserRecipeDetail()?>";
  }

  //onclick function: show review detail
  function showUserReviewDetail(user){
     reviewId = document.getElementById("")
     document.getElementById('UserProfile').innerHTML = "<?php showUserReviewDetail()?>";
  }

  //onclick function: show group detail
  function showUserGroupDetail(user){
      groupId = document.getElementById("")
      document.getElementById('UserProfile').innerHTML = "<?php showUserGroupDetail()?>";
  }

  //onclick function: show event detail
  function showUserEventDetail(user){
      eventtId = document.getElementById("")
      document.getElementById('UserProfile').innerHTML = "<?php showUserEventDetail()?>";
  }
  //onclick function: show report detail
  function showUserReportDetail(user){
      reportId = document.getElementById("")
      document.getElementById('UserProfile').innerHTML = "<?php showUserReportDetail()?>";
  }
</script>
