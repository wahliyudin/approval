<?php

namespace Tbu\Approval\Models;

use Tbu\Approval\Contracts\ApprovalModelInterface;
use Tbu\Approval\Traits\HasWorkflow;
use Illuminate\Database\Eloquent\Model;

abstract class ApprovalModel extends Model implements ApprovalModelInterface
{
    use HasWorkflow;

    public $module = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function setModule($module): self
    {
        $this->module = $module;
        return $this;
    }

    abstract function getNik(): string|int|null;
}
