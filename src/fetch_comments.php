<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
    include('init_connect.php');
    include('session.php');
   
    $item_per_page = 10;
    
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
    $postid = $_POST["postid"];

    //throw HTTP error if page number is not valid
    if(!is_numeric($page_number)){
        header('HTTP/1.1 500 Invalid page number!');
        exit();
    }
    
    //get current starting point of records
    
    $position = (($page_number-1) * $item_per_page);

            //select l.username, m_message, c_timestamp
        //from postsmsg pm
       // inner join login l on l.userid = pm.m_userid
    
    $sql = "SELECT pm.commentid, l.username, m_message "
            . "FROM postsmsg pm "
            . "INNER JOIN login l ON l.userid = pm.m_userid "
            . "WHERE pm.postid = $postid "
            . "ORDER BY pm.c_timestamp ASC LIMIT $position, $item_per_page";
   
    
    //fetch records using page position and item per page. 
    $results = $connection->query($sql);
    
    if($results === FALSE) { 
        echo $connection->error;
    }  
    //output results from database
    while($row = mysqli_fetch_array($results,MYSQLI_ASSOC)){ //fetch values


        $username = $row['username'];
        $comment = $row['m_message'];
        echo '<div class="comment">';
        echo '<span><a href="profile.php?user='.$username.'">'.$username.'</a></span>';
        echo '<span> '.$comment.'</span>';
        echo '<br/>';
        echo '</div>';

    }