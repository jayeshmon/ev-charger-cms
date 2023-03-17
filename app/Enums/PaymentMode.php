<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 */
final class TransactionMode extends Enum {
    const PersonalCredit =   'personal credit';
    const Upi =   'upi';
	const Aeps =   'aeps';
}
