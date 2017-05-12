
var lastTweet = '0';

function tweetCode(user, message) {
	var html = '<div class="tweet"><span class="name">' + user + '</span><span class="message">' + message + '</span></div>';
	return(html);
}
function scrollTweets() {
	var r = Math.random();
	var url = "tweets.json?" + r;
	var htmlcode = "";
	$.getJSON(url, function(json) {
		console.log("DONE");
		var done = false;
		var htmlcode = "";
		$.each(json, function(key, value) {
			if(lastTweet == "" + value['id']) {
				done = true;
			}
			if(done) {
				return;
			}
			htmlcode = htmlcode + tweetCode(value['user'], value['text']);
		});
		lastTweet = "" + json[0]['id'];
		if(htmlcode.length > 0) {
			$('body').prepend('<div id="hidden">' + htmlcode + '</div>')
			var h = $('#hidden').height();
			$('#hidden').remove();
			$('#content').prepend('<div id="filler">&nbsp;</div>');
			$('#filler').animate({height: h}, 500, function() {
				$('#filler').remove();
				$('#content').prepend(htmlcode);
				$("#content").find("div.tweet:gt(20)").remove();
			});
		}
	});
}
function placeTweets() {
	var url = "./tweets.json.php";
	var htmlcode = "";
	var last = "";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function(json) {
			$.each(json, function(key, value) {
				if(last.length == 0) {
					last = '' + value['id'];
				}
				htmlcode = htmlcode + tweetCode(value['user'], value['text']);
			});
			$('#content').html(htmlcode);
			lastTweet = last;
		}
	});
}

$(document).ready(function() {
	placeTweets();
	setInterval(scrollTweets, 1000);
});



