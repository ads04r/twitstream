<?php

include_once(dirname(dirname(dirname(__FILE__))) . "/lib/valium/valium.php");
include_once("./twitstream.php");

function settings() {

	$configfile = dirname(dirname(dirname(__FILE__))) . "/etc/settings.json";
	if(!(file_exists($configfile))) {
		return array();
	}
	return( json_decode(file_get_contents($configfile), true) );
}

$v = new Valium();

$v->route("|/$|", function($m, $get, $post) {
	return(file_get_contents("./home.html"));
});

$v->route("|/tweets.json$|", function($m, $get, $post)
{
	$config = settings();
	tweets($config);
});

$ret = $v->run();

if(is_array($ret))
{
	header("Content-type: application/json"); 
	print(json_encode($ret, true));
	exit();
}

print($ret);

