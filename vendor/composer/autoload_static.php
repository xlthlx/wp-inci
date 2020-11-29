<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit85cde9c9654f74dda0efb9fd30164263
{
    public static $files = array (
        'a2c48002d05f7782d8b603bd2bcb5252' => __DIR__ . '/..' . '/johnbillion/extended-cpts/extended-cpts.php',
    );

    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'ExtCPTs\\Tests\\' => 14,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ExtCPTs\\Tests\\' => 
        array (
            0 => __DIR__ . '/..' . '/johnbillion/extended-cpts/tests/phpunit',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit85cde9c9654f74dda0efb9fd30164263::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit85cde9c9654f74dda0efb9fd30164263::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit85cde9c9654f74dda0efb9fd30164263::$classMap;

        }, null, ClassLoader::class);
    }
}
