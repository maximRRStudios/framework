<?php
/**
 * Created by RiDeR.
 * Date: 25.02.14
 * Time: 7:01
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
            "db" => "DataBase",
        ),
    )
);