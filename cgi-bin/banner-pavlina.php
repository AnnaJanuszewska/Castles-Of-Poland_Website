<?php

error_reporting(0);

include("include.php");

if ($link = mysql_connect($cop_db_host, $cop_db_user, $cop_db_pwd)) {
	
	$the_query = "select html from cop_banners_pavlina";
	
  if (mysql_select_db($cop_db_name, $link)) {

    if ($result = mysql_query($the_query)) {
	    $row_count = mysql_num_rows($result);
	    if ($row_count > 0) {
	  
        mysql_data_seek($result, rand(0, $row_count - 1));
        $row = mysql_fetch_array($result);
 		    print($row["html"]);
 	    }
    }
  }
  mysql_close($link);
}

?>
