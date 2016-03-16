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
            "db"          => "classes\\main\\storage\\DataBase",
            "request"     => "classes\\main\\http\\Request",
            "httpClient"  => "classes\\main\\http\\HttpClient",
            "route"       => "classes\\main\\controller\\Routing",
            "template"    => "classes\\main\\template\\Template",
            "localCache"  => "classes\\main\\storage\\Apc",
            "globalCache" => "classes\\main\\storage\\Memcached",
            "fileCache"   => "classes\\main\\storage\\File",
            "social"      => "classes\\main\\social\\SocialManager",
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