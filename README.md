# push file manager
![Website Preview](https://raw.githubusercontent.com/JannisBecker/push/master/res/img/readme-header.png)

## Introduction
This is a slim and performant implementation of a web file management service,
including file upload, rename, move and removal capabilities, folder support, user accounts,
batch file operations and more!

## Interactive demo
I hosted a demo on my website, which can be found [here](http://pushdemo.jannisbecker.me).

## First setup
First of all, use the /res/sql/push-db.sql file to set up the required MySQL databases. 
This is where you can add accounts for your users later on.

After that, there are two neccessary file changes to setup push on your web server.
First look at /res/php/sql-config.template.php, fill in the required data
and finally rename the file to sql-config.php .
Then open up /upload/index.template.php, again filling in the data required,
which in this case is a simple key needed for Upload to work.
Again, rename this file to index.php after you're done,
and you're good to go!


