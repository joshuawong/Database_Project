<?php
    require('controller.php');
    session_start();
    $title = "Group";
    
    $content = showGroup();
    
    include('Templates.php')
    ?>
<script type="text/javascript">
document.getElementById('home').setAttribute("class","none");
document.getElementById('group').setAttribute("class","active");

// onclick function 1 : show Group detail
function showGroupDetail(){
    // call php function 'showGroupDetail()'
}


// button: create new group

</script>
