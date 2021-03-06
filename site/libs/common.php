<?php

$available_locales = array("en","sp","ru","ua","fr","nl");
$default_locale = "en";
$siteroot = "";
$site_encoding = "utf-8"; 

$requestUri = $_SERVER["REQUEST_URI"];
if(preg_match("/^\/(en|sp|ru|ua|fr|nl)\//", $requestUri)) {
	$siteroot = substr($requestUri, 0, 3);
}

if(preg_match("/www\.mibew\.org/", $_SERVER['HTTP_HOST'])) {
    header("Location: http://mibew.org");
    exit;
}

function myiconv($in_enc, $out_enc, $string) {
	if($in_enc == $out_enc ) {
		return $string;
	}
	if( function_exists('iconv') ) {
		$converted = @iconv($in_enc, $out_enc, $string);
		if( $converted !== FALSE ) {
			return $converted;
		}
	}
	return $string; // do not know how to convert
}

function verifyparam( $name, $regexp, $default = null ) {
        if( isset( $_GET[$name] ) ) {
                $val = $_GET[$name];
                if( preg_match( $regexp, $val ) )
                        return $val;

        } else if( isset( $_POST[$name] ) ) {
                $val = $_POST[$name];
                if( preg_match( $regexp, $val ) )
                        return $val;

        } else {
                if( isset( $default ) )
                        return $default;
        }
        echo "<html><head></head><body>Wrong parameter used or absent: ".$name."</body></html>";
        exit;
}

function get_locale() {
	global $available_locales;

	$locale = verifyparam("intlocale", "/^[\w-]{2,5}$/", "");
	if( !$locale || !in_array($locale,$available_locales) )
		$locale = "en";

	return $locale;
}

$current_locale = get_locale();
setlocale(LC_TIME, getstring("time.locale"));
$messages = array();
$output_encoding = array();

function get_locale_links($href) {
	global $available_locales, $current_locale;
	$localeLinks = "";
	foreach($available_locales as $k) {
		if( strlen($localeLinks) > 0 )
			$localeLinks .= " &bull; ";
		if( $k == $current_locale )
			$localeLinks .= getlocal_($k, "names");
		else
			$localeLinks .= "<a href=\"$href?locale=$k\">".getlocal_($k, "names")."</a>";
	}
	return $localeLinks;
}

function load_messages($locale) {
	global $messages, $site_encoding, $output_encoding;
	$hash = array();
	$current_encoding = $site_encoding;
	$fp = @fopen(dirname(__FILE__)."/../locales/$locale/properties", "r");
	if(!$fp) { die("wrong locale"); }
	while (!feof($fp)) {
		$line = fgets($fp, 4096);
		$keyval = preg_split("/=/", $line, 2 );
		if( isset($keyval[1]) ) {
			if($keyval[0] == 'encoding') {
				$current_encoding = trim($keyval[1]);
			} else if($keyval[0] == 'output_encoding') {
				$output_encoding[$locale] = trim($keyval[1]);
			} else if( $current_encoding == $site_encoding ) {
				$hash[$keyval[0]] = str_replace("\\n", "\n",trim($keyval[1]));
			} else {
				$hash[$keyval[0]] = myiconv($current_encoding, $site_encoding, str_replace("\\n", "\n",trim($keyval[1])));
			}
		}
	}
	fclose($fp);
	$messages[$locale] = $hash;
}

function getoutputenc() {
	global $current_locale, $output_encoding, $site_encoding, $messages;
	if(!isset($messages[$current_locale]))
		load_messages($current_locale);
	return isset($output_encoding[$current_locale]) ? $output_encoding[$current_locale] : $site_encoding;
}

function getstring_($text,$locale) {
	global $messages;
	if(!isset($messages[$locale]))
		load_messages($locale);

	$localized = $messages[$locale];
	if( isset($localized[$text]) )
		return $localized[$text];

	if( $locale != 'en' ) {
		return getstring_($text,'en');
	}
	return "!".$text;
}

function getstring($text) {
	global $current_locale;
	return getstring_($text,$current_locale);
}

function getlocal($text) {
	global $current_locale, $site_encoding;
	return myiconv($site_encoding,getoutputenc(), getstring_($text,$current_locale));
}

function getlocal_($text,$locale) {
	global $site_encoding;
	return myiconv($site_encoding,getoutputenc(), getstring_($text,$locale));
}

function topage($text) {
	global $site_encoding;
	return myiconv($site_encoding,getoutputenc(), $text);
}

function getstring2_($text,$params,$locale) {
	$string = getstring_($text,$locale);
	for( $i = 0; $i < count($params); $i++ ) {
		$string = str_replace("{".$i."}", $params[$i], $string);
	}
	return $string;
}

function getstring2($text,$params) {
	global $current_locale;
	return getstring2_($text,$params,$current_locale);
}

function getlocal2($text,$params) {
	global $current_locale, $site_encoding;
	$string = myiconv($site_encoding,getoutputenc(), getstring_($text,$current_locale));
	for( $i = 0; $i < count($params); $i++ ) {
		$string = str_replace("{".$i."}", $params[$i], $string);
	}
	return $string;
}

function start_html_output() {
	$charset = getstring("output_charset");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: text/html".(isset($charset)?"; charset=".$charset:""));
}

function div($a,$b) {
    return ($a-($a % $b)) / $b;
}

function date_to_text($unixtime) {
	return strftime(getlocal("time.dateformat"), $unixtime);
}

function ccdate($day,$month,$year) {
	return date_to_text(mktime(0,0,0,$month,$day,$year));
}
	
?>
