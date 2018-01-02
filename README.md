# aze
Lightweight PHP Framework

## aze/cli
aze-php/cli is a binary use to install and serve your aze application
```bash
composer global require "aze/cli"
```
[More about aze-php/cli](https://github.com/aze-php/cli)

## How to install
### With aze/cli
```bash
aze new
```
### With composer
```bash
composer require "aze/aze"
```
Then create an index.php file in your public directory with the following content
```php
<?php
$loader = require_once(__DIR__ . '/../vendor/autoload.php');
AZE\core\Initializer::initialize();
```
## How to serve your application
### With aze/cli
```bash
aze serve
```
### With apache
Create an .htaccess file in your public directory to redirect all requests to your index.php
```bash
RewriteEngine on
       
Options +FollowSymLinks

php_flag display_errors on

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule !\.(js|ico|gif|jpg|png|css|swf)$		/index.php
```
