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
   
   $postid = $_GET['postid'];
   
   $sql = "SELECT u.postid, l.userid, l.username, i.p_imagepath, p_caption, p_timestamp, g.address, g.lat, g.lng "
                            . "FROM userposts u "
                            . "INNER JOIN login l ON u.userid = l.userid "
                            . "LEFT JOIN postscontent p ON p.p_contentid = u.p_contentid "
                            . "LEFT JOIN imagecontent i ON i.p_imageid = p.p_imageid "
                            . "LEFT JOIN geocontent g ON g.geoid = u.p_locationid "
                            . "WHERE u.postid = $postid";
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
        $location = $row['address'];
        $lat = $row['lat'];
        $long = $row['lng'];
    }
   
   
  
?>
<html>
   
   <head>
      <title>Around OZ | Home</title>
      <link rel="stylesheet" href="css/menu.css">
      <link rel="stylesheet" href="css/blog.css">
      <link rel="stylesheet" href="css/share.css">
      <script src="js/maps.js"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDG1dgrXGxjZBFIO3tYyM3nEqm18qtCokI&libraries=places&callback=initAutocomplete" async defer></script>
      <script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
   </head>
   
   <body>
       
   <div>
        <div class="header">
            <div class="menu-bar">
                <ul>
                    <li class="menu menu-title"><a href="index.php"><img src="resources/ozbar.jpg"></a></li>
                    <li class="menu menu-hover menu-left"><a href="profile.php?user=<?php echo $login_username; ?>" title="Profile">You</a></li>
                    <li class="menu menu-hover"><a href="explore.php" title="Explore">Explore</a></li>
                    <li class="menu menu-hover menu-right"><a href="logout.php" title="Sign-out">Logout</a></li>
                    <li class="menu menu-hover menu-right"><a href="notif.php" title="Notifications">Activity</a></li>
                    <li class="menu menu-hover menu-right"><a href="#" id="share" class="share-btn" title="Share">Share</a></li>
                    <form id="search-form" action="search.php" method="post">
                    <li class="menu menu-right"><input type="text" id="searchbox" name="searchtag" class="search-box" placeholder="Search"/></li>
                    <input type="submit" id="searchbtn" placeholder="Search" style="display:none"/>
                    </form>
                </ul>
            </div>
        </div>
        
        <div id="newpost" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                  <span class="close">&times;</span>
                  <p>Share your story</p>
                </div>
                <div class="modal-body">
                  <form id="uploader" class="create-post" action="" method="POST" enctype="multipart/form-data">
                    <span class="err"><?php if ($error) { echo $error; } ?></span><br/>
                    <textarea name="caption" placeholder="Anything interesting mate?"></textarea><br/>
                    <label>Location: </label><input id="autocomplete" name="location" placeholder="Add location" onFocus="geolocate()" type="text"/><br/>
                    <label>File: </label><input type="file" name="uploadimage" accept='image/*'/><br/>
                    <input type="button" id="addsubmit" class="share-btn" value="Share"/>
                 </form>

                </div>
                <div class="modal-footer">
                  <p id="progress"></p>
                </div>
            </div>
        </div>
            
        <!-- A wrapper for all the blog posts -->
            <input type="hidden" id="postid" value="<?php echo $postid; ?>">
            <input type="hidden" id="loginuser" value="<?php echo $login_username;?>">
        <div class="post-only">
            <div class="posts">
            <section class="post">
                <?php
                echo '<div class="post-description">';
                echo '<a href="profile.php?user='.$username.'" class="post-author">'.$username.'</a>&nbsp;&nbsp;';
                // Get timestamp diff
                $date_a = new DateTime(date('m/d/Y h:i:s a', time()));
                $date_b = new DateTime($timestamp);
                $interval = $date_a->diff($date_b);
                //
                if ($interval->y > 0) {
                    $timediff= $interval->y."y";
                } elseif ($interval->m > 0) {
                    $timediff= $interval->m."m";
                } elseif ($interval->d > 0) {
                    $timediff= $interval->d."d";
                } else {
                    $timediff= "0d";
                }
                echo '<span style="float:right">';
                echo '<a style="text-decoration:none;color:#908f8b" title="'.$timestamp.'">'.$timediff.'</a>';
                echo '</span>';
                echo '<hr class="post-line">';
                if ($imagepath != null) {
                    if ($location) {
                        echo '<div class="location"><a href="http://maps.google.com/maps?z=12&t=m&q=loc:'.$lat.'+'.$long.'" target="_blank">'.$location.'</a></div>';
                    }
                    echo '<div class="landscape">';
                    echo '<img alt="missing image" src="'.$imagepath.'"/>';
                    echo '</div>';
                } else {
                    if ($location) {
                        echo '<div class="location"><a href="http://maps.google.com/maps?z=12&t=m&q=loc:'.$lat.'+'.$long.'" target="_blank">'.$location.'</a><img src="https://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$long.'&zoom=14&size=640x380&markers=color:red%7Clabel:A%7C'.$lat.','.$long.'&key=AIzaSyDG1dgrXGxjZBFIO3tYyM3nEqm18qtCokI"></div>';
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
                ?>
                <div class="posts-comment" id="results" >
                    
                </div>
                
                <div class="loading-info">
                    <input type="button" id="loadmore-comments" value="Load More"/>
                </div>
                <hr class="post-line">
                <div class="form-posts">
                   <input class="comment-textarea" type="text" name="comment" id="comment" placeholder="Add comment" required>
                   <input class="comment-button" type="submit" id="submit-comment" onClick="<?php echo 'addComment('.$login_uid.','.$postid.')';?>" value="Comment"/>
                </div>
            </section>
            </div>
        </div>
    </div>
      
    <script src="js/like.js"></script>
    <script src="js/share.js"></script>
    <script src="js/singlepost.js"></script>
    <div class="footer">
    </div>
   </body>
   
</html>