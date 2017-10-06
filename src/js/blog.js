/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */
var track_page = 1; //track user scroll as page number, right now page number is 1
var loading  = false; //prevents multiple loads

load_contents(track_page); //initial content load

$(document).ready(function() {
$("#loadmore").click(function(event) {
event.preventDefault();
 var post_data = $(this).closest('form').serialize();
   track_page++;
    load_contents(track_page);
   return false;
 });
});
		
//Ajax load function
function load_contents(track_page){
    
    var query = document.getElementById('showpage');
    var uid = document.getElementById('profileuid');
    
    //var str = query.value;
    
    if(loading == false){
		loading = true;  //set loading flag on
		$('.loading-info').show(); //show loading animation 
		$.post( 'fetch_posts.php', {'page': track_page, 'query': query.value, 'uid': uid.value}, function(data){
			loading = false; //set loading flag off once the content is loaded
			if(data.trim().length == 0){
				//notify user if nothing to load
                                $("#results").append('<p style="text-align:center">No more posts</p>');
                                $("#loadmore").hide();
				return;
			}
			//$('.loading-info').hide(); //hide loading animation once data is received
			$("#results").append(data); //append data into #results element
		
		}).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
			alert(thrownError); //alert with HTTP error
		})
	}
}

