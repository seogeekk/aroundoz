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
  
?>
<html>
   
   <head>
      <title>Around OZ | Home</title>
      <link rel="stylesheet" href="css/menu.css">
      <link rel="stylesheet" href="css/blog.css">
      <link rel="stylesheet" href="css/share.css">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
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
                     <span class="err" id="errmsg"><?php if ($error) { echo $error; } ?></span><br/>
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
        <div class="user-body">
            <!-- A wrapper for all the blog posts -->
            <input type="hidden" id="profileuid" value="<?php echo $login_uid;?>">
            <div class="explore-posts">
                <div id="results">
                     <!-- This is where posts will appear -->
                </div>
                <div class="loading-info">
                     <input type="button" id="loadmore" value="Load More"/>
                </div>
             </div>
        </div>
       </div>
      
       <script src="js/post.js"></script>
       <script src="js/share.js"></script>
       <script src="js/explore.js"></script>
   </body>
   
</html>