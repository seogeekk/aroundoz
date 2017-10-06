<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
session_start();

if (!isset($_SESSION['login_uid'])) {
    header("location: index.php");
} else {
    // Main SESSION variables
   $login_uid = $_SESSION['login_uid'];
   $login_username = $_SESSION['login_username'];
   $login_fname = $_SESSION['login_fname'];
   $login_fullname = $_SESSION['login_fname']." ".$_SESSION['login_lname'];
}