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
   
   if (!empty($_POST['comment']) AND !empty($_POST['postid']) AND !empty($_POST['userid'])) {
        // preventing sql injection
        $userid = mysqli_escape_string($connection, $_POST['userid']);
        $comment = mysqli_escape_string($connection, $_POST['comment']);
        $postid = mysqli_escape_string($connection, $_POST['postid']);

        $sql = "INSERT INTO postsmsg (postid, m_userid, m_message) "
                . "VALUES ($postid, $userid, '".$comment."')";
        
        // insert new comment into comment table
        $results = $connection->query($sql);

        if($results === FALSE) { 
            echo '<span class="posterror">'.$connection->error.'</span>';
        } else {
           // $data = array('username' => $username, 'comment' => $comment);           
            echo '<span><a href="profile.php?user='.$login_username.'">'.$login_username.'</a></span>';
            echo '<span> '.$comment.'</span><br/>';
        }

        
  } 
    
?>

