Pharven
=======

[![Build
Status](https://travis-ci.org/cdtweb/pharven.svg?branch=master)](https://travis-ci.org/cdtweb/pharven)

If you have ever needed to commit your `vendor/` directory into your project repository then Pharven was built for you.

Pharven bundles your vendor directory into a single PHP Archive (PHAR) and allows you to
commit and deploy a single file instead of your entire `vendor/` directory.
 
 
## Installation

Add Pharven to your project using Composer:

    composer require cdtweb/pharven
    

## Usage

Add `pharven.json` to your project root with the following content:

    {
        "include_dirs": [
            "vendor"
        ],
        "mount_dirs": [
            "src"
        ]
    }
    
Run Pharven to create a PHAR file:

    php vendor/bin/pharven
    
A new file named `pharven.phar` will be added to the working directory. Update your bootstrap to include `./pharven.phar` instead of `vendor/autoload.php` and you're good to go.

    <?php
    require __DIR__ . '/pharven.phar';