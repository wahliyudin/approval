<?php

namespace Tbu\Approval\Checkers\Model;

use Tbu\Approval\Contracts\WorkflowModel;
use Tbu\Approval\Enums\LastAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tbu\Approval\Enums\Approval;

class QueryChecker extends ModelChecker
{
    public function getCurrentModelWorkflows(): Collection
    {
        return $this->model->workflows()->orderBy('sequence')->get();
    }

    public function getLastWorkflow(): Model|WorkflowModel|null
    {
        return $this->model->workflows()
            ->orderByDesc('sequence')
            ->first();
    }

    public function hasCurrentLastWorkflow(): bool
    {
        return $this->model->workflows()
            ->where('last_action', LastAction::NOTTING)
            ->orderBy('sequence', 'ASC')
            ->count() == 1;
    }

    public function getCurrentWorkflow(): Model|WorkflowModel|null
    {
        return $this->model->workflows()
            ->where('last_action', LastAction::NOTTING)
            ->orderBy('sequence', 'ASC')
            ->first();
    }

    public function getNextWorkflow(): Model|WorkflowModel|null
    {
        $current = $this->getCurrentWorkflow();
        if (!$current) return null;
        $next = $current->sequence + 1;
        return $this->model->workflows()
            ->where('sequence', $next)
            ->first();
    }

    public function getSubmittedWorkflow(): Model|WorkflowModel|null
    {
        return $this->model->workflows()
            ->where('approval', Approval::SUBMITTED)
            ->first();
    }
}
