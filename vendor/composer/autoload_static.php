<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd6a13361646294744741080329e45271
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '72142d7b40a3a0b14e91825290b5ad82' => __DIR__ . '/..' . '/cakephp/core/functions.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '0d59ee240a4cd96ddbb4ff164fccea4d' => __DIR__ . '/..' . '/symfony/polyfill-php73/bootstrap.php',
        '028fdea3165c4ba1ecccc83b7fec69fc' => __DIR__ . '/..' . '/cakephp/collection/functions.php',
        '948ad5488880985ff1c06721a4e447fe' => __DIR__ . '/..' . '/cakephp/utility/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php73\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Contracts\\Service\\' => 26,
            'Symfony\\Component\\Yaml\\' => 23,
            'Symfony\\Component\\Filesystem\\' => 29,
            'Symfony\\Component\\Console\\' => 26,
            'Symfony\\Component\\Config\\' => 25,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Log\\' => 8,
            'Psr\\Container\\' => 14,
            'Phinx\\' => 6,
        ),
        'L' => 
        array (
            'League\\HTMLToMarkdown\\' => 22,
        ),
        'C' => 
        array (
            'Cake\\Utility\\' => 13,
            'Cake\\Log\\' => 9,
            'Cake\\Datasource\\' => 16,
            'Cake\\Database\\' => 14,
            'Cake\\Core\\' => 10,
            'Cake\\Collection\\' => 16,
            'Cake\\Cache\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php73\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php73',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Contracts\\Service\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/service-contracts',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Symfony\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/filesystem',
        ),
        'Symfony\\Component\\Console\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/console',
        ),
        'Symfony\\Component\\Config\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/config',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Phinx\\' => 
        array (
            0 => __DIR__ . '/..' . '/robmorgan/phinx/src/Phinx',
        ),
        'League\\HTMLToMarkdown\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/html-to-markdown/src',
        ),
        'Cake\\Utility\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/utility',
        ),
        'Cake\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/log',
        ),
        'Cake\\Datasource\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/datasource',
        ),
        'Cake\\Database\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/database',
        ),
        'Cake\\Core\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/core',
        ),
        'Cake\\Collection\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/collection',
        ),
        'Cake\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/cakephp/cache',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static $classMap = array (
        'JsonException' => __DIR__ . '/..' . '/symfony/polyfill-php73/Resources/stubs/JsonException.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd6a13361646294744741080329e45271::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd6a13361646294744741080329e45271::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitd6a13361646294744741080329e45271::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitd6a13361646294744741080329e45271::$classMap;

        }, null, ClassLoader::class);
    }
}
