<?php
/*
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
error_reporting (E_ALL ^ E_NOTICE);
include('init_connect.php');
include('session.php');
include('common.php');
/**
 * TODO: Move INSERTS into functions for reusability
 */
// initialize error
$error = '';
$success = '';
$validated = '';
// Retrieve details of user
// If Save
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from form    

    $emailaddress = mysqli_escape_string($connection, $_POST['emailadd']);
    $firstname = mysqli_escape_string($connection, $_POST['fname']);
    $lastname = mysqli_escape_string($connection, $_POST['lname']);
    $password = mysqli_escape_string($connection, $_POST['password']);
    $newpassword = mysqli_escape_string($connection, $_POST['newpassword']);
    $bday = mysqli_escape_string($connection, $_POST['bday']);

    $aboutme = mysqli_escape_string($connection, $_POST['aboutme']);
    $urlprofile = mysqli_escape_string($connection, $_POST['urlprofile']);

    $imagepath = '';
    if ($_SERVER['CONTENT_LENGTH'] > 2097152) {
        $error = "File exceeds limit!";
    } elseif ($_FILES['profilepic']['size'] == 0) {
        // do nothing
    } elseif ($_FILES['profilepic']['size'] > 0 && $_FILES['profilepic']['error'] > 0) {
        $error = "File error!";
    }
    else {
        
        if (file_exists($defaultpic) == TRUE) {
            unlink($defaultpic);
        }
        $randomkey = strtotime(date('Y-m-d H:i:s'));
        $imagepath = 'upload_pic/'.$login_username.'_profile_'.$randomkey.'.jpg';
        move_uploaded_file($_FILES['profilepic']['tmp_name'], $imagepath);
        if (file_exists($imagepath) == FALSE) {
            $error = "File upload failed!";
        }
    }

    // Validate password first
    if ($newpassword) {
        if (strlen($newpassword) < 8) {
            $error = "Password should be 8 characters or more";
        } else {
            // Hey you changed your password

            $sql = "SELECT userid FROM login WHERE userid = $login_uid and password = sha1('$password')";

            $result = $connection->query($sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $count = mysqli_num_rows($result);

            if ($count == 1) {
                // proceed
                $validated = true;
            }
        }
    } elseif ($error == null) {
        $validated = true;
    }

    if ($validated == true && $error == null) {
        // Fix code - vulnerable to SQL injection
        if ($newpassword) {
            $sql = "UPDATE login SET "
                    . "emailadd = '" . $emailaddress . "', "
                    . "firstname = '" . $firstname . "', "
                    . "lastname = '" . $lastname . "', "
                    . "password = sha1('" . $newpassword . "'), "
                    . "birthday = STR_TO_DATE('" . $bday . "','%d/%m/%Y') "
                    . "WHERE userid = $login_uid";
        } else {
            $sql = "UPDATE login SET "
                    . "emailadd = '" . $emailaddress . "', "
                    . "firstname = '" . $firstname . "', "
                    . "lastname = '" . $lastname . "', "
                    . "birthday = STR_TO_DATE('" . $bday . "','%d/%m/%Y') "
                    . "WHERE userid = $login_uid";
        }
        
        if ($imagepath) {
            $sql2 = "UPDATE userprofile SET "
                . "aboutme = '".$aboutme."', "
                . "urlprofile = '".$urlprofile."', "
                . "defaultpic = '{$imagepath}' "
                . "WHERE userid = $login_uid";
        } else {
            $sql2 = "UPDATE userprofile SET "
                . "aboutme = '".$aboutme."', "
                . "urlprofile = '".$urlprofile."' "
                . "WHERE userid = $login_uid";
        }
        
        try {
            $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
            
            if ($connection->query($sql) == TRUE) {
                if ($connection->query($sql2) == FALSE) {
                    $error = "There's a problem saving changes:".$connection->error;
                } else {
                    $connection->commit();
                    $success = "Profile successfully updated.";
                }
            } else {
                $error = "There's a problem saving changes:".$connection->error;
            }

        } catch (Exception $ex) {
            $connection->rollback();
            $error = "There's a problem saving changes";
        }
        
    } elseif ($error == null) {
        $error = "Password invalid. Update failed!";
    } 
}

$sql = "SELECT l.userid, l.emailadd, l.firstname, l.lastname, date_format(l.birthday,'%d/%m/%Y') as birthday, l.username, p.aboutme, p.urlprofile, p.defaultpic from login l
         LEFT JOIN userprofile p ON l.userid = p.userid
         WHERE l.userid = " . $login_uid;

$results = $connection->query($sql);
if ($results === FALSE) {
    echo $connection->error;
} else {
    $row = mysqli_fetch_array($results, MYSQLI_ASSOC);

    $emailaddress = $row['emailadd'];
    $username = $row['username'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $bday = $row['birthday'];
    $aboutme = $row['aboutme'];
    $urlprofile = $row['urlprofile'];
    $defaultpic = $row['defaultpic'];
}
?>
<html>

    <head>
        <title>Around OZ | Home</title>
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/share.css">
        <link rel="stylesheet" href="css/editprofile.css">
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
            <div class="edit-container" >

                <div class="form">
                    <h3> Edit Profile</h3>
                    <span class="err"><?php if ($error) { echo $error;} ?></span>
                    <span class="success"><?php if ($success) { echo $success;} ?></span>
                    <div class="profile-img">
                        
                                <?php if ($defaultpic != null) {
                                           echo '<img class="profile-pic" id="avatar" alt="missing image" src="'.$defaultpic.'"/>';
                                        } else {   
                                           echo '<img class="profile-pic" id="avatar" src="resources/noprofile.jpg"/>';
                                        }
                                ?>
                    </div>
                    <div>
                        <p style="font-size:1.5vw;font-size:18px;margin:auto; padding-bottom: 20px">@<?php echo $username; ?></p>
                    </div>
                    <div id="edit-form">
                        
                        <form id="editprofile" name="profile" method="post" enctype="multipart/form-data">
                            <label>Email address:</label>
                            <input type="text" name="emailadd" id="emailadd" value="<?php echo $emailaddress; ?>" required/>
                            <label>First Name:</label>
                            <input type="text" name="fname" id="fname" value="<?php echo $firstname; ?>" required/>
                            <label>Last Name:</label>
                            <input type="text" name="lname" id="lname" value="<?php echo $lastname; ?>" />
                            <label>Old Password:</label>
                            <input name="password" type="password" id="password" placeholder=""/>
                            <label>New Password:</label>
                            <input name="newpassword" type="password" id="newpassword" placeholder=""/>
                            <label>Birthday:</label>
                            <input type="text" name="bday" id="bday" value="<?php echo $bday; ?>" onkeyup="
                                    var date = this.value;
                                    if (date.match(/^\d{2}$/) !== null) {
                                        this.value = date + '/';
                                    } else if (date.match(/^\d{2}\/\d{2}$/) !== null) {
                                        this.value = date + '/';
                                    }" maxlength="10" required/>
                            <label>About Me:</label>
                            <textarea name="aboutme" id="aboutme"><?php echo $aboutme; ?></textarea>
                            <label>Website URL:</label>
                            <input style="width:100%" type="text" name="urlprofile" id="urlprofile" value="<?php echo $urlprofile; ?>"/>
                            <label>Default Photo:</label>
                            <input type="file" name="profilepic" accept='image/*'/>
                            <button id="save-btn" type="submit" name="save">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/share.js"></script>
        <script src="js/blog.js"></script>
    </body>

</html>