<?php

error_reporting(0);

include("include.php");

$link_id = $_GET["id"];

if ($link = mysql_connect($cop_db_host, $cop_db_user, $cop_db_pwd)) {
  if (mysql_select_db($cop_db_name, $link)) {
    if ($result = mysql_query("select address from cop_links where id=$link_id")) {
	  if (mysql_num_rows($result) > 0) {
	    $row = mysql_fetch_array($result);
		mysql_query("update cop_links set count=count+1 where id=$link_id");
		header("Location: " . $row["address"]);
		exit;
	  }
	}
  }
  mysql_close($link);
}

if (isset($_SERVER["HTTP_REFERER"])) {
  if (substr($_SERVER["HTTP_REFERER"], 0, 7) == "http://") {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
	exit;
  }
  else {
    header("Location: http://www.castlesofpoland.com/");
    exit;
  }
}
else {
  header("Location: http://www.castlesofpoland.com/");
  exit;
}

?>

