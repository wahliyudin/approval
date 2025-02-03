<?php

namespace Tbu\Approval\Checkers\Model;

use Tbu\Approval\Contracts\WorkflowModel;
use Tbu\Approval\Enums\LastAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DefaultChecker extends ModelChecker
{
    public function getCurrentModelWorkflows(): Collection
    {
        return $this->model->getRelation('workflows');
    }

    public function getLastWorkflow(): Model|WorkflowModel|null
    {
        return $this->model->getRelation('workflows')
            ->sortByDesc('sequence')
            ->first();
    }

    public function hasCurrentLastWorkflow(): bool
    {
        return $this->model->getRelation('workflows')
            ->where('last_action', LastAction::NOTTING)
            ->sortBy('sequence')
            ->count() == 1;
    }

    public function getCurrentWorkflow(): Model|WorkflowModel|null
    {
        return $this->model->getRelation('workflows')
            ->where('last_action', LastAction::NOTTING)
            ->sortBy('sequence')
            ->first();
    }

    public function getNextWorkflow(): Model|WorkflowModel|null
    {
        $current = $this->getCurrentWorkflow();
        if (!$current) return null;
        $next = $current->sequence + 1;
        return $this->model->getRelation('workflows')
            ->where('sequence', $next)
            ->first();
    }
}
