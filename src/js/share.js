/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */

 // Get the modal
         var modal = document.getElementById('newpost');

         // Get the button that opens the modal
         var btn = document.getElementById("share");

         // Get the <span> element that closes the modal
         var span = document.getElementsByClassName("close")[0];
        

         // When the user clicks the button, open the modal 
         btn.onclick = function() {
             modal.style.display = "block";
         }

         // When the user clicks on <span> (x), close the modal
         span.onclick = function() {
             modal.style.display = "none";
         }

         // When the user clicks anywhere outside of the modal, close it
         window.onclick = function(event) {
             if (event.target == modal) {
                 modal.style.display = "none";
             }
         }
         
         
$(document).ready(function() {
     $("#addsubmit").click(function(event) {
     event.preventDefault();
     var formData = new FormData($('#uploader')[0]);
        $.ajax({
           type: "POST",
           url: "share.php",
           data: formData,
           contentType: false,
           cache: false,
           processData: false,
           dataType: "HTML",
           beforeSend: function(data) {
             $("#addsubmit").attr('disabled', 'disabled');
           },
           success: function (data) {
             if (data.match("posterror")) {
                if (data.match("Post is empty")) {
                    alert("Post cannot be empty");
                } else {
                    alert("Error occurred!");
                }
             } else {
                modal.style.display = "none";
                window.location.reload();
             }
             $("#addsubmit").removeAttr('disabled');
           },
           error: function(e) {
             $("#addsubmit").removeAttr('disabled');
             alert("Error occurred!");
           }
       });
       return false;
      });
  });

                       