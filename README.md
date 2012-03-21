SG - Static site generator in PHP
=====================================

SG is a static site generator made with php.

SG rely on different open-source library:

* Symfony 2 Components:
    * [Yaml](https://github.com/symfony/Yaml)
    * [Console](https://github.com/symfony/Console)
    * [Finder](https://github.com/symfony/Finder)
* [Twig](https://github.com/fabpot/Twig)
* [Assetic](https://github.com/kriswallsmith/assetic)
* [LessPHP](https://github.com/leafo/lessphp)


Requirements
------------

PHP 5.3.6 or later.

To use the PHAR executable you need PHP to access the `phar` module.
On UNIX, in order to check whether you have this module or not, you just need to run the following command in your terminal :

    # php -m | grep -i phar

If `Phar` gets displayed then the module is properly installed.

Installation from PHAR
--------------------

1. Download the [`sg.phar`](https://github.com/maxailloud/SG/blob/master/sg.phar) executable .
2. Start using SG: `php sg.phar`

Installation from Source
------------------------

To generate your site you can use the sources instead, or if you want to develop SG.

1. Go to your project directory and run `git clone https://github.com/maxailloud/SG.git`
2. Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable
3. Run Composer to get the dependencies: `php composer.phar install`
4. Start using SG: `php sg`


License
-------

SG is licensed under the WTFPL License - see the LICENCE file for details