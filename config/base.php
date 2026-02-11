<?php

$path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
/**
 * главный конфиг
 */
return array(
    'basePath' => $path,
    "name"       => "framework",
    "version"    => "0.1",
    "components" => array(
        "db"       => array(
            "host"    => "localhost",
            "dbname"  => "dbname",
            "user"    => "root",
            "pass"    => "root",
            "charset" => "utf8"
        ),
        'cache_global' => array(
            'servers' => array(
                array('ip', 'port', 0),
            ),
            'compression' => true,
            'prefix' => 'prefix'
        ),
        'cache_local' => array(
            'prefix' => 'prefix',
        ),
        'cache_file' => array(
            'path_to_files' => "{$path}/data/libraries",
        ),
        "autoload" => array(
            "db"          => "App\\Main\\Storage\\DataBase",
            "request"     => "App\\Main\\Http\\Request",
            "httpClient"  => "App\\Main\\Http\\HttpClient",
            "route"       => "App\\Main\\Controller\\Routing",
            "template"    => "App\\Main\\Template\\Template",
            "localCache"  => "App\\Main\\Storage\\Apc",
            "globalCache" => "App\\Main\\Storage\\Memcached",
            "fileCache"   => "App\\Main\\Storage\\File",
            "social"      => "App\\Main\\Social\\SocialManager",
        ),
        "routing"  => array(
            "parameter" => "route",
        ),
        'smarty' => array(
            'template_dir' => "{$path}/templates/html",
            'template_compile_dir' => "{$path}/data/templates_c",
        ),
        'redis' => array(
            'host' => array(
                'tcp://localhost:6380',
            ),
            'db' => '1',
            'prefix' => 'prefix'
        ),
        'mailer' => array(
            'smtp' => false,
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'auth' => true,
            'login' => 'username@gmail.com',
            'pass' => '*****',
            'from' => 'from@example.com',
        ),
    )
);