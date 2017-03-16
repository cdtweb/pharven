Pharven
=======

Do you commit your vendor directory?

If you do, then you know that adding and updating packages can lead to tons of 
file changes that need to be commited and deployed..

Pharven bundles your vendor directory into a PHP Archive (PHAR) and allows you to
commit and deploy a single file for your entire vendor directory.
 
 
## Installation

Add Pharven to your project using Composer:

    composer require cdtweb/pharven
    

## Usage

Add `pharven.json` to your project with the following content:

    {
        "config": {
            "phar_name": "pharven.phar",
            "phar_alias": "pharven.phar"
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
    
A new file named `pharven.phar` will be added to the working directory. Update your bootstrap to include the phar file instead of vendor/autoload.php and be on your way!

