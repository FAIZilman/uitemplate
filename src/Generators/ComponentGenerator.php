<?php

namespace Uitemplate\Laravel\Generators;

use Illuminate\Support\Facades\File;
use Exception;

class ComponentGenerator
{
    protected string $stubPath;
    protected string $targetPath;

    
    public function __construct()
    {
        $this->stubPath = __DIR__ . '/../components/blade';
        $this->targetPath = resource_path('views/components/ui');
    }

    public function generate(string $name, array $children = [], bool $force = false, bool $onlyChild = false)
    {
        $config = config("uitemplate.$name");

        if (!$config) {
            throw new Exception("Component [$name] not found.");
        }

        if ($config['type'] === "file") {
            return $this->generateFile($config, $force);
        }

        if ($config['type'] === "folder") {
            return $this->generateFolder($config, $name, $children, $force, $onlyChild);
        }

        return [];
    }

    protected function generateFile(array $config, bool $force): array
    {
        $source = $this->stubPath . '/' . $config['stub'];
        $target = $this->targetPath . '/' . $config['stub'];

        if (File::exists($target) && !$force) {
            return [];
        }

        $this->copyFile($source, $target, true);

        return [$target];
    }

    protected function generateFolder(
        array $config,
        string $name,
        array $children,
        bool $force,
        bool $onlyChild = false
    ): array {
        $created = [];

        $sourceDir = $this->stubPath . '/' . $config['stub'];
        $targetDir = $this->targetPath . '/' . $name;

        // Normalize children (support comma)
        $children = $this->normalizeChildren($children);

        $availableChildren = $config['children'] ?? [];

        // Handle child logic
        if (empty($children)) {
            $children = $config['default_children'] ?? [];
        } elseif (in_array('all', $children)) {
            $children = $availableChildren;
        } else {
            foreach ($children as $child) {
                if (!in_array($child, $availableChildren)) {
                    throw new Exception("Invalid child [$child] for [$name]");
                }
            }
        }

        // === HANDLE PARENT ===
        if (!$onlyChild) {

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $indexTarget = "$targetDir/{$name}.blade.php";

            if (!File::exists($indexTarget) || $force) {
                $this->copyFile(
                    "$sourceDir/$name.blade.php",
                    $indexTarget,
                    true
                );

                $created[] = $indexTarget;
            }
        }

        // === HANDLE CHILD ===
        foreach ($children as $child) {

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $targetFile = "$targetDir/{$child}.blade.php";

            // skip kalau sudah ada
            if (File::exists($targetFile) && !$force) {
                continue;
            }

            $this->copyFile(
                "$sourceDir/children/{$child}.blade.php",
                $targetFile,
                true
            );

            $created[] = $targetFile;
        }

        return $created;
    }

    protected function copyFile(string $source, string $target, bool $force)
    {
        if (!File::exists($source)) {
            throw new Exception("Stub not found: $source");
        }

        if (File::exists($target) && !$force) {
            return;
        }

        File::ensureDirectoryExists(dirname($target));

        File::copy($source, $target);
    }

    protected function normalizeChildren(array $children): array
    {
        $changeText = function ($text) {
            return strtolower(
                trim(
                    preg_replace(
                        '/-+/',
                        '-',
                        implode(
                            '-',
                            explode(
                                '/',
                                trim(
                                    preg_replace('/([A-Z])/', '/$1', $text)
                                )
                            )
                        )
                    ),
                    '-'
                )
            );
        };

        return collect($children)
            ->flatMap(fn($item) => explode(',', $item)) // support comma
            ->map(fn($item) => trim($item))
            ->filter()
            ->map($changeText)
            ->unique()
            ->values()
            ->toArray();
    }
}