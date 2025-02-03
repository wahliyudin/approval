<?php

namespace Tbu\Approval\Contracts;

/**
 * @property ApprovalRepositoryInterface $approvalRepository
 */
interface ApprovalServiceInterface
{
    public function create($nik, $workflows = null): void;

    public function addAdditionalParam(array $param): self;

    public function setAdditionalParams(array $params): self;

    public function setConditionContinue(\Closure $conditionContinue): self;

    public function setConditionBreak(\Closure $conditionBreak): self;
}
