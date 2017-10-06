/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */

function likeButton(userid, postid) {
      
      var actiontype = document.getElementById('post-like-action' + postid);
      var action = actiontype.textContent;
      var likes = document.getElementById('like-count' + postid);
      cnt = parseInt(likes.textContent);
      //alert(postid + 'psot' + uid + 'uid');
               
      $.ajax({
           type: "POST",
           url: 'likepost.php',
           data:{postid: postid, uid: userid, action: action},
           success:function(html) {
              if (html.match("posterror")) {
                  alert("Error occurred!");
              } else {
                if (action.toUpperCase() === "LIKE") {
                    actiontype.textContent = "Unlike";
                    likes.textContent = cnt + 1;
                } else {
                    actiontype.textContent = "Like";  
                    likes.textContent = cnt - 1;
                }  
              } 
           }
      });
      
      
 }