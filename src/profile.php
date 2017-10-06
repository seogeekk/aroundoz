<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */

   include('init_connect.php');
   include('session.php');    
   
   $error = '';
   $p_username = $_GET['user'] ? $_GET['user'] : $login_username;
   
   $sql = "SELECT userid FROM login where username = '$p_username'";
   
   $results = $connection->query($sql);
   
   if($results === FALSE) { 
        echo $connection->error;
    } 
   $row = mysqli_fetch_array($results,MYSQLI_ASSOC);
   
   $profileuid = $row['userid'];
   
   
   $sql = "SELECT username, 
                  firstname, 
                  lastname, 
                  aboutme, 
                  urlprofile, 
                  defaultpic ,
                  (SELECT count(*) FROM userposts WHERE userid = $profileuid) as 'postcount',
                  (SELECT count(*) FROM watchlist WHERE userid = $profileuid) as 'following', 
                  (SELECT count(*) FROM watchlist WHERE w_userid = $profileuid) as 'followers' "
           . "FROM login l "
           . "LEFT JOIN userprofile u ON l.userid = u.userid "
           . "WHERE l.userid = $profileuid";
   
   $results = $connection->query($sql);
   
   if($results === FALSE) { 
        echo $connection->error;
    } 
   $row = mysqli_fetch_array($results,MYSQLI_ASSOC);
   
   $username = $row['username'] ? $row['username'] : 'null';
   $firstname = $row['firstname'] ? $row['firstname'] : 'null';
   $lastname = $row['lastname'];
   $aboutme = $row['aboutme'];
   $urlprofile = $row['urlprofile'];
   $defaultpic = $row['defaultpic'];
   $postcount = $row['postcount'] ? $row['postcount'] : 0;
   $following = $row['following'] ? $row['following'] : 0;
   $followers = $row['followers'] ? $row['followers'] : 0;
  
?>
<html>
   
   <head>
      <title>Around OZ | <?php echo $firstname." ".$lastname ?></title>
      <link rel="stylesheet" href="css/menu.css">
      <link rel="stylesheet" href="css/blog.css">
      <link rel="stylesheet" href="css/profile.css">
      <link rel="stylesheet" href="css/share.css">
      <link href='//fonts.googleapis.com/css?family=ABeeZee' rel='stylesheet'>
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
                     <input id="autocomplete" name="location" placeholder="Add location" onFocus="geolocate()" type="text"/><br/>
                     <label>Choose Photo: </label><br/><input type="file" name="uploadimage" accept='image/*'/><br/>
                     <input type="button" id="addsubmit" class="share-btn" value="Share"/>
                  </form>
                 </div>
                 <div class="modal-footer">
                   <p id="progress"></p>
                 </div>
             </div>
        </div>

       <div class="profile-header">
            <div class="profile">
                <div class="profile-img">
                    <?php if ($defaultpic != null) {
                            //$tmp_jpg = 'upload_pic/'.$login_username.'_default.jpg';
                            //$cropped_jpg = 'upload_pic/'.$login_username.'_cropped.jpg';
                           // file_put_contents($tmp_jpg, $defaultpic);
                               echo '<img id="avatar" alt="missing image" src="'.$defaultpic.'"/>';
                            } else {   
                               echo '<img id="avatar" src="resources/noprofile.jpg"/>';
                            }
                    ?>
                </div>
                <div class="profile-name">
                   @<?php echo $username; ?>
                   <a class="editprofile" href="editprofile.php" <?php if ($login_uid != $profileuid) { echo 'style="display:none"'; } ?> >Edit Profile</a>
                </div>

                <div class="profile-about">
                    <?php echo $firstname." ".$lastname; ?><br/>
                    <?php echo $aboutme; ?><br/>
                    <?php echo '<a href="'.$urlprofile.'" target="_blank">'.$urlprofile.'</a>'; ?><br/>
                    <form method="post" class="profile-action">
                    <?php

                        $follow = "Unfollow"; // Can't follow
                        if ($profileuid != $login_uid) {
                            // Check if already on watchlist
                            $sql = "SELECT count(*) as flag FROM watchlist "
                                    . "WHERE w_userid = $profileuid "
                                    . "AND userid = $login_uid";

                            $results = $connection->query($sql);
                             if($results === FALSE) { 
                                echo $connection->error;
                            } 
                            $row = mysqli_fetch_array($results,MYSQLI_ASSOC);
                            if ($row['flag'] == 0) {
                                $follow = "Follow";
                            }
                            echo '<input type="hidden" name="fuid" value="'.$profileuid.'"/> ';
                            echo '<input type="hidden" name="uid" value="'.$login_uid.'"/>';
                            echo '<input type="hidden" name="action" value="'.$follow.'"/>';
                            echo '<input type="button" name="follow" id="follow" class="profile-follow" value="'.$follow.'"/>';
                        } 
                    ?>
                    </form>
                </div>
                <div class="profile-details">    
                    <?php echo $postcount; ?> Posts •
                    <?php echo $following; ?> Watching •
                    <?php echo $followers; ?> Watchers
                    <br/>
                </div>
            </div>
        </div>
        <div class="profile-body"> 
            
            
            
            
            <input type="hidden" id="showpage" value="u"/>
            <input type="hidden" id="loginid" value="<?php echo $login_uid; ?>"/> 
            <input type="hidden" id="profileuid" value="<?php echo $profileuid; ?>"/> 
           
            <div class="posts" id="results">
            </div>
            <div class="posts">
                <div class="loading-info"><input type="button" id="loadmore" value="Load More"/></div>
            </div>
        </div>
       </div>
       
       <script src="js/like.js"></script>
       <script src="js/profile.js"></script>
       <script src="js/post.js"></script>
       <script src="js/share.js"></script>
       <script src="js/blog.js"></script>
   </body>
   
</html>