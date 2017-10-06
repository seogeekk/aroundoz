<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
    include('init_connect.php');
    include('session.php');
   
    $item_per_page = 10;
    
    //$login_uid = 3;
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

    //throw HTTP error if page number is not valid
    if(!is_numeric($page_number)){
        header('HTTP/1.1 500 Invalid page number!');
        exit();
    }   

    //get current starting point of records
    
    $position = (($page_number-1) * $item_per_page);

    $sql = "SELECT n.notifid, "
            . "(select username from login where userid = n.userid) as ownerid, "
            . "n.postid, "
            . "(select username from login where userid = n.w_userid) as userid, "
            . "n.notifmsg, "
            . "n.notif_status, "
            . "n.n_datetime "
            . "FROM usernotif n "
            . "WHERE n.userid = ".$login_uid." "
            . "ORDER BY n.n_datetime DESC LIMIT $position, $item_per_page";
   
    //fetch records using page position and item per page. 
    $results = $connection->query($sql);
    
    $cnt = $results->num_rows;
    
    if($results === FALSE) { 
        echo $connection->error;
    }
    if ($cnt > 0) {
        //output results from database
        while($row = mysqli_fetch_array($results,MYSQLI_ASSOC)){ //fetch values

            $notifid = $row['notifid'];
            $postid = $row['postid'];
            $ownerid = $row['ownerid'];
            $userid = $row['userid'];
            $notifmsg = $row['notifmsg'];
            $timestamp = $row['n_datetime'];
            $status = $row['notif_status'];

            if ($status == 'u') {
                $style = "notif-unread";
            } else {
                $style = "notif-read";
            }
            echo '<section class="post '.$style.'">';
            echo '<div class="post-description">';

            // Get timestamp diff
            $date_a = new DateTime(date('m/d/Y h:i:s a', time()));
            $date_b = new DateTime($timestamp);
            $interval = $date_a->diff($date_b);



            $msg = '';
            if ($notifmsg == "COMMENT") {
                $msg = '<a href="profile.php?user='.$userid.'" class="post-author">'.$userid.'</a>'.' commented on your <a href="post.php?postid='.$postid.'">post</a>';
            } elseif ($notifmsg == "LIKE") {
                $msg = '<a href="profile.php?user='.$userid.'" class="post-author">'.$userid.'</a>'.' liked your <a href="post.php?postid='.$postid.'">post</a>';
            } elseif ($notifmsg == "FOLLOW") {
                $msg = '<a href="profile.php?user='.$userid.'" class="post-author">'.$userid.'</a>'.' started watching you';
            }
            echo $msg;
            echo '<span style="float:right">';

            //

            if ($interval->y > 0) {
                $timediff = $interval->y."";
            } elseif ($interval->m > 0) {
                $timediff = $interval->m."m";
            } elseif ($interval->d > 0) {
                $timediff = $interval->d."d";
            } else {
                $timediff = "0d";
            }
            echo '<a style="text-decoration:none;color:#908f8b" title="'.$timestamp.'">'.$timediff.'</a>';
            echo '</span>';


            echo '</div>';
            echo '</section>';

            // UPDATE status to r

            $sql = "UPDATE usernotif "
                    . "SET notif_status = 'r' "
                    . "WHERE notifid = ".$notifid;

            if ($connection->query($sql) == FALSE) {
                echo $connection->error;
            }


        }
    } 
    