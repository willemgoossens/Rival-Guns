<?php
    // DB Params
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'rivalguns');

    // App Root
    define('APPROOT', dirname(dirname(__FILE__)));
    // Root
    define('ROOT', dirname(dirname(dirname(__FILE__))));
    // URL Root
    // IMPORTANT: DO *NOT* ADD A / AT THE END OF THE URL
    define('URLROOT', '/Projecten/Rival_Guns');
    // Site Name
    define('SITENAME', 'Rival Guns');
    // App Version
    define('APPVERSION', '0.1.0');
    // Environment - development or production
    define('ENVIRONMENT', 'development');

    //Namespaces
    define('CONTROLLER_NAMESPACE', 'App\\Controllers\\');
    define('API_CONTROLLER_NAMESPACE', 'App\\Controllers\\API\\');
    define('EXECUTABLE_NAMESPACE', 'App\\Executables\\');
    define('MODEL_NAMESPACE', 'App\\Models\\');
    define('MIDDLEWARE_NAMESPACE', 'App\\Middleware\\');

    // Encrypt Salt
    // This string is use as a salt for encrypting keys
    // E.g. the resetKey
    // Note: the password encryption function uses the default salt by password_hash()
    define('ENCRYPT_SALT', '(§è$$$\@@@sfqsdfsdfsfe56)');


    /*************************************
    *
    *   ERROR PAGES
    *
    **************************************/
    define('ERROR_PAGES', [
        '404' => ['page' => 'pages/error404',
                  'api'  => 'HTTP/1.1 404 Not Found'],
        '400' => ['page' => 'pages/error400',
                  'api'  => 'HTTP/1.1 400 Bad Request'],
        '500' => ['page' => 'pages/error500',
                  'api'  => 'HTTP/1.1 500 Internal Server Error']
    ]);


    /*************************************
    *
    *   DO NOT CHANGE THIS PART!
    *
    **************************************/
    define('COPYRIGHT_URL', 'http://willemgoossens.be');
    define('COPYRIGHT', 'Willem Goossens');
