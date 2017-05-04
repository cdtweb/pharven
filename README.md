Pharven
=======

If you have ever needed to commit your Composer dependencies then Pharven is built for you.

Pharven bundles your vendor directory into a single PHP Archive (PHAR) and allows you to
commit and deploy a single file for your entire vendor directory.
 
 
## Installation

Add Pharven to your project using Composer:

    composer require cdtweb/pharven
    

## Usage

Add `pharven.json` to your project root with the following content:

    {
        "config": {
            "name": "pharven.phar"
        },
        "include_dirs": [
            "vendor"
        ],
        "mount_dirs": [
            "src"
        ]
    }
    
Run Pharven to create a PHAR file:

    php vendor/bin/pharven
    
A new file named `pharven.phar` will be added to the working directory. Update your bootstrap to include the phar file instead of vendor/autoload.php and you're good to go.

    <?php
    require __DIR__ . '/pharven.phar';