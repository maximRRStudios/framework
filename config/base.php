<?php

$path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
/**
 * главный конфиг
 */
return array(
    'basePath' => $path,
    "name"       => "TourGeneration",
    "version"    => "0.1",
    "components" => array(
        "db"       => array(
            "host"    => "localhost",
            "dbname"  => "tgdb",
            "user"    => "root",
            "pass"    => "root",
            "charset" => "utf8"
        ),
        "autoload" => array(
            "db"         => "classes\\main\\storage\\DataBase",
            "request"    => "classes\\main\\http\\Request",
            "httpClient" => "classes\\main\\http\\HttpClient",
            "route"      => "classes\\main\\controller\\Routing",
        ),
        "routing"  => array(
            "parameter" => "method",
        ),
    )
);