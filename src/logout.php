<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
include("session.php");
session_destroy();

header("location: index.php");
