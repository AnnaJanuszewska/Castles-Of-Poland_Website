<?php

//*********** helper functions

   function checkid ($theid) {

      $query = "select id from ids where id=$theid";

      if (!$result = mysql_query($query)) {
        return false;
      }

      return (mysql_num_rows($result) > 0);
   }//end checkid

   function getid () {

      $query = "insert into ids(id) values(NULL)";

      if (!$result = mysql_query($query)) {
        return 0;
      }

      return mysql_insert_id();

   }//end getid

   function loguseragent($theid) {

      $useragent = getenv('HTTP_USER_AGENT');
      $lang = getenv('HTTP_ACCEPT_LANGUAGE');

      $query = "select * from cop_browsers where "
              ."userid=$theid and "
              ."useragent='$useragent' and "
              ."lang='$lang'";
      if (!$result = mysql_query($query)) {
        return false;
      }

      if (mysql_num_rows($result) > 0) {
            return true;
          }

      $query = "insert into cop_browsers(id, userid, useragent, lang) "
              ."values(null,"
              ."$theid, '$useragent', '$lang')";
      return mysql_query($query);
   }//end loguseragent

   function logreferer() {

     global $HTTP_REFERER;

         //if empty don't log
         if (!$HTTP_REFERER) {
       return false;
         }

     //check if it's from castlesofpoland.com
         if ( !(strpos($HTTP_REFERER,"castlesofpoland.com") === false)) {
           return false;
         }

         //check dbase for this referer
         //if exists quit
     $query = "select referer from cop_referers "
                 ."where referer='$HTTP_REFERER'";

     if (!$result = mysql_query($query)) {
       return false;
     }

     if (mysql_num_rows($result) > 0) {
              return true;
         }

     //doesn't exist so insert
     $query = "insert into cop_referers(referer) "
                 ."values('$HTTP_REFERER')";
         return mysql_query($query);
   }//end logreferer

   function loghost($id) {

                global $HTTP_X_FORWARDED_FOR, $REMOTE_ADDR, $REMOTE_HOST;

        //determine ip and hostname        of the client
                $ev_hostname = "unresolved";

                if ($HTTP_X_FORWARDED_FOR) // try to see through the proxy
                {
                        $ev_hostname = gethostbyaddr($HTTP_X_FORWARDED_FOR);
                        $ip = $HTTP_X_FORWARDED_FOR;
                }
                else if ($REMOTE_ADDR)
                {
                        $ev_hostname = gethostbyaddr($REMOTE_ADDR);
                        $ip = $REMOTE_ADDR;
                }
                else if ($REMOTE_HOST)
                {
                        $ev_hostname = $REMOTE_HOST;
                        $ip = gethostbyname($REMOTE_HOST);
                }

                if (!$ip)
                {
                        $ip = "unknown";
                }

        //check with dbase
        $query = "select * from cop_hosts where "
                ."userid=$id and "
                ."ip='$ip' and "
                ."hostname='$ev_hostname'";
        if (!$result = mysql_query($query)) {
          return false;
        }

        if (mysql_num_rows($result) > 0) {
                  return true;
                }

        $query = "insert into cop_hosts(id, userid, ip, hostname) "
                ."values(null,"
                ."$id, '$ip', '$ev_hostname')";
        return mysql_query($query);

   }//end loghost

   function processid () {

      global $HTTP_COOKIE_VARS;

      //open connection with database
      //here - since we have to open it anyway
      if (!$link = mysql_connect("localhost", "spec", "marek602")) {
        return false;
      }

      if (!mysql_select_db ("spec")) {
        return false;
      }

      $id = $HTTP_COOKIE_VARS["id"];

      //check if cookie id is set
      //and if its valid
      //if not get a new one
      if (!isset($HTTP_COOKIE_VARS["id"])) {
         $id = getid();
      } else if (!checkid($id)) {
         $id = getid();
      }

      if ($id) {

         loguseragent($id);
                 logreferer();
                 loghost($id);

         //set the cookie
         //if is invalid
         //or just to refresh it
         setcookie("id", "$id", time()+31000000, "/", ".castlesofpoland.com", 0); //for almost a year

         //let know other scripts that id
         //exists and is valid
         session_Start();
         session_register("idchecked");

         //close database link
         mysql_close($link);
      }

      return $id;

   }//end checkid2


//*********** MAIN PROGRAM **********

   //turn off ALL error messages
   error_reporting(0);

   if (isset($HTTP_COOKIE_VARS["id"])) {

      //id cookie is already set
      //if session cookie 'idchecked' not set
      //process checkid
      session_start();

      if (!session_is_registered("idchecked")) {
        $id = processid();
      };

   } else {

   //id is not set
   //check for 'test' cookie
     if (isset($HTTP_COOKIE_VARS["test"])) {

        //test cookie exists
        //delete it and check id
        setcookie("test", "test", time()-31000000, "/", ".castlesofpoland.com", 0);

        $id = processid();

     } else {

        //attempt to set a 'test' cookie
        setcookie("test", "test", time()+31000000, "/", ".castlesofpoland.com", 0);

     };
   };

//*********** END MAIN PROGRAM **********

  if (stristr($_SERVER['HTTP_HOST'], 'elk.castlesofpoland.com')) {
    header("Location: http://www.castlesofpoland.com/prusy/elk.htm");
    exit;    
  }

  DEFINE ("BBCLONEDIR", "/home/web/beginner/spec/bb/");
  DEFINE ("COUNTER", BBCLONEDIR . "counter_add.php");
  if (file_exists(COUNTER)) include (COUNTER);

  header("Content-type: text/html");

?>
