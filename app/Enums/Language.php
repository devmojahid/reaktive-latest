<?php

namespace App\Enums;

enum Language: string
{
    case English = 'English';
    case Spanish = 'Spanish';
    case French = 'French';
    case German = 'German';
    case Italian = 'Italian';
    case Chinese = 'Chinese';
    case Japanese = 'Japanese';
    case Arabic = 'Arabic';
    case Russian = 'Russian';
   case Bangla= 'Bangla';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
