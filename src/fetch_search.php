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
    $searchtag = $_POST["query"];
    
    //get current starting point of records
    
    $position = (($page_number-1) * $item_per_page);

    $sql = "SELECT l.userid, l.username, l.firstname, l.lastname, u.defaultpic from login l "
            . "left join userprofile u on u.userid = l.userid "
            . "where username like '%$searchtag%' "
            . "or username like '$searchtag%' "
            . "or firstname like '%$searchtag%' "
            . "or lastname like '%$searchtag%' "
                            . "ORDER BY RAND() DESC LIMIT $position, $item_per_page";
    
    //fetch records using page position and item per page. 
    $results = $connection->query($sql);
    
    if($results === FALSE) { 
        echo $connection->error;
    }  
    //output results from database
    while($row = mysqli_fetch_array($results,MYSQLI_ASSOC)){ //fetch values
        
        $userid = $row['userid'];
        $username = $row['username'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $imagepath = $row['defaultpic'];
        
        
        echo '<section class="post" style="height: 60px">';
        echo '<div class="post-description">';
        
        if ($imagepath != null) {
            echo '<div style="float:left; margin: 10px">';
            echo '<img class="avatar" style="height: 50px; width: 50px" alt="'.$username.'" src="'.$imagepath.'"/>';
            echo '</div>';
        } else {
            echo '<div style="float:left; margin: 10px">';
            echo '<img class="avatar" style="height: 50px; width: 50px" alt="'.$username.'" src="resources/noprofile.jpg"/>';
            echo '</div>';
        }
        echo '<div class="search-result">';
        echo '<span style="font-size: 16px; font-weight:bold; display:block"><a href="profile.php?user='.$username.'">'.$username.'</a></span>';
        echo '<span style="font-size: 16px; display:block">'.$firstname.' '.$lastname.'</span>';
        echo '</div>';
        echo '</div>';
        echo '</section>';
        
    }