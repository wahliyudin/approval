<?php

namespace Tbu\Approval\Enums;

enum Approval: string
{
    case SUBMITTED = 'SUBMITTED';
    case ATASAN_LANGSUNG = 'ATASAN_LANGSUNG';
    case DIRECTOR = 'DIRECTOR';
    case GENERAL_MANAGER = 'GENERAL_MANAGER';
    case DEPARTMENT_HEAD = 'DEPARTMENT_HEAD';
    case PROJECT_OWNER = 'PROJECT_OWNER';
    case FINANCE = 'FINANCE';
    case HCA = 'HCA';
    case GENERAL_MANAGER_OPERATION = 'GENERAL_MANAGER_OPERATION';
    case DIVISION_HEAD = 'DIVISION_HEAD';
    case ATASAN_TIDAK_LANGSUNG = 'ATASAN_TIDAK_LANGSUNG';
    case FINANCE_SITE = 'FINANCE_SITE';
    case PROCUREMENT = 'PROCUREMENT';
    case ASSET = 'ASSET';
    case OTHER = 'OTHER';

    public function valueByHRIS()
    {
        return match ($this) {
            self::SUBMITTED => 0,
            self::ATASAN_LANGSUNG => 1,
            self::DIRECTOR => 2,
            self::GENERAL_MANAGER => 3,
            self::DEPARTMENT_HEAD => 4,
            self::PROJECT_OWNER => 5,
            self::FINANCE => 6,
            self::HCA => 7,
            self::GENERAL_MANAGER_OPERATION => 8,
            self::OTHER => 9,
            self::DIVISION_HEAD => 10,
            self::ATASAN_TIDAK_LANGSUNG => 11,
            self::FINANCE_SITE => 12,
            self::PROCUREMENT => 14,
            self::ASSET => 15,
            default => null
        };
    }

    public function label()
    {
        return match ($this) {
            self::SUBMITTED => 'Submitted',
            self::ATASAN_LANGSUNG => 'Atasan Langsung',
            self::DIRECTOR => 'Director',
            self::GENERAL_MANAGER => 'General Manager SCA',
            self::DEPARTMENT_HEAD => 'Department Head',
            self::PROJECT_OWNER => 'Project Owner',
            self::FINANCE => 'Finance',
            self::HCA => 'HCA',
            self::GENERAL_MANAGER_OPERATION => 'General Manager Operation',
            self::OTHER => 'Other',
            self::DIVISION_HEAD => 'Division Head',
            self::ATASAN_TIDAK_LANGSUNG => 'Atasan Tidak Langsung',
            self::FINANCE_SITE => 'Finance Site',
            self::PROCUREMENT => 'Procurement',
            self::ASSET => 'Asset',
            default => null
        };
    }

    public static function byValue(string $val): self|null
    {
        return match ($val) {
            self::SUBMITTED->value => self::SUBMITTED,
            self::ATASAN_LANGSUNG->value => self::ATASAN_LANGSUNG,
            self::DIRECTOR->value => self::DIRECTOR,
            self::GENERAL_MANAGER->value => self::GENERAL_MANAGER,
            self::DEPARTMENT_HEAD->value => self::DEPARTMENT_HEAD,
            self::PROJECT_OWNER->value => self::PROJECT_OWNER,
            self::FINANCE->value => self::FINANCE,
            self::HCA->value => self::HCA,
            self::GENERAL_MANAGER_OPERATION->value => self::GENERAL_MANAGER_OPERATION,
            self::OTHER->value => self::OTHER,
            self::DIVISION_HEAD->value => self::DIVISION_HEAD,
            self::ATASAN_TIDAK_LANGSUNG->value => self::ATASAN_TIDAK_LANGSUNG,
            self::FINANCE_SITE->value => self::FINANCE_SITE,
            self::PROCUREMENT->value => self::PROCUREMENT,
            self::ASSET->value => self::ASSET,
            default => null
        };
    }

    public static function getFromService($value): self
    {
        return match ($value) {
            0 => self::SUBMITTED,
            1 => self::ATASAN_LANGSUNG,
            2 => self::DIRECTOR,
            3 => self::GENERAL_MANAGER,
            4 => self::DEPARTMENT_HEAD,
            5 => self::PROJECT_OWNER,
            6 => self::FINANCE,
            7 => self::HCA,
            8 => self::GENERAL_MANAGER_OPERATION,
            9 => self::OTHER,
            10 => self::DIVISION_HEAD,
            11 => self::ATASAN_TIDAK_LANGSUNG,
            12 => self::FINANCE_SITE,
            14 => self::PROCUREMENT,
            15 => self::ASSET,
            default => null,
        };
    }
}
