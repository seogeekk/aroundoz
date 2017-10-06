<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */

   include('init_connect.php');
   include('session.php');
   
   /**
    * TODO: Move INSERTS into functions for reusability
    */
   
   // initialize error
   $error = '';
   
   if (!empty($_POST['postid']) AND !empty($_POST['uid']) AND !empty($_POST['action'])) {
    // preventing sql injection
    $userid = mysqli_escape_string($connection, $_POST['uid']);
    $postid = mysqli_escape_string($connection, $_POST['postid']);
    $action = mysqli_escape_string($connection, $_POST['action']);

    if (strtoupper($action) == 'LIKE') {
        $sql = "CALL insert_like($userid, $postid)";
    } elseif (strtoupper($action) == 'UNLIKE') {
        $sql = "CALL delete_like($userid, $postid)";
    } else {
        
        echo '<span class="posterror">Error occurred</span>';
    }
    // insert new comment into comment table
    $results = $connection->query($sql);
    
    if($results === FALSE) { 
        echo '<span class="posterror">Error occurred</span>';
    }
    
  } else {
      echo '<span class="posterror">Error occurred</span>';
  }
    
?>

