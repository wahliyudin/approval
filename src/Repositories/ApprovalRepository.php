<?php

namespace Tbu\Approval\Repositories;

use BackedEnum;
use Tbu\Approval\Contracts\ApprovalRepositoryInterface;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    public function getByModule(BackedEnum $module) {}

    public function getWorkflows($payload) {}

    public function getEmployees(array $niks) {}
}
