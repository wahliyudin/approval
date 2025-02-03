<?php

namespace Tbu\Approval;

use Tbu\Approval\Contracts\ApprovalRepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Tbu\Approval\Commands\ApprovalWorkflowCommand;

class ApprovalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $repository = Config::get('approval.repository');
        if (!$repository) {
            throw new \RuntimeException("Approval repository not set");
        } else if (!is_subclass_of($repository, ApprovalRepositoryInterface::class)) {
            throw new \RuntimeException(
                $repository . " must implement " . ApprovalRepositoryInterface::class
            );
        }
        $this->app->bind(ApprovalRepositoryInterface::class, $repository);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApprovalWorkflowCommand::class
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../../config/approval.php', 'approval');
    }
}
