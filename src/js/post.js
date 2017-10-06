/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */


function addComment(userid, postid) {
      
      var comment = document.getElementById('comment' + postid);
     
      $.ajax({
           type: "POST",
           url: 'add_comment.php',
           data:{postid: postid, userid: userid, comment: comment.value},
           success:function(html) {
             if (html.match("posterror")) {
                alert("Error occurred!");
             } else {
                $("#comment-box" + postid).append(html);
                comment.value = "";
             }
             
           }
      });
      
      
 }
