
var lastTweet = '0';

function tweetCode(user, message, media) {
	var html = '<div class="tweet"><span class="name">' + user + '</span><span class="message">' + message + '</span>';
	console.log("Bum farmer " + media.length);
	if(media.length > 0)
	{
		html = html + '<span class="media">';
		for(var i = 0; i < media.length; i++)
		{
			html = html + '<img src="' + media[i] + '">';
		}
		html = html + '</span>';
	}
	html = html + '</div>';
	return(html);
}
function scrollTweets() {
	var r = Math.random();
	var url = "./tweets.json?" + r;
	var htmlcode = "";
	$.getJSON(url, function(json) {
		var done = false;
		var htmlcode = "";
		$.each(json, function(key, value) {
			var media = [];
			if(lastTweet == "" + value['id']) {
				done = true;
			}
			if(done) {
				return;
			}
			if(value['media']) { media = value['media']; }
			htmlcode = htmlcode + tweetCode(value['user'], value['text'], media);
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
	var url = "./tweets.json";
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
				var media = [];
				if(value['media']) { media = value['media']; }
				htmlcode = htmlcode + tweetCode(value['user'], value['text'], media);
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



