Twitstream
==========

A lightweight client/server framework for public twitter displays.

Features
--------

* Client written in HTML/JavaScript - runs on anything with a browser
* Customisable styles
* All Twitter interaction is server side - client just acts as a wrapper
* Keeps complete history by default

Requirements
------------

* A box or VM running PHP 5, to which you have SSH access
* A box or VM with Apache/PHP (may be the same box)
* A MySQL database accessable by both of the above

Installation
------------

* Copy the contents of 'server' to a directory within your home
* Modify the settings.json file to include your MySQL login credentials

* Create the tables in the MySQL database (use the file create_tables.sql)

* Copy the contents of 'client' into public_html, /var/www or similar
* Modify the settings.json file so that it includes a finer search query plus
  any terms to exclude
* [Optional] Modify settings.json to point to a custom stylesheet

Testing
-------

Testing the server component is achieved by running the script 'search',
which is located within the 'server' directory. It takes one or two
arguments. The first is a search query, the second is a repeat interval.
For testing purposes we can omit the second argument.

Run the search script as follows:

./search DrAshSmith

This will import every recent tweet containing the string 'DrAshSmith'
into your database. You can check the import succeeded by looking in the table
'Twitter'. If it contains rows, the import succeeded.

Running
-------

Once the test has been complete, you can now run the server component.
Running 'search' with two arguments will put the program into an infinite loop
(stop the process with Ctrl-C, or kill -1 if running in the background).
The second argument is the number of seconds to wait between searches.
You don't really want this to be too low, or you'll get blocked by Twitter
for hammering. However, if you're searching for a busy hashtag you don't
want it too high in case you miss tweets. Start with 30 and decrease until
optimal.

Now it's a simple case of opening the client index.php script in a web
browser.
