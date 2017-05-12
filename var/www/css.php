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

RenderCSS();
