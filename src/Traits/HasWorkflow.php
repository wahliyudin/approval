<?php

namespace Tbu\Approval\Traits;

use BackedEnum;
use Tbu\Approval\Checkers\CheckerManager;
use Tbu\Approval\Checkers\Model\ModelChecker;
use Tbu\Approval\Contracts\ApprovalModelInterface;
use Tbu\Approval\Contracts\ApprovalRepositoryInterface;
use Tbu\Approval\Contracts\ApprovalServiceInterface;
use Tbu\Approval\Contracts\WorkflowModel;
use Tbu\Approval\Enums\LastAction;
use Tbu\Approval\Enums\Status;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tbu\Approval\Helper;
use Tbu\Approval\Services\ApprovalService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasWorkflow
{
    use HasApprovalEvents, HasApprovalScopes;

    public static function bootHasWorkflow(): void
    {
        // TODO: buat stub observer
        if (!is_subclass_of(static::class, ApprovalModelInterface::class)) {
            throw new \RuntimeException(
                static::class . " must implement " . ApprovalModelInterface::class
            );
        }
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Helper::modelWorkflow($this));
    }

    public function workflow(): HasOne
    {
        return $this->hasOne(Helper::modelWorkflow($this))->ofMany([
            'sequence' => 'min',
        ], function ($query) {
            $query->where('last_action', LastAction::NOTTING);
        });
    }

    protected function approvalModelChecker(): ModelChecker
    {
        $repository = resolve(ApprovalRepositoryInterface::class);
        return (new CheckerManager($this, $repository))->getChecker();
    }

    public function getWorkflows(): Collection
    {
        return $this->approvalModelChecker()->getCurrentModelWorkflows();
    }

    public function currentWorkflow(): Model|WorkflowModel|null
    {
        return $this->approvalModelChecker()->getCurrentWorkflow();
    }

    public function lastWorkflow(): Model|WorkflowModel|null
    {
        return $this->approvalModelChecker()->getLastWorkflow();
    }

    public function getSubmittedWorkflow(): Model|WorkflowModel|null
    {
        return $this->approvalModelChecker()->getSubmittedWorkflow();
    }

    public function nextWorkflow(): Model|WorkflowModel|null
    {
        return $this->approvalModelChecker()->getNextWorkflow();
    }

    public function hasLastWorkflow(): bool
    {
        return $this->approvalModelChecker()->hasCurrentLastWorkflow();
    }

    public function hasAllApproved(): bool
    {
        return $this->lastWorkflow()->last_action == LastAction::APPROVE;
    }

    public function hasCurrentWorkflow($nik): bool
    {
        return $this->currentWorkflow()?->nik == $nik;
    }

    public function approval(?BackedEnum $module = null): ApprovalServiceInterface
    {
        $repository = resolve(ApprovalRepositoryInterface::class);
        if ($module) {
            $this->module = $module;
        }
        return new ApprovalService($this, $repository);
    }

    public function createWorkflows($nik, $workflows = null)
    {
        return $this->approval()->create($nik, $workflows);
    }

    public function lastAction(LastAction $lastAction, $reason = null, $nik = null)
    {
        $workflow = $this->currentWorkflow();
        if (!$workflow) throw new \Exception('All approval have been done');
        $hasLast = $this->hasLastWorkflow();

        DB::beginTransaction();
        $workflow->setAttribute('last_action', $lastAction);
        $workflow->setAttribute('last_action_date', now());
        if ($nik) {
            $workflow->setAttribute('nik', $nik);
        }
        $workflow->save();

        if ($hasLast && $lastAction == LastAction::APPROVE) {
            $this->setAttribute('status', Status::CLOSE);
            $this->save();
            $this->fireApprovalEvent('workflow.last.and.approved', [$this]);
        }
        if (!$hasLast && $lastAction == LastAction::APPROVE) {
            $this->fireApprovalEvent('workflow.not.last.and.approved', [$this]);
        }
        if ($lastAction == LastAction::REJECT) {
            if (!in_array('reason', $this->getFillable())) {
                throw new \Exception('Reason is not fillable');
            }
            $this->setAttribute('status', Status::REJECT);
            $this->setAttribute('reason', $reason);
            $this->save();
            $this->fireApprovalEvent('workflow.rejected', [$this]);
        }
        DB::commit();
    }

    public function resetToFirst()
    {
        DB::transaction(function () {
            $this->workflows()->where('sequence', '!=', 1)->update([
                'last_action' => LastAction::NOTTING
            ]);
            $this->setAttribute('status', Status::OPEN);
            $this->setAttribute('reason', null);
            $this->save();
        });
    }

    public function approve(?int $nik = null)
    {
        $this->lastAction(LastAction::APPROVE, null, $nik);
    }

    public function reject($reason, ?int $nik = null)
    {
        $this->lastAction(LastAction::REJECT, $reason, $nik);
    }
}
