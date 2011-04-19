
# PTask - Parallel Processing for PHP

PTask is a library for easily implementing parallel processing of arbitrary
jobs in your PHP application.  It allows you to implement your own job processor
for your applications business logic, and handles all the details of dispatching
and aggregating job results for you via one blocking call.

PTask is built on ZeroMQ (http://zeromq.org).

# Example

To create your own basic client/server setup see the *./example* folder.

<pre>
php server.php &
php client.php
</pre>

To see something cool, try starting the client *before* the server.

# Requirements

* PHP 5
* ZeroMQ
* pcntl

*NB:* PTask does not work on Windows

## ZeroMQ

Full details for installing ZeroMQ are available on the website.

http://www.zeromq.org/intro:get-the-software

When you've done that you'll need to install the binding for PHP.

http://www.zeromq.org/bindings:php

## pcntl

The server needs to fork worker processes for handling processing jobs, and this
requires PHP has the *pcntl* module loaded.  This can either be compiled in with
PHP, or compiled as an extension and loaded from your php.ini.

### Compile with PHP

To include *pcntl* when compiling PHP just include the following flag to _configure_

<pre>
--enable-pcntl
</pre>

### Loading as a module

To load *pcntl* as a module, download the PHP source code, then...

<pre>
cd php-X.X.X/ext/pcntl
phpize
./configure
make
sudo make install
</pre>

Then add the following line to your _php.ini_

<pre>
extension=pcntl.so
</pre>
