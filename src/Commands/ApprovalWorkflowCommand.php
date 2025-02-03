<?php

namespace Tbu\Approval\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApprovalWorkflowCommand extends Command
{
    protected $signature = 'approval:workflow {--parent=}';
    protected $description = 'Generate approval workflow model and migration';

    private string $namespace = 'App\Models';

    public function handle(): int
    {
        if (!$this->validateInput()) {
            return 1;
        }

        $modelStructure = $this->parseModelInput();

        if (!$this->checkStubFiles($modelStructure)) {
            return 1;
        }

        if (!$this->generateModel($modelStructure)) {
            return 1;
        }

        $this->generateMigration($modelStructure);

        $this->info("Model {$modelStructure['modelName']} created successfully at {$modelStructure['modelPath']}");
        return 0;
    }

    private function validateInput(): bool
    {
        if (!$this->option('parent')) {
            $this->error('The --parent option is required.');
            return false;
        }
        return true;
    }

    private function parseModelInput(): array
    {
        $modelParts = explode('/', $this->option('parent'));
        $parentClass = array_pop($modelParts);
        $relativeNamespace = implode('\\', $modelParts);

        $namespace = $this->namespace;
        if ($relativeNamespace) {
            $namespace .= '\\' . $relativeNamespace;
        }

        $modelName = $parentClass . 'Workflow';
        $parentKey = Str::of($parentClass . 'Id')->snake()->value();
        $approvalTableName = Str::of($modelName)->snake()->plural()->value();
        $relativePath = Str::replaceFirst('App\\', '', $namespace);
        $modelPath = app_path(str_replace('\\', '/', $relativePath) . "/{$modelName}.php");
        $parentFunction = Str::of($parentClass)->lcfirst()->value();

        return compact(
            'namespace',
            'parentClass',
            'modelName',
            'parentKey',
            'approvalTableName',
            'modelPath',
            'parentFunction'
        );
    }

    private function checkStubFiles(array $modelStructure): bool
    {
        $stubPaths = [
            'model' => __DIR__ . '/../../stubs/workflow.stub',
            'migration' => __DIR__ . '/../../stubs/migration.stub'
        ];

        foreach ($stubPaths as $type => $path) {
            if (!File::exists($path)) {
                $this->error("Stub file not found at {$path}");
                return false;
            }
        }

        if (File::exists($modelStructure['modelPath'])) {
            $this->error("Model {$modelStructure['modelName']} already exists.");
            return false;
        }

        return true;
    }

    private function generateModel(array $modelStructure): bool
    {
        $stubPath = __DIR__ . '/../../stubs/workflow.stub';
        $stubContent = File::get($stubPath);

        $modelContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ parentNamespace }}',
                '{{ parentClass }}',
                '{{ parentKey }}',
                '{{ parentFunction }}',
            ],
            [
                $modelStructure['namespace'],
                $modelStructure['modelName'],
                $modelStructure['namespace'],
                $modelStructure['parentClass'],
                $modelStructure['parentKey'],
                $modelStructure['parentFunction']
            ],
            $stubContent
        );

        $directory = dirname($modelStructure['modelPath']);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return (bool) File::put($modelStructure['modelPath'], $modelContent);
    }

    private function generateMigration(array $modelStructure): void
    {
        $fileName = date('Y_m_d_His') . "_create_{$modelStructure['approvalTableName']}_table.php";
        $path = database_path("migrations/{$fileName}");
        $stubPath = __DIR__ . '/../../stubs/migration.stub';

        $stubContent = File::get($stubPath);
        $migrationContent = str_replace(
            ['{{ tableName }}', '{{ parentKey }}'],
            [$modelStructure['approvalTableName'], $modelStructure['parentKey']],
            $stubContent
        );

        file_put_contents($path, $migrationContent);
        $this->info("Migration created: {$fileName}");
    }
}
