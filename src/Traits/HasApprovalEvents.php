<?php

declare(strict_types=1);

namespace Tbu\Approval\Traits;

use Closure;
use Illuminate\Support\Str;

/**
 * @property \Illuminate\Events\Dispatcher $dispatcher
 */
trait HasApprovalEvents
{
    protected static array $approvalObservables = [
        'workflowLastAndApproved',
        'workflowNotLastAndApproved',
        'workflowRejected',
        'workflowCreated',
    ];

    public static function approvalObserve(object|string $class): void
    {
        $className = is_string($class) ? $class : get_class($class);

        foreach (self::$approvalObservables as $event) {
            if (method_exists($class, $event)) {
                static::registerApprovalEvent(Str::snake($event, '.'), $className . '@' . $event);
            }
        }
    }

    public static function approvalFlushObservables()
    {
        foreach (self::$approvalObservables as $event) {
            $event = Str::snake($event, '.');
            static::$dispatcher->forget("approval.{$event}: " . static::class);
        }
    }

    public function fireApprovalEvent(string $event, array $payload)
    {
        if (! isset(static::$dispatcher)) {
            return true;
        }

        return static::$dispatcher->dispatch(
            "approval.{$event}: " . static::class,
            $payload
        );
    }

    public static function registerApprovalEvent(
        string $event,
        Closure|string|array $callback
    ): void {
        if (isset(static::$dispatcher)) {
            $name = static::class;

            static::$dispatcher->listen("approval.{$event}: {$name}", $callback);
        }
    }

    public static function workflowLastAndApproved(Closure|string|array $callback): void
    {
        static::registerApprovalEvent('workflow.last.and.approved', $callback);
    }

    public static function workflowNotLastAndApproved(Closure|string|array $callback): void
    {
        static::registerApprovalEvent('workflow.not.last.and.approved', $callback);
    }

    public static function workflowRejected(Closure|string|array $callback): void
    {
        static::registerApprovalEvent('workflow.rejected', $callback);
    }

    public static function workflowCreated(Closure|string|array $callback): void
    {
        static::registerApprovalEvent('workflow.created', $callback);
    }
}
