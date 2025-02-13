<?php

namespace AlexeyGfi\ErrorLog;

/**
 * AlexeyGfi | alexeygfi@gmail.com
 * Кастомный класс для логирования ошибок в Битриксе
 * Мы перехватываем поток вывода ошибок и дополняем его информацией о том, на каком урле возникла ситуация.
 * Зачастую мы информацию об ошибке имеем, но не знаем, как её воспроизвести, потому что возникать она может в специфических или конкретных условиях.
 * По указанному примеру можно дополнять информацию об ошибках и другой сопроводительной информацией.
 *
 * Класс предназначается для использования в настройках вывода ошибок и предупреждений — в файле /bitrix/.settings.php
 */

// Пример подключения класса в настройках вывода ошибок:
// 'log' => array(
//    'class_name' => '\AlexeyGfi\ErrorLog\CustomHandler',
//    // !!! путь относительно "/bitrix/" или "/local/":
//    'required_file' => 'lib/AlexeyGfi/ErrorLog/CustomHandler.php',
//
//    'settings' => array(
//        'file' => 'bitrix/modules/error.log',
//        'log_size' => 1000000,
//    ),
//),

use Bitrix\Main\Context;
use Bitrix\Main\Diag\ExceptionHandlerFormatter;
use Bitrix\Main\Diag\FileExceptionHandlerLog;

class CustomHandler extends FileExceptionHandlerLog
{
    public function write($exception, $logType): void
    {
        // Стандартный форматировщик ошибки
        $text = ExceptionHandlerFormatter::format($exception, false);

        // Пример фильтрации ошибок по их типу
//        if (
//            str_contains($text, 'E_WARNING')
//        ) {
//            return;
//        }

        $server = Context::getCurrent()->getServer();

        $requestUri = $server->getRequestUri();
        $text .= 'RQ: ' . $requestUri . "\n";

        $this->logger->debug(
            date('Y-m-d H:i:s')
            . ' - Host: ' . $server->getHttpHost()
            . ' - ' . static::logTypeToString($logType)
            . ' - ' . $text . "\n"
        );
    }
}