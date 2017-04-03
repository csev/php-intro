<!DOCTYPE html>
<?php
$string = file_get_contents("peer.json");
$json = json_decode($string);
if ( $json === null ) {
    echo("<pre>\n");
    echo("Invalid JSON:\n\n");
    echo($string);
    echo("</pre>\n");
    die("<p>Internal error contact instructor</p>\n");
}
?>
<html>
<head>
<title>Assignment: <?= $json->title ?></title>
<style>
li {padding-top: 0.5em;}
pre {padding-left: 2em;}
</style>
</head>
<body style="margin-left:5%; margin-bottom: 60px; margin-right: 5%; font-family: sans-serif;">
<h1>Assignment: <?= $json->title ?></h1>
<p>
<?= $json->description ?>
</p>
<?php if ( isset($json->solution) ) { ?>
<h2>Sample solution</h2>
<p>
You can explore a sample solution for this problem at
<pre>
<a href="<?= $json->solution ?>" target="_blank"><?= $json->solution ?></a>
</pre>
<?php } ?>
<h1>Resources</h1>
<p>There are several resources you might find useful:
<ul>
<li>Recorded lectures, sample code and chapters from
<a href="http://www.wa4e.com" target="_blank">www.wa4e.com</a>:
<ul>
<li class="toplevel">
Review the SQL language
</li>
<li class="toplevel">
Using PDO in PHP
</li>
<li>JavaScript</li>
</li>
<li>How to validate a form in JavaScript on 
<a href="http://stackoverflow.com/questions/32410590/form-submission-after-javascript-validation"
target="_blank">StackOverflow</a>.
</ul>
</li>
<li>Documentation from www.php.net on how to use
<a href="http://php.net/manual/en/book.pdo.php"
target="_blank">PDO</a> to talk to a database.
</li>
</ul>
</p>
<h2 clear="all">General Specifications</h2>
<p>
Here are some general specifications for this assignment:
<ul>
<li>
You must use the PHP PDO database layer for this assignment.
</li>
<li>
Your name must be in the title tag of the HTML for all of the pages
for this assignment.
</li>
<li>
All data that comes from the users must be properly escaped
using the <b>htmlentities()</b> function in PHP.  You do not 
need to escape text that is generated by your program.
</li>
<li>
You must follow the POST-Redirect-GET pattern for all POST requests.
This means when your program receives and processes a POST request, 
it must not generate any HTML as the HTTP response to that request.
It must use the "header('Location: ...');" function and either "return"
or "exit();" to send the location header and redirect the browser
to the same or a different page.
</li>
<li>
All error messages must be "flash-style" messages where the message is 
passed from a POST to a GET using the SESSION.
</li>
<li>
Please do not use HTML5 in-browser data 
validation (i.e. type="number") for the fields 
in this assignment as we want to make sure you can properly do server 
side data validation.  And in general, even when you do client-side
data validation, you should still validate data on the server in case
the user is using a non-HTML5 browser.
</li>
</ul>
<h2 clear="all">Databases and Tables Required for the Assignment</h2>
<p>
You will need to have a <b>users</b> table as follows:
<pre>
CREATE TABLE users (
   user_id INTEGER NOT NULL AUTO_INCREMENT KEY,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users ADD INDEX(email);
ALTER TABLE users ADD INDEX(password);
</pre>
You will also need to add a <b>Profile</b> table as follows:
<pre>
CREATE TABLE Profile (
  profile_id INTEGER NOT NULL KEY AUTO_INCREMENT,
  user_id INTEGER NOT NULL,
  first_name TEXT,
  last_name TEXT,
  email TEXT,
  headline TEXT,
  summary TEXT,

  CONSTRAINT profile_ibfk_2
        FOREIGN KEY (user_id)
        REFERENCES users (user_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
</pre>
This table has a foreign key to the <b>users</b> table.
</p>
<h2 clear="all">The Screens for This Assignment</h2>
<p>
We are going to have a number of screens (files) for this assignment.  Functionality will
be moved around from the previous assignment, although much of the code from the 
previous assignment can be adapted with some refactoring.
<ul>
<li>
<b>index.php</b> Will present a list of all profiles in the system with a link to
a detailed view with <b>view.php</b> whether or not you are logged in. 
If you are not logged in, you will be given a link to <b>login.php</b>.
If you are logged in you will see a link to <b>add.php</b> add a new resume and 
links to delete or edit any resumes that are owned by the logged in user. 
</li>
<li><b>login.php</b> will present the user the login screen with an email address
and password to get the user to log in.  If there is an error, redirect the user back
to the login page with a message.  If the login is successful, redirect the user back
to <b>index.php</b> after setting up the session.  In this assignment, you will need to 
store the user's hashed password in the <b>users</b> table as described below.
</li>
<li><b>logout.php</b> will log the user out by clearing data in the session and redirecting back
to <b>index.php</b>.  This file can be very short - similar to the following:
<pre>
session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
header('Location: index.php');
</pre>
</li>
<li>
<b>add.php</b> add a new Profile entry.  Make sure to mark the entry with the 
foreign key
<b>user_id</b> of the currently logged in user. (create)
</li>
<li>
<b>view.php</b> show the detail for a particular entry.  This works even is the user 
is not logged in. (read)
</li>
<li>
<b>edit.php</b> edit an exsiting entry in the database.  
Make sure the user is logged in, that
the entry actually exists, and that the current logged in user owns the entry in the database.
(update)
</li>
<li>
<b>delete.php</b> delete an entry from the database.  
Do not do the delete in a GET - you must put up a verification screen and do the 
actual delete in a POST request, after which you redirect back to index.php wih a 
success message.
Before you do the delete, make sure the user is logged in, that
the entry actually exists, and that the current logged in user owns the entry in the database.
(delete)
</li>
</ul>
<p>
If the user goes to an add, edit, or delete script without being logged in, 
die with a message of "Not logged in".
</p>
<p>
You might notice that there are several common operations across these files.   You might want to build 
a set of utility functions to avoid copying and pasting the same code over and over across several 
files.
</p>
<h2 clear="all">Storing Users and Hashed Password in the Database</h2>
<p>
In this assignment, we are going to allow for more than one user to log into our
system so we will switch from storing the account and hashed password in PHP
strings to storing them in the database.   The salt value will remain in the PHP
code.
<p>
Once you create the <b>users</b> table above, you will need to 
insert a single user record into the "users" table
using this SQL:
<pre>
INSERT INTO users (name,email,password)
    VALUES ('UMSI','umsi@umich.edu','1a52e17fa899cf40fb04cfc42e6352f1');
</pre>
The above password is the salted MD5 hash of 'php123' using a salt
of 'XyZzy12*_'.  You can compute the salted hash of any password with the
following PHP code:
<pre>
$md5 = hash('md5', 'XyZzy12*_secret456');
echo($md5."\n");
</pre>
The salt value remains in the PHP code while the stored hash moves into
the database.  There should be no stored hash in your PHP code.
</p>
<p>
You will need this user in the database to pass the assignment.  You can 
add other users to the database is you like.
</p>
<p>
Since the email address salted hash is stored in the database, we must use 
a different approach than in the previous assignment to check to see if the 
email and password match using the following approach:
<pre>
$check = hash('md5', $salt.$_POST['pass']);
$stmt = $pdo-&gt;prepare('SELECT user_id, name FROM users
    WHERE email = :em AND password = :pw');
$stmt-&gt;execute(array( ':em' =&gt; $_POST['email'], ':pw' =&gt; $check));
$row = $stmt-&gt;fetch(PDO::FETCH_ASSOC);
</pre>
Since we are checking if the stored hashed password matches the hash computation of
the user-provided password, If we get a row, then the password matches, if we don't
get a row (i.e.  $row is false)  then the password did not match.
If he password matches, put the user_id value for the user's row
into session as well as the user's name:
<pre>
if ( $row !== false ) {
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
...
</pre>
<h2 clear="all">Login Data Validation in JavaScript</h2>
<p>
In addition to the PHP data validation in the previous assignment, you need to add
JavaScript based data validation on the <b>login.php</b> screen that pops up an alert()
dialog if teither field is blank or the email address is missing.
</p>
<center>
<a href="01-Js-Validation.png" target="_blank">
<img 
alt="Image of the JavaScript popup"
width="80%" src="01-Js-Validation.png" border="2"/>
</a>
</center>
<p>
This is done using an <b>onclick</b> event on the form submit button that calls a JavaScript
function that checks the data, puts up an alert box if there is a problem and then returns
<b>true</b> or <b>false</b> depending on the validity of the data.
<pre>
...
&lt;input type="password" name="pass" id="id_1723">
&lt;input type="submit" onclick="return doValidate();" value="Log In">
...
</pre>
</p>
<p>
This is a partial implementation of the doValidate() function that only checks
the <b>password</b> field.
<pre>
function doValidate() {
    console.log('Validating...');
    try {
        pw = document.getElementById('id_1723').value;
        console.log("Validating pw="+pw);
        if (pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</pre>
<p>
Make sure to retain the PHP data validation checks as well given that any in-browser
checks can be bypassed by a determinied end-user.
</p>
<h2 clear="all">Profile Data validation</h2>
<p>
When you are reading data in <b>add.php</b> or <b>edit.php</b>, do the following data 
validation:
<ul>
<li>
All fields are required.  If one of the fields is left blank put out a message like
<pre style="color:red">
All fields are required
</pre>
and redirect back to the same page.
</li>
<li>
The email address must include an @ sign in the text.  If there is no @ sign in the
email address issue a message of the form:
<pre style="color:red">
Email address must contain @
</pre>
and redirect back to the same page.
</li>
</ul>
To redirect back to the same page when the page requires a GET parameter (i.e. like 
edit), you need to add the GET parameter to the URL that you put in the location
header using a technique similar to the following:
<pre>
header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
</pre>
You may need to change <b>profile_id</b> to match the GET parameter your code
is expecting and the name of the hidden parameter in your form (and $_POST)
data.
</p>
<h1>What To Hand In</h1>
<p>
As a reminder, your code must meet all the specifications
(including the general specifications) above.  Just having good screen shots
is not enough - we will look at your code to see if you made coding errors.
For this assignment you will hand in:
<ol>
<?php
foreach($json->parts as $part ) {
    echo("<li>$part->title</li>\n");
}
?>
</ol>
<h1><em>Optional</em> Challenges</h1>
<p>
<b>This section is entirely <em>optional</em> and is here in case you want to 
explore a bit more deeply and test your code skillz.</b></p>
<p>
Here are some possible improvements:
<ul>
<li>
Add an optional URL field to your tables and user interface.  
Validate the URL to make sure it starts with 
"http://" or "https://".  It is OK for this to be blank.
If this is non-blank show the image in the table view in
index.php and in the view.php file.
</li>
<li>
Medium Difficulty: Use the PHP cURL library to do a GET to the 
image URL from within PHP and if the URL does not exist, 
issue an error message to the user and do not add the
profile.
</li>
<li>
<b>This is a bit tricky so please don't try if it feels 
confusing.</b>  Change the program so it supports 
multiple users and each user can only edit or delete
profiles that match their <b>user_id</b>.
Insert a second row into the <b>users</b>
table with the same or a diffferent hashed password.
This way you can log in with one user name, add some 
profiles, logout and log in as another user, add some profiles
and then logout and log back in as the original user and
the Edit/Delete buttons will only appear for the 
profiles owned by the user.
</li>
<li>
Advanced: Change the <b>index.php</b> so that it has a search field.
Use the LIKE operator in the WHERE clause.  You can use
a LIKE operator on any column (including numbers) and you 
can use the LIKE column on all of the columns as well.
</li>
<li>
Super Advanced: If there are more than 10 profiles, only show 10 at
a time and put up Next and Back buttons as appropriate.  Use count 
query to determine number of rows and a a LIMIT
clause in your tables query to return the correct range of rows.
</li>
</ul>
</p>
<p>
Provided by: <a href="http://www.wa4e.com/" target="_blank">
www.wa4e.com</a> <br/>
</p>
<center>
Copyright Creative Commons Attribution 3.0 - Charles R. Severance
</center>
</body>
</html>
