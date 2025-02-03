<?php

namespace Tbu\Approval;

use Illuminate\Database\Eloquent\Model;

class Helper
{
    public static function modelWorkflow(Model $model)
    {
        $modelClass = get_class($model);
        $modelName = class_basename($model);
        $namespace = substr($modelClass, 0, strrpos($modelClass, '\\'));
        return $namespace . '\\' . $modelName . 'Workflow';
    }
}
