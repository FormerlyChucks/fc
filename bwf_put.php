<?php

// Copywrite (c) 2018 cheapskatesguide.org
//
// This software is the beta version of bwfForum.  It is free for
// you to use.  You may not modify any of the PHP code without
// the author's permission.  You may modify the configuration file
// as needed to get bwfForum to run as designed on your computer.
// You may not sell or redistribute bwfForum without the author's
// permission.  If you would like to use bwfForum on your website,
// you must display the words "Powered by bwfForum" below the
// bwfForum "Reply" and "What You Want To Say" windows.
//
// Since this is beta software, it comes with no guarantee or warranty
// of any kind.  Use it at your own risk.
//
//  Author's email address: cheapskatesguide@protonmail.com
//
//  To Download the latest copy of bwfForum:
//  https://cheapskatesguide.org/articles/bwfforum.html
//


   include 'babblewebforum.php';

   // Read data from babbleweb.conf file:
   $conf_file = fopen("babblewebforum.conf", "r")
                or die("Unable to open file babblewebforum.conf");
   if(!file_exists("babblewebforum.conf"))
   {
      echo "No babblewebforum.conf file present!  Exiting ...";
      exit;
   }
   while(!feof($conf_file))
   {
      $line = fgets($conf_file);
      if(strncmp("enable_replies=","$line",15) == 0)
      {
         $enable_replies = substr($line,15);
         $enable_replies = chop($enable_replies);
      }
      else {;}
   }
   fclose($conf_file);

   // If replies (i.e. comments) are disabled, don't add new ones.
   // Old replies are still inserted into the post with 
   // the get_comments() function, which has already been called elsewhere.
   if($enable_replies)
   {
      put_comments();
   }
   else
   {
      $html_filename = "";
      
      // We already know the filename is there, otherwise we 
      // wouldn't be to this point in the code.  So, just read
      // the filename.
      $html_filename = validate($_POST["html_filename"]);

      // Refresh the webpage:
      if(file_exists("posts/" . $html_filename))
      {
         $html_filename_wp = "posts/" . $html_filename;
      }
      else
      {
         echo "Post filename unknown.";
         exit;
      }
      header("Refresh:0; url=$html_filename_wp");
   }

?>
