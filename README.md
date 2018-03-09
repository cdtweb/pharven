Pharven
=======

[![Build
Status](https://travis-ci.org/cdtweb/pharven.svg?branch=master)](https://travis-ci.org/cdtweb/pharven)

If you have ever needed to commit your `vendor/` directory into your project repository then Pharven was built for you.

Pharven bundles your vendor directory into a single PHP Archive (PHAR) and allows you to
commit and deploy a single file instead of your entire `vendor/` directory.
 
 
## Installation

Add Pharven to your project using Composer:

    composer require axute/pharven
    

## Usage

Add `pharven.json` to your project root with the following content:

    {
        "mount_dirs": [
            "src"
        ]
    }
In this case, the directory will tried mounted every time you include the phar.
**mount_dirs** are important if you are using Composer to autoload your project classes. `.phar` files act as their own file system, so if your classes aren't mounted, they won't be visible to the Composer autoloader.
**But for PhpStorm its not possible to debug in mounted directories.**


    
For Debugging or not mounted dirs this is also possible, but you need absolute paths

    $autoload = require_once __DIR__ . DIRECTORY_SEPARATOR . 'pharven.phar';
    $autoload->addPsr4('',__DIR__.DIRECTORY_SEPARATOR.'src');
    
    
Run Pharven to create/update your PHAR file:

    php vendor/bin/pharven
    
A new file named `pharven.phar` will be added to the working directory. Update your bootstrap to include `./pharven.phar` instead of `vendor/autoload.php` and you're good to go.

    <?php
    require __DIR__ . '/pharven.phar';
    
## Debugging

mounted dirs can not be debugged (with PhpStorm)

