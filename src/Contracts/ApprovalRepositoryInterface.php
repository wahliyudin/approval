<?php

namespace Tbu\Approval\Contracts;

use BackedEnum;

interface ApprovalRepositoryInterface
{
    public function getByModule(BackedEnum $module);

    public function getWorkflows($payload);

    public function getEmployees(array $niks);
}
