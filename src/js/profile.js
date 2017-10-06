/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */


 $(document).ready(function() {
$("#follow").click(function(event) {
event.preventDefault();
 var post_data = $(this).closest('form').serialize();
   $.ajax({
      type: "POST",
      url: "follow.php",
      data: post_data,
      cache: false,
      success: function(data) {
          if (data.match("posterror")) {
              alert("Error occurred!");
          } else {
              window.location.reload();
          }
      },
      error: function(data) {
          window.alert("ERROR occured");
      }
   });
   return false;
 });
});