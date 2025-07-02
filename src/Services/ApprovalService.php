<?php

namespace Tbu\Approval\Services;

use Tbu\Approval\Contracts\ApprovalRepositoryInterface;
use Tbu\Approval\Contracts\ApprovalServiceInterface;
use Tbu\Approval\Contracts\ApprovalModelInterface;
use Tbu\Approval\Enums\LastAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tbu\Approval\Enums\Approval;

class ApprovalService implements ApprovalServiceInterface
{
    protected array $additionalParams = [];

    protected \Closure $conditionBreak;

    protected \Closure $conditionContinue;

    protected bool $forceClose = false;

    protected $currentWorkflow = null;

    public function __construct(
        public Model|ApprovalModelInterface $model,
        public ApprovalRepositoryInterface $approvalRepository
    ) {
        $this->conditionBreak = fn() => true;
        $this->conditionContinue = fn() => true;
    }

    public function create($nik, $workflows = null): void
    {
        if ($workflows) {
            $workflowDatas = $workflows;
        } else {
            $workflowDatas = $this->approvals($nik);
        }
        $workflowDatas = $this->checkDuplicate($workflowDatas);
        DB::beginTransaction();
        $this->model->workflows()->createMany($workflowDatas->toArray());
        $this->model->refresh();
        $this->model->fireApprovalEvent('workflow.created', [$this->model]);
        DB::commit();
    }

    public function approvals($nik)
    {
        $approvals = $this->approvalRepository->getByModule($this->model->module);

        $data = $this->prepareApprovals($approvals);

        $response = $this->patchDataWorkflows($data, $nik);

        return $this->payloadApprovals($response);
    }

    private function prepareApprovals(Collection $approvals): array
    {
        $data = [];
        foreach ($approvals as $key => $approval) {
            if (!($this->conditionBreak)($approval)) break;
            if (!($this->conditionContinue)($approval)) continue;
            array_push($data, $this->payloadApprovalForHRIS($approval, $key++));
        }
        return $data;
    }

    private function payloadApprovalForHRIS($approval, $sequence): array
    {
        $payload = [
            'approval' => $approval->approval->valueByHRIS(),
            'nik' => $approval->nik,
            'title' => $approval->title
        ];
        foreach ($this->additionalParams as $param) {
            if ((isset($param['sequence']) ? $param['sequence'] : null) == $sequence) {
                $payload = array_merge($payload, $param);
            }
        }
        return $payload;
    }

    private function patchDataWorkflows(array $data, $nik)
    {
        $payload = $this->preparePayload($data, $nik);
        $response = $this->approvalRepository->getWorkflows($payload);
        if (isset($response['exception'])) {
            throw ValidationException::withMessages([
                isset($response['message']) ? $response['message'] : 'Something went wrong!'
            ]);
        }
        return $response;
    }

    private function preparePayload(array $data, $nik)
    {
        $payload = [
            'submitted' => $nik,
            'approvals' => $data,
            'with_approval' => true
        ];
        return array_merge($payload, $this->additionalParams);
    }

    private function payloadApprovals($approvals)
    {
        return collect($approvals)
            ->except('employee', 'last_action_date')->map(function ($item) {
                return [
                    "sequence" => $item['sequence'],
                    "approval" => Approval::getFromService($item['approval']),
                    "nik" => $item['nik'],
                    "title" => $item['title'],
                    "last_action" => LastAction::getFromService($item['last_action']),
                ];
            });
    }

    private function checkDuplicate(Collection $approvals)
    {
        $submitted = $approvals->shift();
        return $approvals->unique('nik')->prepend($submitted);
    }

    public function addAdditionalParam(array $param): self
    {
        $this->additionalParams[] = $param;
        return $this;
    }

    public function setAdditionalParams(array $params): self
    {
        $this->additionalParams = $params;
        return $this;
    }

    public function setConditionBreak(\Closure $conditionBreak): self
    {
        $this->conditionBreak = $conditionBreak;
        return $this;
    }

    public function setConditionContinue(\Closure $conditionContinue): self
    {
        $this->conditionContinue = $conditionContinue;
        return $this;
    }
}
