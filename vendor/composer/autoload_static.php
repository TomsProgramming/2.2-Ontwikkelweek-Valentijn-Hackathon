<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit690e04a27fa241bad9da145ee44cf2eb
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'YoHang88\\LetterAvatar\\' => 22,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Predis\\' => 7,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'I' => 
        array (
            'Intervention\\Image\\' => 19,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'YoHang88\\LetterAvatar\\' => 
        array (
            0 => __DIR__ . '/..' . '/yohang88/letter-avatar/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Intervention\\Image\\' => 
        array (
            0 => __DIR__ . '/..' . '/intervention/image/src/Intervention/Image',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit690e04a27fa241bad9da145ee44cf2eb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit690e04a27fa241bad9da145ee44cf2eb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit690e04a27fa241bad9da145ee44cf2eb::$classMap;

        }, null, ClassLoader::class);
    }
}
