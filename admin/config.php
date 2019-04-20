<?php
// HTTP
define('HTTP_SERVER', 'http://pt.loc/admin/');
define('HTTP_CATALOG', 'http://pt.loc/');

// HTTPS
define('HTTPS_SERVER', 'http://pt.loc/admin/');
define('HTTPS_CATALOG', 'http://pt.loc/');

// DIR
define('DIR_APPLICATION', '/var/www/pt.loc/admin/');
define('DIR_SYSTEM', '/var/www/pt.loc/system/');
define('DIR_IMAGE', '/var/www/pt.loc/image/');
define('DIR_STORAGE', '/var/www/storage-shopclimat24/');
define('DIR_CATALOG', '/var/www/pt.loc/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '111');
define('DB_DATABASE', 'shopclimat24');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
define('OPENCARTFORUM_SERVER', 'https://opencartforum.com/');

function custom_translit($str) {
  $tr = [
    "А" => "a",
    "Б" => "b",
    "В" => "v",
    "Г" => "g",
    "Д" => "d",
    "Е" => "e",
    "Ё" => "e",
    "Ж" => "g",
    "З" => "z",
    "И" => "i",
    "Й" => "J",
    "К" => "k",
    "Л" => "l",
    "М" => "m",
    "Н" => "n",
    "О" => "o",
    "П" => "p",
    "Р" => "r",
    "С" => "s",
    "Т" => "t",
    "У" => "u",
    "Ф" => "f",
    "Х" => "h",
    "Ц" => "ts",
    "Ч" => "ch",
    "Ш" => "sh",
    "Щ" => "sch",
    "Ъ" => "a",
    "Ы" => "y",
    "Ь" => "",
    "Э" => "e",
    "Ю" => "yu",
    "Я" => "ya",
    "Ї" => "ji",
    "Ґ" => "g",
    "І" => "I",
    "а" => "a",
    "б" => "b",
    "в" => "v",
    "г" => "g",
    "д" => "d",
    "е" => "e",
    "ё" => "e",
    "ж" => "g",
    "з" => "z",
    "и" => "i",
    "й" => "j",
    "к" => "k",
    "л" => "l",
    "м" => "m",
    "н" => "n",
    "о" => "o",
    "п" => "p",
    "р" => "r",
    "с" => "s",
    "т" => "t",
    "у" => "u",
    "ф" => "f",
    "х" => "h",
    "ц" => "ts",
    "ч" => "ch",
    "ш" => "sh",
    "щ" => "sch",
    "ъ" => "a",
    "ы" => "y",
    "ь" => "",
    "э" => "e",
    "ю" => "yu",
    "я" => "ya",
    "ї" => "ji",
    "і" => "i",
    "ґ" => "g",
    "Є" => "e",
    "є" => "e",
    "ў" => "u",
    "Ў" => "U",
    "і" => "i",
    "І" => "I",
    " " => '-'
  ];

  $str = strtr($str, $tr);
  $str = strtolower(str_replace(' ','-', $str));
  unset ($tr);
  return $str;
}
