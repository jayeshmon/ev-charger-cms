<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 */
final class TransactionMode extends Enum {
    const Mobile =   'mobile';
    const Upi =   'upi';
	const Web =   'web';
	const Whatspp =   'whatsapp';
	const Aeps =  'aeps';
	const Remote =   'remote';
}
