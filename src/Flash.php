<?php

namespace Atelier;

class Flash
{
    public static function addWarning(string $message)
    {
        self::add(new FlashMessage($message, FlashMessageType::WARNING));
    }

    public static function addError(string $message)
    {
        self::add(new FlashMessage($message, FlashMessageType::ERROR));
    }

    public static function addSuccess(string $message)
    {
        self::add(new FlashMessage($message, FlashMessageType::SUCCESS));
    }

    public static function receive(): ?FlashMessage
    {
        $message = $_COOKIE['flash_message'] ?? '';
        unset($_COOKIE['flash_message']);
        setcookie('flash_message', '', -1, '/');

        return unserialize($message) ?: null;
    }

    private static function add(FlashMessage $message)
    {
        setcookie(
            'flash_message',
            serialize($message),
            time() + 60 * 10,
            '/'
        );
    }
}