<?php
/**
 * главный конфиг
 */
return array(
    "name"       => "TourGeneration",
    "version"    => "0.1",
    "components" => array(
        "db" => array(
            "host"    => "localhost",
            "dbname"  => "tgdb",
            "user"    => "root",
            "pass"    => "root",
            "charset" => "utf8"
        ),
        "autoload" => array(
            "db" => "classes\\storage\\DataBase",
        ),
    )
);