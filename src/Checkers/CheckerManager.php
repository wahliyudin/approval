<?php

namespace Tbu\Approval\Checkers;

use Tbu\Approval\Checkers\Model\DefaultChecker;
use Tbu\Approval\Checkers\Model\ModelChecker;
use Tbu\Approval\Checkers\Model\QueryChecker;
use Illuminate\Database\Eloquent\Model;
use Tbu\Approval\Contracts\ApprovalRepositoryInterface;

class CheckerManager
{
    public function __construct(
        protected Model $model,
        protected ApprovalRepositoryInterface $approvalRepository
    ) {}

    public function getChecker(): ModelChecker
    {
        $this->model->loadMissing('workflows');
        $workflow = $this->model->workflows?->first();
        $employees = collect();
        if (!$this->model->relationLoaded('employee') || ($workflow && !$workflow->relationLoaded('employee'))) {
            $niks = $this->model->workflows->pluck('nik');
            $niks->push($this->model->getNik());
            $employees = $this->approvalRepository->getEmployees(
                $niks->unique()->filter()->toArray()
            );
        }
        if ($workflow && !$workflow->relationLoaded('employee')) {
            $this->model->workflows->each(function ($workflow) use ($employees) {
                $workflow->setRelation('employee', $employees[$workflow->nik] ?? null);
            });
        }
        if (!$this->model->relationLoaded('employee')) {
            $this->model->setRelation('employee', $employees[$this->model->getNik()] ?? null);
        }
        return new DefaultChecker($this->model);
    }
}
