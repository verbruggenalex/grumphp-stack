<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite8a1da9fa2045d4227c965b12e291937
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Finder\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite8a1da9fa2045d4227c965b12e291937::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite8a1da9fa2045d4227c965b12e291937::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}