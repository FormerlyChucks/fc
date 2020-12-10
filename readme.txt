Running this myself so I need the code somewhere. GitHub seems like the best place.

BwfForum is a very simple php script that gives website visitors a
way to make comments on your webpages.  BwfForum is written entirely 
in PHP and does not require a database program to work.  BwfForum
respects your visitors' privacy by not collecting any information
from their browsers or installing any cookies on their computers.
BwfForum only collects the information that the visitor intentionally
provides, in the form of a name, email address, post title, and reply.

This version of BwfForum works with PHP version 5.6.38.  Since PHP 
is constantly changing, there is no guarantee that it will work 
with older or newer versions of PHP.



Instructions for using bwfForum on a Linux Webserver:


Manual Installation instructions:

1. If you don't already have PHP installed on your Linux webserver, 
install it now.  BwfForum was written to work with PHP version 5.6.38.

2. Create the "babblewebforum" directory directly below the webserver's root 
directory (the directory containing your index.html file), and give it 
the following permissions:

drwxr-x--- 3 root www-data     4096 Dec  7 14:29 babbleweb

In the permissions above, www-data is the webserver visitor's (or web
user's) group.  The www-data group works for the Lighttpd webserver.  For
other webservers, use the appropriate group name.  

3. Inside the babblewebforum directory, put the following files from the 
compressed bwfForum file that you downloaded and create empty "posts" 
and "emails" directories.  Make sure your files, "emails", and 
"posts" directories have the following permissions:

-rw-r----- 1 root www-data  1364 Dec 14 19:59 babblewebforum.conf
-rw-r----- 1 root www-data 19587 Dec 15 12:40 babblewebforum.php
-rw-r----- 1 root www-data  1719 Dec 14 21:09 bwf_get.php
-rw-r--r-- 1 root root       952 Dec 13 14:52 bwf_get_posts.php
-rw-r----- 1 root www-data   888 Dec 13 19:55 bwf_put.php
-rw-r--r-- 1 root root       886 Dec 13 15:03 bwf_put_posts.php
drwxrwx--- 2 root www-data  4096 Dec 15 12:21 emails
drwxrwx--- 2 root www-data  4096 Dec 15 12:23 posts
-rw-r--r-- 1 root root      7543 Dec 15 13:00 readme.txt

The "posts" directory will contain the webserver visitor's posts and 
replies to posts.

4. Modify the "babblewebforum.conf" file to give it the path to your main
forum page html file, in which you will add the below lines of html code.  
If you would like to create more than one main forum page, so that
you can have multiple forums in very different realms of interest, 
you will have to use one copy of bwfForum for each main forum page.  
Also specify in the "babblewebforum.conf" file whether you want to collect 
visitors' email addresses.  Directions for modifying the "babblewebforum.conf" 
file are inside the "babblewebforum.conf" file. 



Integrating bwfForum into Your Main Forum Page HTML File:

Your main Forum page html file MUST BE DIRECTLY BELOW THE WEBSERVER'S ROOT
DIRECTORY and must contain the following PHP/HTML code,
near the bottom of the html body:


<?php
   // $rel_path is the relative path from this html file to the
   // bwf_get.php file.
   $rel_path = "../babblewebforum";
   include ("$rel_path" . "/bwf_get_posts.php");
?>
<br><br>
<h2 align="center">Create a New Topic For Discussion</h2>
<br>Required Fields *<br><br>
<!-- The path to bw_put.php in the "action" field is relative to this thml file. -->
<form action="../babblewebforum/bwf_put_posts.php" method="post">
   <input type="hidden" name="html_filename" value="forum_test.html" />
   *Name: <br><input type="text" name="name"><br><br>
   *Email: (Will Not Be Posted)<br><input type="text" name="email"><br><br>
   *Topic Title: <br><input type="text" name="title"><br><br>
   *What You Want To Say: <br><textarea name="comment" rows="10" cols="40"></textarea>
   <br><span style="margin-left:70px;">Powered by bwfForum</span><br><br>
   <input type="submit" value="Submit">
</form>


Since the above code snippet is not part of bwfForum, you are free to 
modify it as suits you--except that you must leave the phrase "Powered 
by bwfForum" below the "What You Want to Say" text window.  If you 
want to respect your website visitors' privacy by not requiring them 
to give their email addresses, set the variable "enable_email" to 0 in 
the babblewebforum.conf file.  Then, use the following code in your main forum
page html code instead of the above code:


<?php
   // $rel_path is the relative path from this html file to the
   // bwf_get.php file.
   $rel_path = "../babblewebforum";
   include ("$rel_path" . "/bwf_get_posts.php");
?>
<br><br>
<h2 align="center">Create a New Topic For Discussion</h2>
<br>Required Fields *<br><br>
<!-- The path to bw_put.php in the "action" field is relative to this thml file. -->
<form action="../babblewebforum/bwf_put_posts.php" method="post">
   <input type="hidden" name="html_filename" value="forum_test.html" />
   *Name: <br><input type="text" name="name"><br><br>
   *Topic Title: <br><input type="text" name="title"><br><br>
   *What You Want To Say: <br><textarea name="comment" rows="10" cols="40"></textarea>
   <br><span style="margin-left:70px;">Powered by bwfForum</span><br><br>
   <input type="submit" value="Submit">
</form>


A template for your forum's main page is included in the bwfForum
beta compressed file that you downloaded.  This file, which is called,
"forum_test.html", is the html page that will show the listing of 
forum post titles (after posts have been created).  This file has a header 
with a "Home" link that directs visitors to any page, that you specify
by modifying the "Home" link in the html code, when they are ready to 
leave your forum.  You can also rename this file to any name you want,
and bwfForum will automaticaly adjust to use it as it's main forum page. 

 
WARNING:  

Although bwfForum is very easy to install and use, it is very
easy to make a mistake by giving it incorrect path names.  So, follow
the directions for defining path names VERY carefully.  In the code snippet
above, the two paths are defined as:

1. $rel_path = "../babblewebforum"; 
2. <form action="../babblewebforum/bwf_put_posts.php"
 
$rel_path is the path from the main forum page html file, where you have 
added the above code snippet, to the babblewebforum directory that 
contains the bwfForum PHP code.  For the example above, it has been 
assumed that the html file is in a directory directly below the webserver 
root directory (the directory that contains your index.html file),and that 
the "babblewebforum" directory (that contains the bwfForum PHP code) is 
also directly below the webserver root directory.  So, the directory 
containing your main forum html file and the babblewebforum directory 
are in parallel directories directly below the webserver root directory. 
I am being as precise as I can here, because if you don't put the paths in 
correctly, bwfForum won't work, and it won't tell you why.

Also, notice the entry "forum_test.html" in the above html code.  You must
changes this to the file name of your main forum html file.


A WORD OF WARNING ABOUT WEB USER'S EMAIL ADDRESSES:

BwfForum was not written by a webserver security expert.  So, you
may justifiably feel that your visitors' email addresses are not
stored securely enough on your webserver.  The best solution to this
problem is to not ask for them in the first place.  If you feel
that you must collect visitors' email addresses, the easiest security  
precaution to follow is to frequently copy the 
/babblewebforum/emails/emails.txt file off of your server and delete 
it on the server.  A new emails.txt file will be created automatically 
when the next visitor enters their email address into the forum.  If you
need a more secure solution than that, you will have to come up
with it on your own.


