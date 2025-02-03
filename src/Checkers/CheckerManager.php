<?php

namespace Tbu\Approval\Checkers;

use Tbu\Approval\Checkers\Model\DefaultChecker;
use Tbu\Approval\Checkers\Model\ModelChecker;
use Tbu\Approval\Checkers\Model\QueryChecker;
use Illuminate\Database\Eloquent\Model;

class CheckerManager
{
    public function __construct(
        protected Model $model
    ) {}

    public function getChecker(): ModelChecker
    {
        if ($this->model->relationLoaded('workflows')) {
            return new DefaultChecker($this->model);
        }
        return new QueryChecker($this->model);
    }
}
