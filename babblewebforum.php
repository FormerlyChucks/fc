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
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// This function displays the List of the previous Post Titles
// along with the calling html file that is the top-level Forum Page.
function get_posts($art_path,$this_html_filename,$rel_path)
{
   // At this point, $this_html_filename contains only the name of the
   // html file that called this function.  There is no path name attached.
   // $rel_path is the relative path from the calling html file to this
   // php file.

   // Define the post titles list file for $this_html_filename:
   $len = strlen($this_html_filename);
   $substr_len = $len - 5;  //remove ".html" off the end of the string
   $file_name_short = substr($this_html_filename,0,$substr_len);
   $com_filename = $file_name_short . "_posts_list.html";
   $refs_filename = $file_name_short . "_posts_list_refs.html";

/*
   // Read data from babblewebforum.conf file:
   $conf_file = fopen("$rel_path/babblewebforum.conf", "r")
                or die("Unable to open file $rel_path/babblewebforum.conf");
   if(!file_exists($rel_path . "/babblewebforum.conf")) { exit;}
   while(!feof($conf_file))
   {
      $line = fgets($conf_file);
      if(strncmp("enable_email=","$line",13) == 0)
      {
         $enable_email = substr($line,13);
         $enable_email = chop($enable_email);
      }
      else {;}
   }
   fclose($conf_file);
*/

   // Save in home_file.txt the location and name of the Main forum 
   // page and the relative path provided by the user to be 
   // retrieved later by other BabbleWebForum functions.
   // If home_file.txt already exists, compare to the current calling function,
   // and if they are not the same, rewrite the name and location in the
   // saved file.
   if(!file_exists("$rel_path/posts/home_file.txt"))
   {
//echo "<br> working dir: "; 
//echo getcwd();
//echo "<br>art_path= $art_path";
//echo "<br>rel_path= $rel_path";
      $file = fopen("$rel_path/posts/home_file.txt","a");
      // write path from lighttpd working directory to main forum page file
      // and main forum file name.  Also write relative path from main
      // forum page to babbleweb directory.
      fwrite($file, $art_path . $this_html_filename . PHP_EOL); 
      fwrite($file, $rel_path . PHP_EOL); 
//exit;
   }
   else
   {
//echo "<br>art_path= $art_path";
//echo "<br>rel_path= $rel_path";
//echo "<br> working dir: "; 
//echo getcwd();
      //$len = strlen($art_path . $this_html_filename);
      $main_file_name = $art_path . $this_html_filename;
      $main_file_name_peol = $main_file_name . PHP_EOL;
//$le_mf = strlen($main_file_name);
//$le_mf2 = strlen($main_file_name_peol);
//echo "<br>le_mf = $le_mf";
//echo "<br>le_mf2 = $le_mf2";
//echo "<br> file = $main_file_name";
      $file = fopen("$rel_path/posts/home_file.txt","r");
      $line = fgets($file);
//echo "<br> line = $line";
      if(strcmp($main_file_name_peol,$line) != 0)
      {
         fclose($file);
         echo "<br>home_file.txt contents didn't match forum main page file name and path.";
         echo "<br> main file name = $main_file_name";
         echo "<br> line = $line";
         if(!unlink("$rel_path/posts/home_file.txt"))
            {echo "<br>Error Deleting File";}
         $file = fopen("$rel_path/posts/home_file.txt","a");
         fwrite($file, $art_path . $this_html_filename . PHP_EOL); 
         fwrite($file, $rel_path . PHP_EOL); 
      }
      fclose($file);
   }

   // If "list of posts" file already exists, display its contents. 
   $com_filename_with_path = $rel_path . "/posts/" .  $com_filename;
   if(file_exists($com_filename_with_path))
   {
      $com_file = fopen($com_filename_with_path, "r");
      while(!feof($com_file))
      {
         $line = fgets($com_file);
         echo $line;
      }
      fclose($com_file);
   }


   // If "list of posts" file reference file already exists, display its 
   // contents. 
   $refs_filename_with_path = $rel_path . "/posts/" .  $refs_filename;
   if(file_exists($refs_filename_with_path))
   {
      $file = fopen($refs_filename_with_path, "r");
      while(!feof($file))
      {
         $line = fgets($file);
         echo $line;
      }
      fclose($file);
   }
   
} //end of function get_posts


// This function adds the current user's Post Title to the List of 
// Post Topics, creates a new post file, and then refreshes the Forum Main Page 
// to display the List of all the Post Topics.  It also calls the manage_posts
// function to manage the posts_list files.
function put_posts()
{
   $html_filename = $name = $email = $title = $comment = $bot_test = "";
   $html_filenameError = $nameError = $emailError = $commentError = "";
   $titleError = "";
   $bot_testError = "";
   $enable_email=0;

   // Read data from babblewebforum.conf file:
   $conf_file = fopen("babblewebforum.conf", "r")
                or die("Unable to open file babblewebforum.conf");
   if(!file_exists("babblewebforum.conf")) { exit;}
   while(!feof($conf_file))
   {
      $line = fgets($conf_file);
      if(strncmp("art_path=","$line",9) == 0)
      {
         $art_path = substr($line,9);
         $art_path = chop($art_path);
      }
      else if(strncmp("enable_email=","$line",13) == 0)
      {
         $enable_email = substr($line,13);
         $enable_email = chop($enable_email);
      }
      else if(strncmp("home_page=","$line",10) == 0)
      {
         $home_page = substr($line,10);
         $home_page = chop($home_Page);
      }
      else if(strncmp("enable_replies=","$line",15) == 0)
      {
         $enable_replies = substr($line,15);
         $enable_replies = chop($enable_replies);
      }
      else if(strncmp("website_name=","$line",13) == 0)
      {
         $website_name = substr($line,13);
         $website_name = chop($website_name);
      }
      else if(strncmp("color_website_name=","$line",19) == 0)
      {
         $color_website_name = substr($line,19);
         $color_website_name = chop($color_website_name);
      }
      else if(strncmp("bg_color=","$line",9) == 0)
      {
         $bg_color = substr($line,9);
         $bg_color = chop($bg_color);
      }
      else if(strncmp("bgp_color=","$line",10) == 0)
      {
         $bgp_color = substr($line,10);
         $bgp_color = chop($bgp_color);
      }
      else if(strncmp("bgr_color=","$line",10) == 0)
      {
         $bgr_color = substr($line,10);
         $bgr_color = chop($bgr_color);
      }
      else if(strncmp("text_color=","$line",11) == 0)
      {
         $text_color = substr($line,11);
         $text_color = chop($text_color);
      }
      else if(strncmp("link_color=","$line",11) == 0)
      {
         $link_color = substr($line,11);
         $link_color = chop($link_color);
      }
      else if(strncmp("menu_bg_color=","$line",14) == 0)
      {
         $menu_bg_color = substr($line,14);
         $menu_bg_color = chop($menu_bg_color);
      }
      else {;}
   }
   fclose($conf_file);

   //Retrieve data saved by bwf_get_posts() in the home_file.txt.
   if(file_exists("posts/home_file.txt"))
   {
//echo "<br> working dir: ";
//echo getcwd();
//echo "<br>art_path= $art_path";
//echo "<br>rel_path= $rel_path";
      $file = fopen("posts/home_file.txt","r");
      // read path from lighttpd working directory to main forum page file
      // and main forum file name.  Also read relative path from main
      // forum page to babbleweb directory.
      //$forum_page_file = fgets($file);
      $temp = fgets($file);
//echo "<br>temp = $temp";
      $len = strlen($temp);
      $len_m1 = $len - 1;
      $forum_page_file = substr($temp,0,$len_m1);
//echo "<br>len_m1 = $len_m1";
//echo "<br>forum_page_file = $forum_page_file";
      //$rel_path = fgets($file);
      $temp = fgets($file);
//echo "<br>temp = $temp";
      $len = strlen($temp);
      $len_m1 = $len - 1;
      $rel_path = substr($temp,0,$len_m1);
//echo "<br>len_m1 = $len_m1";
//echo "<br>rel_path = $rel_path"; 
      fclose($file);
//exit;
   }
   else
   {
       echo "<br> home_file.txt does not exist.  Exiting ...";
   }
//echo "<br>art_path= $art_path";
//echo "<br>rel_path= $rel_path";
//exit;



   // Get the data entered by the user into the html forum post form:
   if($_SERVER["REQUEST_METHOD"] == "POST")
   {
      // HTML File Name
      if(empty($_POST["html_filename"]))
      {
         $html_filenameError = "HTML filename is blank.";
      }
      else
      {
         $html_filename = validate($_POST["html_filename"]);
      }

      // User's Name
      if(empty($_POST["name"]))
      {
         $nameError = "Name was missing.";
      }
      else
      {
         $name = validate($_POST["name"]);
      }
      // User's Email Address
      if($enable_email)
      {
         if(empty($_POST["email"]))
         {
             $emailError = "Email was missing.";
         }
         else
         {
             $email = validate($_POST["email"]);
             if (!filter_var($email, FILTER_VALIDATE_EMAIL))
             {
                $emailError = "Email format was invalid.";
             }
          /*
             $email = htmlspecialchars($_POST['email']);
             if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
             {
                 die("E-mail address not valid");
             }
          */

         }
      }

      // Get New Post Title 
      if(empty($_POST["title"]))
      {
         $titleError = "New post title was missing.";
      }
      else
      {
         $title = validate($_POST["title"]);

         // If we got a good main forum page file name, define some 
         // variable names and see if the user's new post title has already 
         // been used.
         if($html_filenameError == "")
         {
            // Define the name of the file containing the list of post titles:
            $len = strlen($html_filename);
            $substr_len = $len - 5;  //remove ".html" off the end of the string
            $posts_list_filename = substr($html_filename,0,$substr_len) . 
               "_posts_list.html";
            $posts_list_filename_short = substr($html_filename,0,$substr_len) .
               "_posts_list";
            $posts_dir = "posts";
            $posts_list_file_with_path = $posts_dir . "/" 
               . $posts_list_filename;
     
            // Define the name of the file containing the replies to this post:
            $filename = strip_non_alpha_numeric($title) . ".html";
            $post_file_with_path = $posts_dir . "/" . $filename;

            // If the title that the user just entered already exits, create 
            // an error message.
            if(file_exists($posts_list_file_with_path))
            {
               $title_already_exists = check_title($title, 
                  $posts_list_file_with_path, 
                  "../babblewebforum/$post_file_with_path");
               if($title_already_exists)
               {
                  $titleError = "This title already exists.  Choose another.";
               }
            } 
         }
         else
         {
            //echo "<br>H38 $html_filenameError";
            display_error($html_filenameError);
         }
      }

      // Get Text of New Post.
      if(empty($_POST["comment"]))
      {
         $commentError = "Text of new post is missing.";
      }
      else
      {
         $comment = validate($_POST["comment"]);
      }

      // User's test to see if he is a robot 
      if(empty($_POST["bot_test"]))
      {
         $bot_testError = "Robot test response with day of month was missing.";
      }
      else
      {
         $bot_test = validate($_POST["bot_test"]);
      
         //Test to see if robot test questions was answered correctly.
         $day_of_month = (int)date("dS");
         $correct_answer = $day_of_month + 8;  
         //Quotes around "$correct_answer" turn it into a string!
         if($bot_test != "$correct_answer")
         {
            $bot_testError = "Robot test with day of month failed";
         } 
      }
   } // end of getting data entered by the user on the main forum page.

   // Add <br> in front of line breaks in the $comment string.  This
   // allows the website visitor to be able to format his comments
   // with line breaks.
   $comment = nl2br($comment);

   // If there are errors in the required fields, say so:
   if(($html_filenameError != "") || ($commentError != "") ||
      ($nameError != "") || ($emailError != "") || ($titleError != "") ||
      ($bot_testError != ""))
   {
      //Write error messages
      if( $nameError != "")
      {
         display_error($nameError);
      }
      if( $enable_email && $emailError != "")
      {
         display_error($emailError);
      }
      if( $titleError != "")
      {
         display_error($titleError);
      }
      if( $commentError != "")
      {
         display_error($commentError);
      }
      if( $bot_testError != "")
      {
         display_error($bot_testError);
      }
   }
   else
   {
      // Write new post title to file containing the list of post titles:
      add_post_title($posts_list_file_with_path, $post_file_with_path, 
                     $title, $bgp_color, $bg_color, $text_color,
                     $link_color);

      if($enable_email)
      {
         // Write user's name and email address to saved_emails file:
         // This needs to write to a protected directory to which
         // web users do not have access.
         // Define emails file name and path:
         $emails_dir = "emails";
         $emails_file_with_path = $emails_dir . "/" . "emails.txt";

         // Check to see if this email address has already been recorded.
         $email_found = check_emails($emails_file_with_path, $email);

         // Write to emails file:
         if(!$email_found)
         {
            // Next 4 lines commented out, because www-data doesn't have write 
            // permission.
            //if(!is_dir($emails_dir))
            //{
            //   mkdir($emails_dir, 770, true);
            //}
            $emails_file = fopen($emails_file_with_path, "a");
            fwrite($emails_file, "Name: " . $name . PHP_EOL);
            fwrite($emails_file, "Email: " . $email . PHP_EOL);
            fwrite($emails_file, PHP_EOL);
            fclose($emails_file);
         }
      }

      // Create a "post" page file:
      if(!file_exists($post_file_with_path))
      {
         create_post_page_file($post_file_with_path, $filename, $title, 
                               $name, $comment, $enable_email, 
                               $enable_replies, 
                               $bgp_color, $bg_color, $bgr_color, $text_color,
                               $link_color, $website_name, $color_website_name,
                               $menu_bg_color, $html_filename);
      }

//echo "<br>calling manage_posts function ...";
      manage_posts($rel_path, $posts_list_filename_short, $posts_dir);

      // Refresh the webpage:
      $html_filename_wp = "../" . $art_path . "/" . $html_filename;
      //header("Refresh:0; url=$html_filename_wp");
      //When the page is cached in the browser, you'll have to use this:
      header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
      header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', false);
      header('Pragma: no-cache');
      header("Location: $html_filename_wp");

      // Manage posts --
      //    Split posts_list_file to keep it from getting too big.
      //    Rearrange posts lists links.
      //    
      //manage_posts($rel_path, $posts_list_filename_short, $posts_dir);
   }

} //end of function put_posts

// This function displays previous user's replies to a post.  
function get_comments($art_path,$this_html_filename,$rel_path)
{

   // At this point, $this_html_filename contains only the name of the
   // html file that called this function.  There is no path name attached.
   // $rel_path is the relative path from the calling html file to this 
   // php file.

   // Define the comments file for $this_html_filename:
   $len = strlen($this_html_filename);
   $substr_len = $len - 5;  //remove ".html" off the end of the string
   $com_filename = substr($this_html_filename,0,$substr_len) . "_replies.html";

   // Read data from babbleweb.conf file:
   $conf_file = fopen("$rel_path/babblewebforum.conf", "r") 
                or die("Unable to open file $rel_path/babblewebforum.conf");
   //if(!file_exists($rel_path . "/babblewebforum.conf")) 
   //if(!file_exists("babblewebforum.conf")) 
   while(!feof($conf_file))
   {
      $line = fgets($conf_file);
      if(strncmp("enable_email=","$line",13) == 0)
      {
         $enable_email = substr($line,13);
         $enable_email = chop($enable_email);
      }
      else {;}
   }
   fclose($conf_file);

   $com_filename_with_path = $rel_path . "/posts/" .  $com_filename;
   //$com_filename_with_path = "posts/" .  $com_filename;

   // If it already exists, display contents of the appropriate "post replies" file: 
   if(file_exists($com_filename_with_path))
   {
      $com_file = fopen($com_filename_with_path, "r");
      while(!feof($com_file))
      {
         $line = fgets($com_file);
         echo $line;
      }
      fclose($com_file);
   }
} //end of function get_comments


// This function adds the current user's reply to the Post Topic Page's
// previous replies and then refreshes the Post Topic page to 
// display all the replies.
function put_comments()
{

   $html_filename = $name = $email = $comment = $bot_test = "";
   $html_filenameError = $nameError = $emailError = $commentError = "";
   $bot_testError = "";
   $enable_email=0;
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
      if(strncmp("art_path=","$line",9) == 0)
      {
         $art_path = substr($line,9);
         $art_path = chop($art_path);
      }
      else if(strncmp("enable_email=","$line",13) == 0)
      {
         $enable_email = substr($line,13);
         $enable_email = chop($enable_email);
      }
      else if(strncmp("enable_replies=","$line",15) == 0)
      {
         $enable_replies = substr($line,15);
         $enable_replies = chop($enable_replies);
      }
      else if(strncmp("website_name=","$line",13) == 0)
      {
         $website_name = substr($line,13);
         $website_name = chop($website_name);
      }
      else if(strncmp("color_website_name=","$line",19) == 0)
      {
         $color_website_name = substr($line,19);
         $color_website_name = chop($color_website_name);
      }
      else if(strncmp("bg_color=","$line",9) == 0)
      {
         $bg_color = substr($line,9);
         $bg_color = chop($bg_color);
      }
      else if(strncmp("bgp_color=","$line",10) == 0)
      {
         $bgp_color = substr($line,10);
         $bgp_color = chop($bgp_color);
      }
      else if(strncmp("bgr_color=","$line",10) == 0)
      {
         $bgr_color = substr($line,10);
         $bgr_color = chop($bgr_color);
      }
      else if(strncmp("text_color=","$line",11) == 0)
      {
         $text_color = substr($line,11);
         $text_color = chop($text_color);
      }
      else if(strncmp("link_color=","$line",11) == 0)
      {
         $link_color = substr($line,11);
         $link_color = chop($link_color);
      }
      else if(strncmp("menu_bg_color=","$line",14) == 0)
      {
         $menu_bg_color = substr($line,14);
         $menu_bg_color = chop($menu_bg_color);
      }
      else {;}
   }
   fclose($conf_file);

   // If replies to posts (AKA "comments") are disabled, exit.
   if(!$enable_replies) 
   { 
      // Refresh the webpage:
      $html_filename_wp = "posts/" . $html_filename;
      //header("Refresh:0; url=$html_filename_wp");
      //When the page is cached in the browser, you'll have to use this:
      header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
      header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', false);
      header('Pragma: no-cache');
      header("Location: $html_filename_wp");
      exit;
   }

   // Get the data entered by the user into the html comments form:
   if($_SERVER["REQUEST_METHOD"] == "POST") 
   {
      // HTML File Name
      if(empty($_POST["html_filename"])) 
      {
         $html_filenameError = "HTML filename is blank.";
      }
      else
      {
         $html_filename = validate($_POST["html_filename"]);
      }

      // User's Name
      if(empty($_POST["name"])) 
      {
         $nameError = "Name was missing.";
      }
      else
      {
         $name = validate($_POST["name"]);
      }
      // User's Email Address
      if($enable_email)
      {
         if(empty($_POST["email"])) 
         {
             $emailError = "Email was missing.";
         } 
         else 
         {
             $email = validate($_POST["email"]);
             if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
             {
                $emailError = "Email format was invalid.";
             }
          
             //$email = htmlspecialchars($_POST['email']);
             //if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
             //{
             //    die("E-mail address not valid");
             //}

         }
      }
      // User's comment
      if(empty($_POST["comment"]))
      {
         $commentError = "Comment was missing.";
      }
      else
      {
         $comment = validate($_POST["comment"]);
      }
      // User's test to see if he is a robot 
      if(empty($_POST["bot_test"]))
      {
         $bot_testError = "Robot test response with day of month was missing.";
      }
      else
      {
         $bot_test = validate($_POST["bot_test"]);
      
         //Test to see if robot test questions was answered correctly.
         $day_of_month = (int)date("dS");
         $correct_answer = $day_of_month + 8;  
         //Quotes around "$correct_answer" turn it into a string!
         if($bot_test != "$correct_answer")
         {
            $bot_testError = "Robot test with day of month failed";
         } 
      }
//echo "bot_test = $bot_test<br>";
//echo "day of month = $day_of_month<br>";
//echo "correct answer = $correct_answer<br>";
//echo "bot_test error message = $bot_testError";
   }

   // Add <br> in front of line breaks in the $comment string.  This
   // allows the website visitor to be able to format his comments
   // with line breaks.
   $comment = nl2br($comment);

   // If there are errors in the required fields, say so:
   if(($html_filenameError != "") || ($commentError != "") ||
      ($nameError != "") || ($emailError != "") ||
      ($bot_testError != ""))
   {

      //Write error messages
      if( $html_filenameError != "")
      {
         display_error($html_filenameError);
      }
      if( $nameError != "")
      {
         display_error($nameError);
      }
      if( $enable_email && $emailError != "")
      {
         display_error($emailError);
      }
      if( $commentError != "")
      {
         display_error($commentError);
      }
      if( $bot_testError != "")
      {
         display_error($bot_testError);
      }
   }
   else
   {

      // Define name of file with replies to the post:
      $len = strlen($html_filename);
      $substr_len = $len - 5;  //remove ".html" off the end of the string
      $com_filename = substr($html_filename,0,$substr_len) . "_replies.html"; 

      // Write a reply to the file holding the replies to a post:
      $com_file_with_path = "posts/" . $com_filename;
      $com_file = fopen($com_file_with_path, "a");
      //$date_and_time = date("Y/m/d") . " at " . date("h:i:sa");
      //$date_and_time = date("M dS Y @ h:i:sa e");//"e" means UTC & time zone
      $date_and_time = date("M dS Y @ h:i:sa");
      fwrite($com_file,"<div style='background-color:$bgr_color; color:$text_color; padding:20px;'>"
             . $name . PHP_EOL
             . "said on "
             . $date_and_time . "," . PHP_EOL
             . "<br><br>" . PHP_EOL
             . $comment . PHP_EOL
             . "</div>");
      fwrite($com_file,"<div style='background-color:$bg_color;'></div>");
      fwrite($com_file,"<br>" . PHP_EOL);
      fclose($com_file);
      if($enable_email)
      {
         // Write user's name and email address to saved_emails file:
         // This needs to write to a protected directory to which
         // web users do not have access. 

         // Define emails file name and path:
         $emails_file_with_path = "emails/" . "emails.txt";

         // Check to see if this email address has already been recorded.
         $email_found = check_emails($emails_file_with_path, $email);

         // Write to emails file:
         if(!$email_found)
         {
            $emails_file = fopen($emails_file_with_path, "a");
            fwrite($emails_file, "Name: " . $name . PHP_EOL);
            fwrite($emails_file, "Email: " . $email . PHP_EOL);
            fwrite($emails_file, PHP_EOL);
            fclose($emails_file);
         }
      }

      // Refresh the webpage:
      $html_filename_wp = "posts/" . $html_filename;
      //header("Refresh:0; url=$html_filename_wp");
      //When the page is cached in the browser, you'll have to use this:
      header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
      header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', false);
      header('Pragma: no-cache');
      header("Location: $html_filename_wp");
   }

} //end of function put_comments


// Validate inputs:
function validate($var) 
{
  $var = trim($var);
  $var = stripslashes($var);
  //This inserts html code for special characters (>, <, $, etc.) 
  //so php code can't be submitted by user.
  $var = htmlspecialchars($var);
  $var = filter_var($var, FILTER_SANITIZE_STRING);
  return $var;
}


// Display any errors made by the user in entering data
// into the html form fields. 
function display_error($myError)
{

echo "<br><br><br><br><br><b>The following errors were encountered:</b><br><br>";
echo "<b style='color:red;'>$myError</b><br><br>"; 
echo "Please hit the web browser's &quotback&quot arrow and
correct the error. <br><br>";
//echo "<br><span style='color:red;'>The following errors
//occured:</span>";
}


// This function creates the html form for user replies to a post.
//
//    THIS FUNCTION IS NOT USED !!!!
//
function replies_form($post_replies_file)
{
?>
<html>
<body>

<br>
<h2 align="center">Add Your Comment<h2>
<br>
Required Fields *<br><br>

<form action="bwf_put.php" method="post"><!-- path is relative to this file -->
   <input type="hidden" name="html_filename" value="<$php echo '$post_replies_file'; ?>" />
   *Name: <br> <input type="text" name="name"><br><br>
   *Email: (Will Not Be Posted) <br><input type="text" name="email"><br><br>
   *Reply: <br><textarea name="comment" rows="10" cols="40" wrap="hard"></textarea><br><br>
   <p style='align:right; font-size:12px;'>Powered by&nbsp 
           <a style='color:$link_color;" 
           href='cheapskatesguide.org/articles/bwfforum.html'>bwfForum</a></p>
   *Robot Test:<br>
   Day of the month in North America + 8 = <textarea name="bot_test" rows="1" cols="5"></textarea><br><br>
   <input type="submit" value="Submit">
</form>

<div align="left">Powered by bwfForum</div>

</body>
</html>
<?php
}


// Check the emails file to see if the current emails has 
// already been recorded.
function check_emails($emails_file, $address)
{
   $email_present = 0;
   if(file_exists($emails_file))
   {
      $file = fopen($emails_file,"r");
      while(!feof($file))
      {
         $line = fgets($file);
         //if(strncmp("Email: ","$line",7) == 0)
         $out = stripos("$line",$address,0);
         if(($out == 0) && ($out !== FALSE))
         {
            $email_present = 1;
            break;
         }
      }
   }
   return $email_present;
}


// Check for non-alphanumeric characters, because post titles
// will be turned into file names, and non-aphanumeric file
// names cause problems.
// Return 0 if all characters are alphanumeric.
function alpha_numeric_screen($input)
{
   $len = strlen($input);
   $non_alpha_numeric=0;
   for($i=0;$i<$len;$i++)
   {  
      $s = $input[$i];
      if(!(((ord($s) > 64) && (ord($s) < 91))
         || ((ord($s) > 47) && (ord($s) < 59))
         || ((ord($s) > 96) && (ord($s) < 123))
         || ((ord($s) > 31) && (ord($s) < 35))
         || (ord($s) == 39)
         || (ord($s) == 44)
         || (ord($s) == 45)
         || (ord($s) == 46)
         || (ord($s) == 95)))
      {$non_alpha_numeric= 1;} 
   } 
   return $non_alpha_numeric;
}


// Check the post title that the user entered to see if it already 
// exists in the most recent posts list file.
function check_title($title, $posts_list_file_with_path, 
                     $post_file_with_path)
{
   $k = 0;

   // Read titles from file:
   $file = fopen("$posts_list_file_with_path", "r")
                or die("Unable to open file $posts_list_file_with_path");
   if(!file_exists($posts_list_file_with_path)) { exit;}

   // Find the location in the line of the posts list file where 
   // the title should be.  
   $char_start = 8;
   $line_start = $char_start + 25;
   //$line_stop = $char_start + strlen($post_file_with_path) - 5;
   $len = strlen($title);

   while(!feof($file))
   {
      $j = 0;
      $line = fgets($file);
      $line_stop = stripos($line, ".html") - 1;

      //Find the next title in the posts list file.
      if(strncmp("<a href=","$line", $char_start) == 0)
      {
//echo "<br>";
//echo "<br>title = $title";
//echo "<br>line = $line";
//echo "<br>line_start_pos = $line_start";
//echo "<br>line_stop_pos = $line_stop";
//echo "<br>line_start = $line[$line_start]";
//echo "<br>line_stop = $line[$line_stop]";

         // Compare the user's new title to the next title in the posts list 
         // file to see if they match.
         for($i=$line_start;$i<=$line_stop;$i++)
         {
            if($title[$j] != $line[$i]){break;}
            $j++;
         }

         // If the next characters in $line are ".html", then you 
         // have reached the end of the title in $line.  If they 
         // are not, the strings 
         // don't match, so set j to 0.
         $n = $line_stop + 1;
         $pos = strpos($line, ".html", 0);
         if($pos != $n) {$j=0;}
//echo "<br>j= $j";
//echo "<br>len= $len";
//echo "<br>line_start = $line_start";
//echo "<br>line_stop = $line_stop";
//echo "<br>title = $title";
//echo "<br>title0 = $title[0]";
//echo "<br>pos = $pos";
//echo "<br>line = $line";
//echo "<br>line_start = $line[$line_start]";
//echo "<br>line_stop = $line[$line_stop]";
//echo "<br>title len = $len";
//echo "<br>post_file_with_path = $post_file_with_path";
//echo "<br>j = $j";
//echo "<br>len = $len";
//echo "<br>char_start = $char_start";
//echo "<br>";
         if($j == $len)
         {
            $k = 1;
         } 
      } 
   }
//exit;
   return $k;
}


// Create a post file containing the user's post, php code to get
// any previous comments, and a comment form to add new comments.
function create_post_page_file($post_file_with_path, $filename, $title, 
                               $name, $comment, $enable_email, 
                               $enable_replies, 
                               $bgp_color, $bg_color, $bgr_color, $text_color,
                               $link_color, $website_name, $color_website_name,
                               $menu_bg_color, $html_filename)
{
   // Next 4 lines commented out, because www-data doesn't have write
   // permission.
   //if(!is_dir($posts_dir))
   //{
   //   mkdir($posts_dir, 770, true);
   //}
   $file = fopen($post_file_with_path, "a");
   //$date_and_time = date("Y/m/d") . " at " . date("h:i:sa");
   //$date_and_time = date("M dS Y @ h:i:sa e");//"e" means UTC & time zone
   //$date_and_time = date("M dS Y @ h:i:sa");
   $date = date("M dS Y");

   // Write the header for the new "post" page file.
   fwrite($file,"<!DOCTYPE html>" . PHP_EOL);
   fwrite($file,"<html>" . PHP_EOL);
   fwrite($file,"<body style='background-color:$bg_color; color:$text_color;'>" . PHP_EOL);

   //Write the top menu for the new "post" page file
   fwrite($file,"<div style='background-color:$menu_bg_color; color:$text_color;'>");
   fwrite($file,"   <pre class='tab'>");
   fwrite($file,"      <p align='left' style='font-color:$color_website_name; font-size:32px'><b>&nbsp&nbsp$website_name</b></p><br><br><br><br><br><br>");

   fwrite($file,"      <p align='right' style='margin-top:-80px; font-size:25px;'><a href='../../' style='color:$link_color; text-decoration:none'><b>Home</b></a>&nbsp&nbsp<a href='../$html_filename' style='color:$link_color; text-decoration:none'><b>Forum</b></a>&nbsp&nbsp</p>");
   fwrite($file,"   </pre>");
   fwrite($file,"</div>");
   fwrite($file,"<br><br><br><br><br><br>");

   //Write the text of the new post.
   fwrite($file,"<h1 align='center'>$title</h1><br>");
   //fwrite($file,"<p style='align:right; font-size:12px;'>"
   //  . "Powered by "
   //  . "<a href='cheapskatesguide.org/articles/bwfforum.html'>"
   //  . "bwfForum</a></p>" . PHP_EOL);
   fwrite($file,"<div style='background-color:$bgp_color; color:$text_color; padding:20px;'>"
          . "<br><br>Posted by: " . $name . PHP_EOL
          . " on " . $date . "," . PHP_EOL
          . "<br><br>" . "$comment" . PHP_EOL
          . "<br><br><br>" . PHP_EOL
          . "</div>");
   fwrite($file,"<div style='background-color:$bg_color;'></div>");
   fwrite($file,"<hr><br>" . PHP_EOL);

   //Write the PHP code that gets previous comments.
   fwrite($file,"<?php" . PHP_EOL);
   //fwrite($file,"$filename = basename(__FILE__, '.html');" . PHP_EOL);
   //fwrite($file,"echo \'$filename\';" . PHP_EOL);
   fwrite($file, 'include("../bwf_get.php");' . PHP_EOL);
   fwrite($file,"?>" . PHP_EOL);
   fwrite($file,"<p style='font-size:12px;'>If you just posted a reply, but you do not see it here, click on the page refresh button in your Internet browser.</p>" . PHP_EOL);

   //Write the html form that adds new comments.
   if($enable_replies)
   {
      fwrite($file,"<br><br><br>" .
             "<h2 align='center'>Add Your Reply</h2><br><br>" . PHP_EOL);
      fwrite($file,"<form action='../bwf_put.php' method='post'>" . PHP_EOL);
      fwrite($file,"<input type='hidden' name='html_filename' value='$filename'>" . PHP_EOL);
      fwrite($file,"*Name: <br> <input type='text' name='name'><br><br>" . PHP_EOL);
      if($enable_email)
      {
         fwrite($file,"*Email: (Will Not Be Posted) <br><input type='text' name='email'><br><br>" . PHP_EOL);
      }
      fwrite($file,"*Reply: <br><textarea name='comment' rows='10' cols='40' wrap='hard'></textarea><br>" . PHP_EOL);
      fwrite($file,"<span style='margin-left:100px;'>Powered by <a style='color:$link_color;' href='http://cheapskatesguide.org/articles/bwfforum.html'>bwfForum</a></span><br><br>
" . PHP_EOL);
      fwrite($file, "*Robot Test:<br>" . PHP_EOL);
      fwrite($file, "Day of the month in North America + 8 = <textarea name='bot_test' rows='1' cols='5'></textarea><br><br>" . PHP_EOL);
      fwrite($file,"<input type='submit' value='Submit'>" 
             . "<br><br>" . PHP_EOL);
   }
   fwrite($file,"</form>" . PHP_EOL);
   fwrite($file,"</body>" . PHP_EOL);
   fwrite($file,"</html>" . PHP_EOL);
   fclose($file);
}


// Add the new post title to the file containing the list of post titles.
function add_post_title($posts_list_file_with_path, $post_file_with_path, 
                        $title, $bgp_color, $bg_color, $text_color,
                        $link_color)
{
      // Next 4 lines commented out, because www-data doesn't have write
      // permission.
      //if(!is_dir($posts_dir))
      //{
      //   mkdir($posts_dir, 770, true);
      //}
      $com_file = fopen($posts_list_file_with_path, "a");
      //$date_and_time = date("Y/m/d") . " at " . date("h:i:sa");
      //$date_and_time = date("M dS Y @ h:i:sa e");//"e" means UTC & time zone
      //$date_and_time = date("M dS Y @ h:i:sa");
      $date = date("M dS Y");
      fwrite($com_file,"<div style='background-color:$bgp_color; color:$text_color; padding:20px;'>"
             . $date . PHP_EOL
             . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . PHP_EOL
             . "<a style='color:$link_color' 
                   href='../babblewebforum/$post_file_with_path'>$title</a>" 
             . PHP_EOL . "</div>");
      fwrite($com_file,"<div style='background-color:$bg_color;'></div>");
      fwrite($com_file,"<hr>" . PHP_EOL);
      fclose($com_file);
}


// Manage the files containing lists of posts and links to those files.
function manage_posts($rel_path, $posts_list_filename_short, $posts_dir)
{
//echo "<br> rel_path = $rel_path";
//echo "<br> working dir: "; 
//echo getcwd();
   $posts_per_list = 100;
   $num_posts_before_splitting = 120;

   $posts_list_file_with_path = $posts_dir . "/" . $posts_list_filename_short
                                . ".html";
//echo "<br>posts_list_file_with_path = $posts_list_file_with_path";
   $posts_list_refs_file_with_path = $posts_dir . "/" 
                                     . $posts_list_filename_short
                                     . "_refs.html";
//echo "<br>posts_list_refs_file_with_path = $posts_list_refs_file_with_path";

   //Count the number of posts in the most recent posts list file.
   $file = fopen($posts_list_file_with_path, "r")
                or die("Unable to open file $posts_list_file_with_path");
   if(!file_exists($posts_list_file_with_path)) { exit;}
   $num_posts=0;
   $line_num=0;
   while(!feof($file))
   {
      $line = fgets($file);
      $posts_lines_array[$line_num] = $line;
      $line_num++;
      if(strncmp("<a href=",$line,8) == 0) {$num_posts++;}
   }
   fclose($file);

   $total_lines = $line_num - 1;
   $lines_per_post = $total_lines/$num_posts;

//echo "<br>total lines = $total_lines";
//echo "<br>lines per post = $lines_per_post";
//echo "<br> num_posts = $num_posts";
//echo "<br>rel_path = $rel_path";
   // If more than  $num_posts_before_splitting, create a new posts 
   // list file and a new link to posts list file.
   if($num_posts > $num_posts_before_splitting)
   {
      //If there is no post list reference file, create one.
      if(!file_exists($posts_list_refs_file_with_path))
      {
//echo "<br>H5";
         $file = fopen($posts_list_refs_file_with_path,"a")
                or die("Unable to open file $posts_list_refs_file_with_path");
         if(!file_exists($posts_list_refs_file_with_path)) { exit;}
         fwrite($file, "<a href='$rel_path/$posts_list_file_with_path'>1</a>"
                . "&nbsp" . PHP_EOL);
         fclose($file);
      }
      //else
      //{
//echo "<br>H6";

         // Read into an array the references in the posts files 
         // references file. 
         $file = fopen($posts_list_refs_file_with_path,"r")
                or die("Unable to open file $posts_list_refs_file_with_path");
         if(!file_exists($posts_list_refs_file_with_path)) { exit;}
         $i=0;
         while(!feof($file))
         {
            $ref_line[$i] = fgets($file);
            $len = strlen($ref_line[$i]);
            if($len == 0){break;} //somehow a non-existent 0-length 
                                  //line is read. 
//echo "<br>i = $i";
//$temp = $ref_line[$i];
//echo "<br>ref_line = $temp";
//echo "<br>line length = $len";
            $i++;
         }   
         fclose($file);
         $num_posts_list_files = $i;
//echo "<br>num_post_list_files = $num_posts_list_files";
         $num_of_new_posts_list_files = $i + 1;
      
         // Split most recent posts list file in two--the new one with
         // the less recent list of posts and the rewritten 
         // old one with the most recent list of posts.

         // Write new posts list file 
         // (posts_list_file $num_of_new_posts_list_files).
         $new_posts_list_filename = $posts_dir .  "/" .
                                   $posts_list_filename_short . 
                                   $num_of_new_posts_list_files . ".html";
//echo "<br>new_posts_list_filename = $new_posts_list_filename";
         $lines_to_write = $posts_per_list*$lines_per_post;
         $start_line = $total_lines - $lines_to_write; //first line is 0.
//echo "<br>lines to write to new file = $lines_to_write";
//echo "<br>start_line = $start_line";
         $new_file = fopen($new_posts_list_filename,"a")
                or die("Unable to open file $new_posts_list_filename");
         if(!file_exists($new_posts_list_filename)) { exit;}
         for($i=0;$i<$start_line;$i++)
         {
            //if(strncmp("<a href=",$line,8) == 0) 
            //$n = $i + 1;
            //if(fmod($n,$lines_per_post) != 3)
            if(strncmp("<a href=",$posts_lines_array[$i],8) != 0) 
            {
               fwrite($new_file,$posts_lines_array[$i]);
            }
            else
            {
            // All reference links after the first one must be changed to
            // to be relative to the babblewebforum directory, so must
            // remove "../babblewebforum/" from the array and then 
            // write the result to file.
            $temp = $posts_lines_array[$i];
            $start = stripos($temp,"../babblewebforum");
            $start = $start + 2; 
            $end = $start + 15;
            $first_str = substr($temp,0,$start);
            $second_str = substr($temp,$end);
            fwrite($new_file, $first_str . $second_str);
            }
//echo "<br>Line: $posts_lines_array[$i]";
         }
         fclose($new_file);

         // Delete old posts list file (posts_list_file unnumbered).
         if(!unlink($posts_list_file_with_path))
            {echo "<br>Error Deleting File";}

         // Rewrite old posts list file.
//echo "<br>total lines = $total_lines";
         $old_file = fopen($posts_list_file_with_path,"a")
                or die("Unable to open file $posts_list_file_with_path");
         if(!file_exists($posts_list_file_with_path)) { exit;}
         for($i=$start_line;$i<$total_lines;$i++)
         {
            fwrite($old_file,$posts_lines_array[$i]);
         }
         fclose($old_file);


         // Delete old posts lists reference file.
         if(!unlink($posts_list_refs_file_with_path))
            {echo "<br>Error Deleting File";}

         // Create the new posts list files references file. 
         $file = fopen($posts_list_refs_file_with_path,"a")
                or die("Unable to open file $posts_list_refs_file_with_path");
         if(!file_exists($posts_list_refs_file_with_path)) { exit;}
         fwrite($file, "<a href='$rel_path/$posts_list_file_with_path'>1</a>"
                   . "&nbsp" . PHP_EOL);	
         fwrite($file, "<a href='$rel_path/$new_posts_list_filename'>2</a>"
                   . "&nbsp" . PHP_EOL);
         // Start for loop at one to skip first entry, since it has
         // been added above.
         if($num_posts_list_files > 1)
         {
            for($j=1;$j<$num_posts_list_files;$j++)
            {
               $line = $ref_line[$j];
               //$len = strlen($line);
               //$sub_ends = $len - 5;
               //$line_sub = substr($line,$sub_end);  
               $ref_ends = stripos($line,">");
               //$num_ends = stripos($line,"<",$ref_ends);
               //strripos($line,"<",$num_ends);
               $ref_ends++;
               //$num_begins = $ref_ends + 1;
               $ref = substr($line,0,$ref_ends); 
               //$rev_num_begins = $ref_ends - $sub_ends;
               //$ref_num = substr($line_sub,$ref_num_begins); 
               $ref_num = $j + 2;
//echo "<br>ref = $ref";
//echo "<br>ref_num = $ref_num";
               fwrite($file, $ref . $ref_num . "</a>" . "&nbsp" . PHP_EOL);
            }
         }
         fclose($file);

      //}
   }
//exit;
}

// Strip non-ascii characters that aren't allowed in file names.
function strip_non_alpha_numeric($title_in)
{
   $len = strlen($title_in);

   // Define the array.
   $title_out_array = [];

   $j=0;
   for($i=0;$i<$len;$i++)
   {
       $s = $title_in[$i];
       if(((ord($s) > 64) && (ord($s) < 91))
         || ((ord($s) > 47) && (ord($s) < 59))
         || ((ord($s) > 96) && (ord($s) < 123))
         || (ord($s) == 32)
         || (ord($s) == 46)
         || (ord($s) == 95))
       {
          $title_out_array[$j] = $title_in[$i];
          $j++;
       }
   }
   // Convert from array to string.
   return implode($title_out_array);
}

?>
