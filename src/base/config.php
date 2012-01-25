<?php
namespace base;

class Config {

    private static $data;

    public static function init($filename) {

        if(file_exists($filename)) {

            self::$data = parse_ini_file($filename);
        }
    }

    public static function read($key) {

        $return = null;

        if(isset(self::$data[$key])) {

            $return = self::$data[$key];
        }

        return $return;
    }
}

?>
