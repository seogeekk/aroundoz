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
   
   $posttype = "POST";
   if($_SERVER["REQUEST_METHOD"] == "POST") {
       /*
        * Try to parse geodata first, then insert into geocontent
        * name, address, lat, lng, type
        */
       // Start mysql transaction
       if ($_POST['location'] != NULL) {
            $location = mysqli_escape_string($connection, $_POST['location']);
       
            $latlong    =   get_lat_long($location); 
            $map        =   explode(',' ,$latlong);
            $mapLat     =   $map[0];
            $mapLong    =   $map[1];    

            $geoid = NULL;
            $imageid = NULL;

            /*
             * INSERT INTO geocontent
             */

            $sql = "INSERT INTO geocontent (address,lat,lng,type) "
                     . "VALUES ('{$location}',$mapLat,$mapLong,'LOCATION')";

            if (!$connection->query($sql)) {
                $error = "Something went wrong! :".$connection->error;
                echo '<span id="posterror">'.$error.'</span>';
                exit();
            } else {
                $geoid = $connection->insert_id;
            }
       }
       
       /*
        * INSERT INTO imagecontent if there's image selected
        * INSERT into postscontent, userposts
        */
       if ($_SERVER['CONTENT_LENGTH'] > 2097152) {
           echo '<span id="posterror">FILE Exceeds limit</span>';
           exit();
       } elseif ($_FILES['uploadimage']['size'] > 0 && $_FILES['uploadimage']['error'] > 0){
           echo '<span id="posterror">FILE ERROR</span>';
       } else {
                
                //INSERT image if there is
                if ($_FILES['uploadimage']['size'] > 0) {
                    //$image = addslashes(file_get_contents($_FILES['uploadimage']['tmp_name']));
                    $randomkey = strtotime(date('Y-m-d H:i:s'));
                    $imagepath = 'upload_pic/'.$login_username.'_'.$randomkey.'.jpg';
                    move_uploaded_file($_FILES['uploadimage']['tmp_name'], $imagepath);
                    if (file_exists($imagepath)) {
                        $sql = "INSERT INTO imagecontent (p_imagepath) VALUES ('{$imagepath}')";
                        if (!$connection->query($sql)) {
                            $error = "Something went wrong! :".$connection->error;
                            unlink($imagepath);
                            echo '<span id="posterror">'.$error.'</span>';
                            exit();
                        } else {
                            $imageid = $connection->insert_id;
                        }
                    } else {
                        echo '<span id="posterror">Error uploading file!</span>';
                    }
                     
                }
                
                // INSERT INTO postscontent
                $p_imageid = $imageid ? $imageid : 'null';
                $p_geoid = $geoid ? $geoid : 'null';

                if ($p_imageid != 'null') {
                    $sql = "INSERT INTO postscontent(p_imageid, p_type) VALUES({$p_imageid},'{$posttype}')";
                    if (!$connection->query($sql)) {
                        $error = "Something went wrong! :".$connection->error;
                        echo '<span id="posterror">'.$error.'</span>';
                        exit();
                    } else {
                        $contentid = $connection->insert_id;
                    }
                }

                // INSERT INTO userposts
                $p_contentid = $contentid ? $contentid : 'null';
                $caption = mysqli_escape_string($connection, $_POST['caption']);
                
                if ($p_imageid != 'null' || strlen($caption)>0) {
                    $sql = "INSERT INTO userposts(userid,p_contentid,p_caption,p_locationid) "
                            . "VALUES({$login_uid},{$p_contentid},'{$caption}',{$p_geoid})";
                    if (!$connection->query($sql)) {
                        $error = "Something went wrong! :".$connection->error;
                        echo '<span id="posterror">'.$error.'</span>';
                        exit();
                    } 
                } else {
                    $error = "Post is empty!";
                    echo '<span id="posterror">'.$error.'</span>';
                } 
       } 
    }  

    if ($error) {
        echo '<span id="posterror">'.$error.'</span>';
    }