ShogChat
========
ShogChat is a simple Gitter/IRC like app written in PHP. The core code was written in a weekend by @MegaSamNinja, @PEMapModder, @TuffDev, and @Falkirks.

### Features
* IRC type chat; no chat history
* Web interface using WebSockets
* IRC bridge (mostly compliant)
* Mongo user accounts system
* GitHub integration through Personal Access Token

### Installing
#### Dependencies
* PHP 5.5 or greater
* Composer 
* Mongo and Mongo PHP Driver
* cURL and cURL extension for PHP
* A web server to run the website portion (php -S works for testing)

#### Downloading and preparing
* ```git clone [repo url]```
* ```cd [repo name]```
* ```php /path/to/composer.phar install```

#### Running
* ```cd /path/to/main/directory```
* ```php index.php``` (Starts ws and IRC)
* ```php -S localhost:80``` or whatever server you use.
    


