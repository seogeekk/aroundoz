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
    $condition = $_POST["query"];
    $uid = $_POST["uid"];

    //throw HTTP error if page number is not valid
    if(!is_numeric($page_number)){
        header('HTTP/1.1 500 Invalid page number!');
        exit();
    }
    
    if ($condition == 'u') {
        $WHERE = "WHERE u.userid = $uid ";
    } elseif ($condition == 'f') {
        $WHERE = "WHERE u.userid IN (SELECT w_userid FROM watchlist WHERE userid = $uid UNION SELECT $uid FROM dual) ";
    } else {
        $WHERE = "";    
    }
    
    
    
    //get current starting point of records
    
    $position = (($page_number-1) * $item_per_page);

    $sql = "SELECT u.postid, l.userid, l.username, i.p_imagepath, p_caption, p_timestamp, g.address, g.lat, g.lng "
                            . "FROM userposts u "
                            . "INNER JOIN login l ON u.userid = l.userid "
                            . "LEFT JOIN postscontent p ON p.p_contentid = u.p_contentid "
                            . "LEFT JOIN imagecontent i ON i.p_imageid = p.p_imageid "
                            . "LEFT JOIN geocontent g ON g.geoid = u.p_locationid "
                            . $WHERE
                            . "ORDER BY u.p_timestamp DESC LIMIT $position, $item_per_page";
    //fetch records using page position and item per page. 
    $results = $connection->query($sql);
    
    if($results === FALSE) { 
        echo $connection->error;
    }  
    //output results from database
    while($row = mysqli_fetch_array($results,MYSQLI_ASSOC)){ //fetch values
        
        $postid = $row['postid'];
        $uid = $row['userid'];
        $username = $row['username'];
        $caption = $row['p_caption'];
        $imagepath = $row['p_imagepath'];
        $timestamp = $row['p_timestamp'];
        $lat = $row['lat'];
        $long = $row['lng'];
        $location = $row['address'];
        
        echo '<section class="post">';
        echo '<div class="post-description">';

        // Get timestamp diff
        $date_a = new DateTime(date('m/d/Y h:i:s a', time()));
        $date_b = new DateTime($timestamp);
        $interval = $date_a->diff($date_b);
     
        echo '<a href="profile.php?user='.$username.'" class="post-author">'.$username.'</a>&nbsp;&nbsp;';
        
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
        echo '<a href="post.php?postid='.$postid.'" style="color:#908f8b" title="'.$timestamp.'">'.$timediff.'</a>';
        echo '</span>';
        echo '<hr class="post-line">';
        //https://maps.googleapis.com/maps/api/staticmap?center=40.714728,-73.998672&zoom=14&size=400x400&key=
        
        if ($imagepath != null) {
            if ($location) 
            {
                echo '<div class="location"><a href="http://maps.google.com/maps?z=14&t=m&q=loc:'.$lat.'+'.$long.'" target="_blank">'.$location.'</a></div>';
            }
            echo '<div class="landscape">';
            echo '<img alt="missing image" src="'.$imagepath.'"/>';
            echo '</div>';
        } else {
            if ($lat && $long) {
                echo '<div class="location"><a href="http://maps.google.com/maps?z=14&t=m&q=loc:'.$lat.'+'.$long.'" target="_blank">'.$location.'</a><img src="https://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$long.'&zoom=14&size=640x380&markers=color:red%7Clabel:A%7C'.$lat.','.$long.'&key=AIzaSyDG1dgrXGxjZBFIO3tYyM3nEqm18qtCokI"></div>';
            }
        }
        
        $sqllikes = "SELECT count(*) as count FROM postsactivity WHERE postid = $postid";
        $likes = $connection->query($sqllikes);
        if ($likes == TRUE) {
            
            $item = mysqli_fetch_array($likes,MYSQLI_ASSOC);
            $cnt = $item['count'];
        } else {
            $cnt = 0;
        }
        
        $postaction = '';
        $sqlaction = "SELECT count(*) as count FROM postsactivity WHERE postid = $postid and userid = $login_uid and activitytype ='COOL'";
        $action = $connection->query($sqlaction);
        if ($action == TRUE) {
            
            $activity = mysqli_fetch_array($action,MYSQLI_ASSOC);
            if ($activity['count'] == 0) {
                $postaction = "Like";
            } else {
                $postaction="Unlike";
            }
        }
        
        
        if ($imagepath == null && $lat == null && $long == null) {
            echo '<div class="post-caption-only">';
            echo '<p>'.$caption.'</p>';
            echo '</div>';
            echo '<div class="post-actions">';
            echo '<span id="posts-likes"><i id="like-count'.$postid.'">'.$cnt.'</i> likes</span>';
            echo '<span id="posts-dolike" style="float:right"><button class="like-btn" id="post-like-action'.$postid.'" onClick="likeButton('.$login_uid.','.$postid.')">'.$postaction.'</button></span>';
            echo '</div>';
        } else {
            echo '<div class="post-actions">';
            echo '<span id="posts-likes"><i id="like-count'.$postid.'">'.$cnt.'</i> likes</span>';
            echo '<span id="posts-dolike" style="float:right"><button class="like-btn" id="post-like-action'.$postid.'" onClick="likeButton('.$login_uid.','.$postid.')">'.$postaction.'</button></span>';
            echo '</div>';
            echo '<div class="post-caption">';
            echo '<p>'.$caption.'</p>';
            echo '</div>';
        }
        
        
        echo '</div>';
        echo '<div class="post-comment">';
        
        // GET COMMENTS
        $c_sql = "SELECT pm.commentid, l.username, m_message "
                . "FROM postsmsg pm "
                . "INNER JOIN login l ON l.userid = pm.m_userid "
                . "WHERE pm.postid = $postid "
                . "ORDER BY pm.c_timestamp ASC LIMIT 4";

        //fetch records using page position and item per page. 
        $c_results = $connection->query($c_sql);

        if($c_results === FALSE) { 
            echo $connection->error;
        }  
        echo '<div id="comment-box'.$postid.'">';
        //output results from database
        while($c_row = mysqli_fetch_array($c_results,MYSQLI_ASSOC)){ //fetch values


            $username = $c_row['username'];
            $comment = $c_row['m_message'];
            echo '<span><a href="profile.php?user='.$username.'">'.$username.'</a></span>';
            echo '<span> '.$comment.'</span>';
            echo '<br/>';

        }
        echo '</div>';
        echo '</div>';
        echo '<hr class="post-line">';
        
        // INSERT COMMENT BOX here!
        ?>
        <div class="form-posts">
            <input class="comment-textarea" type="text" name="comment" id="comment<?php echo $postid; ?>" placeholder="Add comment" required>
            <input class="comment-button" type="submit" id="submit-comment" onClick="<?php echo 'addComment('.$login_uid.','.$postid.')';?>" value="Comment"/>
        </div>
        <?php
        echo '</div>';
        echo '</section>';
        
    }