<?php

namespace App\Enums;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 */
enum ConnectorType: string{
     case CCS = '1';
    case CCS2 = '2';
	
	 public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
        
    }
}
