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


   $html_filenameError = $nameError = $emailError = $commentError = "";

   include 'babblewebforum.php';
   
   // $_SERVER['PHP_SELF'] is the name of the file containing
   // the PHP code that called this function.  The html form
   // in that file doesn't have access to this information, so it
   // must be put into the form as a hidden input.
   $this_html_filename_wp = htmlentities($_SERVER['PHP_SELF']);


// Change:  $this_html_filename_wp is now the path and filename of
//          the html file containing a single forum post topic.


   //Strip off any part of a path from $this_html_filename:
   $i = strrpos($this_html_filename_wp, "/"); //return the position of the last "/"
   $this_html_filename = substr($this_html_filename_wp,++$i);
   $art_path = substr($this_html_filename_wp,0,$i);
   $rel_path="..";
   get_comments($art_path,$this_html_filename,$rel_path);

?>
