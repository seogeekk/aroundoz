<?php
/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
    include("init_connect.php");
    
    // define $error
    $error = '';
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      
        
      $emailaddress = mysqli_escape_string($connection, $_POST['emailadd']);
      $username = mysqli_escape_string($connection, $_POST['username']);
      $fname = mysqli_escape_string($connection, $_POST['fname']);
      $lname = mysqli_escape_string($connection, $_POST['lname']);
      $password = mysqli_escape_string($connection, $_POST['password']);
      $bday = mysqli_escape_string($connection, $_POST['bday']);
      
      $sql = "SELECT userid FROM login where emailadd = '$emailaddress'";
      
      $result = $connection->query($sql);
      $row = mysqli_fetch_assoc($result);
      
      $count = mysqli_num_rows($result);
      if ($count == 0) {
          // Fix code - vulnerable to SQL injection
          $sql = "INSERT INTO login (emailadd, username, firstname, lastname, password, birthday)"
              . " VALUES ('$emailaddress', '$username', '$fname', '$lname', sha1('$password'), STR_TO_DATE('$bday','%d/%m/%Y'))";

          if ($connection->query($sql) == TRUE) {
              header("location: index.php");
          } else {
              $error = "There's a problem signing up: ".$connection->error;
          }
      } else {
          
          $error = "Email address already exists";
      }
    }
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Sign-up | Around Oz</title>
        <link rel="stylesheet" href="css/signup.css">
    </head>

    <body>
        <div class="home" >
            
            <div class="signup-form">
                <div class="imgcontainer">
                    <img src="resources/logo.jpg" alt="Avatar" class="avatar">
                </div>
                <div class="signup-tag">
                    <b>Join others exploring Australia!</b>
                </div>
                <div class="err" id="err-msg"><p><?php if ($error) { echo $error; } ?></p></div>
                <div class="container" >
                    <form id="signup" name="signup" method="post" action="">
                        <label>Email Address:</label>
                        <input type="text" name="emailadd" id="emailadd" placeholder="jamescook@aroundoz.com" required/>
                        <label>Username:</label>
                        <input type="text" name="username" id="username" placeholder="jcook" required/>
                        <label>Password:</label>
                        <input name="password" type="password" id="password" placeholder="******" autocomplete="new-password" required/>
                        <label>First Name:</label>
                        <input type="text" name="fname" id="fname" placeholder="James" required/>
                        <label>Last Name:</label>
                        <input type="text" name="lname" id="lname" placeholder="Cook"/>
                        <label>Birthday:</label>
                        <input type="text" name="bday" id="bday" placeholder="dd/mm/yyyy" onkeyup="
                          var date = this.value;
                          if (date.match(/^\d{2}$/) !== null) {
                             this.value = date + '/';
                          } else if (date.match(/^\d{2}\/\d{2}$/) !== null) {
                             this.value = date + '/';
                          }" maxlength="10"/>
                          <button id="signup-btn" type="submit" name="join">Join</button>
                          <span class="signup">Have an account? <a href="index.php">Login</a></span>
                    </form>
                </div>
            </div>  
        </div>
    </body>
</html>