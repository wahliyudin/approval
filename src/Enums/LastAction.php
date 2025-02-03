<?php

namespace Tbu\Approval\Enums;

enum LastAction: string
{
    case NOTTING = 'notting';
    case SUBMIT = 'submit';
    case APPROVE = 'approve';
    case REJECT = 'reject';

    public function label()
    {
        return match ($this) {
            self::SUBMIT => 'Submitted',
            self::APPROVE => 'Approved',
            self::REJECT => 'Rejected',
            self::NOTTING => 'Notting',
        };
    }

    public static function getByValue($val): self
    {
        return match ($val) {
            self::NOTTING->value => self::NOTTING,
            self::SUBMIT->value => self::SUBMIT,
            self::APPROVE->value => self::APPROVE,
            self::REJECT->value => self::REJECT,
            default => self::NOTTING,
        };
    }

    public static function getFromService($value): self
    {
        return match ($value) {
            1 => self::NOTTING,
            2 => self::SUBMIT,
            3 => self::APPROVE,
            4 => self::REJECT,
            default => 1,
        };
    }
}
