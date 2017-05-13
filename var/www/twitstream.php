<?php

function deurl($text)
{
	$words = explode(" ", $text);
	$ret = array();
	foreach($words as $word)
	{
		if(strlen(stristr($word, "://")) > 0) { continue; }
		$ret[] = $word;
	}
	return(implode(" ", $ret));
}

function tweets($config) {

	$table = $config['tables']['tweets'];
	$table_media = $config['tables']['media'];

	$dbhost = $config['connecton']['host'];
	$dbuser = $config['connection']['username'];
	$dbpass = $config['connection']['password'];
	$database = $config['connection']['database'];

	$link = new mysqli($dbhost, $dbuser, $dbpass, $database);

	$query = "select ID,User,Message from " . $link->escape_string($table) . " ";
	$query .= "order by Date DESC limit 0,40;";
	$result = $link->query($query);
	$r = array();
	$i = 0;
	while($line = $result->fetch_assoc()) {
		if($i > 20) {
			continue;
		}
		$id = $line['ID'];
		$user = $line['User'];
		$text = deurl($line['Message']);
		$item = array();
		$item['id'] = $id;
		$item['user'] = $user;
		$item['text'] = $text;
		$r[] = $item;
		$i++;
	}
	$result->free();

	foreach($r as &$rr)
	{
		$images = array();
		$query = "select * from " . $link->escape_string($table_media) . " where Tweet='" . $rr['id'] . "' order by Ordering ASC;";
		$result = $link->query($query);
		while($line = $result->fetch_assoc()) {
			$images[] = $line['URL'];
		}
		$result->free();

		if(count($images) > 0) { $rr['media'] = $images; }
	}

	return($r);
}

function RenderJavaScript($config) {

	$interval = (int) $config['query']['interval'];

	if($interval < 1000) {
		$interval = 1000;
	}

	header("Content-type: text/plain");
?>

var lastTweet = '0';

function tweetCode(user, message) {
	var html = '<div class="tweet"><span class="name">' + user + '</span><span class="message">' + message + '</span></div>';
	return(html);
}
function scrollTweets() {
	var r = Math.random();
	var url = "botty.json.php?" + r;
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
	setInterval(scrollTweets, <?php print($interval); ?>);
});


<?php

}

function RenderCSS() { 
	
	header("Content-type: text/css");

?>html,body {
	width: 100%;
	height: 100%;
	border: 0px;
	padding: 0px;
	margin: 0px;
	overflow: hidden;
}
div#content {
	border: 0px;
	width: 100%;
	height: 100%;
	overflow: hidden;
	background-color: #000000;
}
div.tweet {
	border-bottom: solid 1px #00007F;
	margin-top: 15px;
	margin-left: 10px;
	margin-right: 10px;
	padding: 5px;
	background-color: #000000;
	color: #FFFFFF;
	font-family: sans-serif;
}
div.tweet span.name {
	display: block;
}
div.tweet span.message {
	font-size: 2em;
	display: block;
}
div#hidden {
	position: absolute;
	display: block;
	visibility: hidden;
}<?php
}

function RenderHTML() { 

	$config = Settings();
	$style = $config['style'];
	if(strlen($style) == 0) { $style="twitstream.css.php"; }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> <html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Twitstream</title>
		<meta http-equiv="Content-Language" content="English" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="<?php print($style); ?>" />
 		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="twitstream.js.php"></script>
	</head>
	<body>
			<div id="content"></div>
	</body>
</html> <?php

}

