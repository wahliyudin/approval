<?php

namespace Tbu\Approval\Enums;

enum Status: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSE = 'close';
    case REJECT = 'reject';
    case CANCEL = 'cancel';
    case REVISION = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::OPEN => 'Open',
            self::CLOSE => 'Close',
            self::REJECT => 'Reject',
            self::CANCEL => 'Cancel',
            self::REVISION => 'Revision',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::DRAFT => '<span class="badge bg-warning-gradient fs-10">' . self::DRAFT->label() . '</span>',
            self::OPEN => '<span class="badge bg-primary-gradient fs-10">' . self::OPEN->label() . '</span>',
            self::CLOSE => '<span class="badge bg-success-gradient fs-10">' . self::CLOSE->label() . '</span>',
            self::REJECT => '<span class="badge bg-danger-gradient fs-10">' . self::REJECT->label() . '</span>',
            self::CANCEL => '<span class="badge bg-secondary-gradient fs-10">' . self::CANCEL->label() . '</span>',
            self::REVISION => '<span class="badge bg-info-gradient fs-10">' . self::REVISION->label() . '</span>',
        };
    }
}
