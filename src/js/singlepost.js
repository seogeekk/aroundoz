/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */


var track_page = 1; //track user scroll as page number, right now page number is 1
var loading  = false; //prevents multiple loads

load_contents(track_page); //initial content load

$(document).ready(function() {
$("#loadmore-comments").click(function(event) {
event.preventDefault();
 var post_data = $(this).closest('form').serialize();
   track_page++;
    load_contents(track_page);
   return false;
 });
});
		
//Ajax load function
function load_contents(track_page){
    
    var pid = document.getElementById('postid');
    
    if(loading == false){
		loading = true;  //set loading flag on
		$('.loading-info').show(); //show loading animation 
		$.post( 'fetch_comments.php', {'page': track_page, postid: pid.value}, function(data){
			loading = false; //set loading flag off once the content is loaded
			if(data.trim().length == 0){
				$('#loadmore-comments').hide();
				return;
			} 
			$("#results").append(data); //append data into #results element
		}).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
			alert(thrownError); //alert with HTTP error
		})
	}
}




function addComment(userid, postid) {
      
      var comment = document.getElementById('comment');
     
      $.ajax({
           type: "POST",
           url: 'add_comment.php',
           data:{postid: postid, userid: userid, comment: comment.value},
           success:function(html) {
             $("#results").append(html);
             comment.value = "";
           }
      });
      
      
 }
