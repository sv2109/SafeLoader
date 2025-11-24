<?php

require_once DIR_SYSTEM . 'library/safeloader_exception.php';

/**
 * ionCube Loader errors handler
 * 
 * @author  sv2109
 * @link    sv2109@gmail.com
*/

function ioncube_event_handler($err_code, $params) {

    $filename = basename(__FILE__, '.php');
    $filename = preg_replace('/_error$/', '', $filename);
    $module_name = ucwords(str_replace('_', ' ', $filename));

    $descriptions = [
        ION_CORRUPT_FILE               => "Файл повреждён (ION_CORRUPT_FILE)",
        ION_EXPIRED_FILE               => "Срок действия файла истёк (ION_EXPIRED_FILE)",
        ION_NO_PERMISSIONS             => "Ошибка доступа (ION_NO_PERMISSIONS)",
        ION_CLOCK_SKEW                 => "Смещение системного времени (ION_CLOCK_SKEW)",
        ION_LICENSE_NOT_FOUND          => "Лицензия не найдена (ION_LICENSE_NOT_FOUND)",
        ION_LICENSE_CORRUPT            => "Лицензия повреждена (ION_LICENSE_CORRUPT)",
        ION_LICENSE_EXPIRED            => "Срок действия лицензии истёк (ION_LICENSE_EXPIRED)",
        ION_LICENSE_PROPERTY_INVALID   => "Некорректное свойство лицензии (ION_LICENSE_PROPERTY_INVALID)",
        ION_LICENSE_HEADER_INVALID     => "Некорректный заголовок лицензии (ION_LICENSE_HEADER_INVALID)",
        ION_LICENSE_SERVER_INVALID     => "Ошибка сервера лицензий (ION_LICENSE_SERVER_INVALID)",
        ION_UNAUTH_INCLUDING_FILE      => "Несанкционированное подключение файла (ION_UNAUTH_INCLUDING_FILE)",
        ION_UNAUTH_INCLUDED_FILE       => "Несанкционированное включение файла (ION_UNAUTH_INCLUDED_FILE)",
        ION_UNAUTH_APPEND_PREPEND_FILE => "Несанкционированное добавление кода (ION_UNAUTH_APPEND_PREPEND_FILE)"
    ];

    $message = "Fatal error in " . $module_name . " module. Please, contact a developer sv2109@gmail.com \n\n";

    $message .= $descriptions[$err_code] ?? "Unknown error ({$err_code})";

    if (isset($_GET['debug_safeloader'])) {
        $message .= "\n\n" . print_r($params, true);
    }

    throw new SafeLoaderException($module_name, $message, $err_code);
}