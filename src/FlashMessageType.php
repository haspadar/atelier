<?php

namespace Atelier;

enum FlashMessageType
{
    case SUCCESS;
    case ERROR;
    case WARNING;

    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $status) {
            if( $name === $status->name ){
                return $status;
            }
        }

        return null;
    }
}