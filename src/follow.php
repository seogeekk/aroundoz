<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */

   include('init_connect.php');
   include('session.php');
   include('common.php');
   
   /**
    * TODO: Move INSERTS into functions for reusability
    */
   
   // initialize error
   $error = '';
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
       
       /*
        *
        */
      $followuid = mysqli_escape_string($connection, $_POST['fuid']);
      $myuid = mysqli_escape_string($connection, $_POST['uid']);
      $action = mysqli_escape_string($connection, $_POST['action']);
      
      if ($action == "Follow") {
          
          $sql = "INSERT INTO watchlist (userid, w_userid) VALUES ($myuid, $followuid)";
          
          $result = $connection->query($sql);
          
          if ($result == FALSE) {
              echo '<span class="posterror">Error occurred'.$connection->error.'</span>';
          } 
      } else {
          
          $sql = "DELETE FROM watchlist WHERE userid = $myuid AND w_userid = $followuid";
          
          $result = $connection->query($sql);
          
          if ($result == FALSE) {
              echo '<span class="posterror">Error occurred'.$connection->error.'</span>';
          } 
      }
      
   }