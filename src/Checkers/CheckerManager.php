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
        if (!$this->model->relationLoaded('workflows')) {
            $this->model->load('workflows');
        }
        if (!$this->model->relationLoaded('workflows.employee')) {
            $niks = $this->model->workflows->pluck('nik');
            $employees = $this->approvalRepository->getEmployees($niks->toArray());
            $this->model->workflows->each(function ($workflow) use ($employees) {
                $workflow->setRelation('employee', $employees[$workflow->nik] ?? null);
            });
        }
        return new DefaultChecker($this->model);
    }
}
