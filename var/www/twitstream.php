<?php

function Settings() {

	$configfile = dirname(dirname(dirname(__FILE__))) . "/etc/settings.json";
	if(!(file_exists($configfile))) {
		return array();
	}
	return( json_decode(file_get_contents($configfile), true) );
}

function RenderFeed() {

	$config = Settings();

	$include = $config['query']['include'];
	$exclude = $config['query']['exclude'];
	$table = $config['tables']['tweets'];
	$table_media = $config['tables']['media'];

	$dbhost = $config['connecton']['host'];
	$dbuser = $config['connection']['username'];
	$dbpass = $config['connection']['password'];
	$database = $config['connection']['database'];

	$link = mysql_pconnect($dbhost, $dbuser, $dbpass) or die("Could not connect");
	mysql_select_db($database) or die("Could not select database");

	$query = "select ID,User,Message from " . mysql_real_escape_string($table) . " where Message like '%" . implode("%' and Message like '%", $include) . "%' and Message 
not like 'RT %' and Message not like '@%' ";
	if(count($exclude) > 0) {
		$query .= "and Message not like '" . implode("%' and Message like '%", $exclude) . "' ";
	}
	$query .= "order by Date DESC limit 0,40;";
	$result = mysql_query($query);
	$r = array();
	$i = 0;
	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($i > 20) {
			continue;
		}
		$id = $line['ID'];
		$user = $line['User'];
		$text = $line['Message'];
		$item = array();
		$item['id'] = $id;
		$item['user'] = $user;
		$item['text'] = $text;
		$r[] = $item;
		$i++;
	}
	mysql_free_result($result);

	foreach($r as &$rr)
	{
		$images = array();
		$query = "select * from " . $table_media . " where Tweet='" . $rr['id'] . "' order by Ordering ASC;";
		$result = mysql_query($query);
		while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$images[] = $line['URL'];
		}
		mysql_free_result($result);

		if(count($images) > 0) { $rr['media'] = $images; }
	}

	header("Content-type: application/json");
	print(json_encode($r));
}

function RenderJavaScript() {

	$config = Settings();
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

