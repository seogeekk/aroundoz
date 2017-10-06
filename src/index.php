<!DOCTYPE html>
<!--
Copyright 2017
Author: Karez Bartolo <KarezDana@gmail.com>
-->
<?php
    include("init_connect.php");
    
    session_start();
    // Redirect to home if session exists
    if (isset($_SESSION['login_uid'])) {
        header("location: home.php");
    } 
    
    // define $error
    $error = '';
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      
      $emailaddress = mysqli_real_escape_string($connection,$_POST['emailadd']);
      $userpasswd = mysqli_real_escape_string($connection,$_POST['password']); 
      
      $sql = "SELECT userid, username, firstname, lastname FROM login WHERE emailadd = '$emailaddress' and password = sha1('$userpasswd')";
      
      $result = $connection->query($sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      
      $login_uid = $row['userid'];
      $login_username = $row['username'];
      $login_fname = $row['firstname'];
      $login_lname = $row['lastname'];
      
      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
         $_SESSION['login_uid'] = $login_uid;
         $_SESSION['login_username'] = $login_username;
         $_SESSION['login_fname'] = $login_fname;
         $_SESSION['login_lname'] = $login_lname;
         
         header("location: home.php");
      }else {
         $error = "Email address or Password is invalid";
      }
   }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Around OZ</title>
        <link rel="stylesheet" href="css/index.css">
    </head>
    <body>
        <div class="home">
            <div class="user-form">
                <form action="" method="post">
                    <div class="imgcontainer">
                        <img src="resources/logo.jpg" alt="Avatar" class="avatar">
                    </div>
                    <div>
                        <span class="err"><?php if ($error) { echo $error; } ?></span>
                    </div>
                    <div class="container">
                      <br/>
                      <label><b>Email Address</b></label>
                      <input type="text" placeholder="Enter Email address" name="emailadd" required>

                      <label><b>Password</b></label>
                      <input type="password" placeholder="Enter Password" name="password" required>

                      <button type="submit">Login</button>
                      <input type="checkbox"> Remember me
                    </div>

                    <div class="container" style="background-color:#f1f1f1">
                        <span class="signup">Join others? <a href="signup.php">Sign-up</a></span>
                    </div>
                </form>
            </div>
            <div class="splash">
                <div class="imgcontainer">
                    <img src="resources/splash.JPG" alt="Avatar" class="tagphoto">
                </div>


                <div class="tagline">
                    <p>
                        Connect to people who share the same interests in 
                    <ul>
                        <li>exploring places,</li> 
                        <li>breathtaking vantage points and</li>
                        <li>picturesque views.</li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
