Shipping Cart
=======================

Introduction
------------
Test Shipping Cart

Installation
------------

The recommended way to get a working copy of this project is to clone the repository
and use `composer` to install dependencies using the `create-project` command:

    curl -s https://getcomposer.org/installer | php --
    php composer.phar create-project -sdev --repository-url="https://packages.zendframework.com" zendframework/skeleton-application path/to/install


### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName shipping-cart.localhost
        DocumentRoot /path/to/shipping-cart/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/shipping-cart/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>
