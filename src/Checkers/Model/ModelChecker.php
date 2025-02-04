<?php

namespace Tbu\Approval\Checkers\Model;

use Tbu\Approval\Contracts\WorkflowModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class ModelChecker
{
    public function __construct(
        protected Model $model
    ) {}

    abstract public function getCurrentModelWorkflows(): Collection;

    abstract public function getLastWorkflow(): Model|WorkflowModel|null;

    abstract public function hasCurrentLastWorkflow(): bool;

    abstract public function getCurrentWorkflow(): Model|WorkflowModel|null;

    abstract public function getNextWorkflow(): Model|WorkflowModel|null;

    abstract public function getSubmittedWorkflow(): Model|WorkflowModel|null;
}
