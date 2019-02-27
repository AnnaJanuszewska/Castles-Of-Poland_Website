<?php

error_reporting(0);

include("include.php");

$lang = "pl";
$text_adv = "REKLAMA";
if (strstr($_SERVER["PHP_SELF"], "_de.htm")) {
  $lang = "de";
  $text_adv = "ADVERTISING";
}
elseif (strstr($_SERVER["PHP_SELF"], "_en.htm")) {
  $lang = "en";
  $text_adv = "ADVERTISING";
}

if (!isset($banner_id)) {
  $banner_id = $_GET["id"];
}

if (!isset($ramka)) {
  $ramka = $_GET["ramka"];
}

if (isset($banner_id) && !$ramka) {
  $pre_html = "<table WIDTH=\"100%\" cellpadding=0 cellspacing=0 border=0>\n"
  ."<tr>\n"
  ."<td align=\"center\"\n"
  ."style=\"font-family: Arial, sans-serif; font-size: 12px; padding: 0px; margin: 0px; width: 140px;\">\n";
  $post_html = "</td>\n"
  . "</tr>\n"
  . "</table>\n";
  $the_query = "select id, html from cop_banners where id = $banner_id";
}
else {
  $pre_html = "<table WIDTH=\"100%\" cellpadding=0 cellspacing=0 border=0>\n"
  ."<tr>\n"
  ."<td align=\"center\"\n"
  ."style=\"font-family: Arial, sans-serif; font-size: 12px; font-weight: bold; background-color: #cd5c5c; border-color: #cd5c5c; color: #ffffff; border-width: 1px 1px 1px 1px;\">\n"
  .$text_adv . "\n"
  ."<td>\n"
  ."</tr>\n"
  ."<tr>\n"
  ."<td align=\"center\"\n"
  ."style=\"font-family: Arial, sans-serif; font-size: 12px; border-style: solid; border-width: 1px 1px 1px 1px; padding: 0px; margin: 0px; border-color: #000000; border-top-color: #cd5c5c; width: 140px;\">\n";
  $post_html = "</td>\n"
  . "</tr>\n"
  . "</table>\n";

  if (isset($banner_id)) {
    $the_query = "select id, html from cop_banners where id = $banner_id";
  }
  else {
    $the_query = "select id, html from cop_banners where (active > 0) and (lang like '%" . $lang . "%')";
  }
}

if ($link = mysql_connect($cop_db_host, $cop_db_user, $cop_db_pwd)) {
  if (mysql_select_db($cop_db_name, $link)) {

    if ($result = mysql_query($the_query)) {
	  $row_count = mysql_num_rows($result);
	  if ($row_count > 0) {
	  
	    mysql_data_seek($result, rand(0, $row_count - 1));
	    
	    $row = mysql_fetch_array($result);
		print($pre_html . $row["html"] . $post_html);
		mysql_query("update cop_banners set count=count+1 where id=" . $row["id"]);
	  }
	}

  }
  mysql_close($link);
}

?>
