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

    public function generate(string $name, array $children = [], bool $force = false, bool $onlyChild = false, bool $viewOnly = false)
    {

        $config = config("uitemplate.$name");

        if (!$config) {
             throw new Exception("✗ Component [$name] not found.");
             return 1; 
        }

        if ($config['type'] === "file") {
            return $this->generateFile($config, $force, $viewOnly);
        }

        if ($config['type'] === "folder") {
            return $this->generateFolder($config, $name, $children, $force, $onlyChild, $viewOnly);
        }

        return [];
    }

    protected function generateFile(array $config, bool $force, bool $viewOnly): array
    {
        $created = [];
        
        $bladeName = $this->toBladeFileName($config['stub']);
        $bladeSource = $this->stubPath . '/' . $bladeName . '/'. $bladeName . '.blade.php';
        $bladeTarget = $this->targetPath . '/' . $bladeName . '.blade.php';

        if (!file_exists($bladeTarget) || $force) {
            $this->copyFile($bladeSource, $bladeTarget, $force);
            $created[] = $bladeTarget;
        }
 

        // class hanya jika tidak --view
        if(!$viewOnly){
            $className = $this->toClassName($config['stub']);
            $classSource = $this->stubPath . '/' . $bladeName . '/' . $className . '.php';
            $classTarget = app_path('View/Components/Ui/' . $className . '.php');

            if(!file_exists($classTarget) || $force){
                $this->copyFile($classSource, $classTarget, $force);
                $created[] = $classTarget;
            }
        }

        return $created;
    }

    protected function generateFolder(
        array $config,
        string $name,
        array $children = [],
        bool $force = false,
        bool $onlyChild = false,
        bool $viewOnly = false
    ): array {
        $created = [];

        $bladeName = $this->toBladeFileName($name);
        $sourceDir = $this->stubPath . '/' . $config['stub'];
        $targetDir = $this->targetPath;

        // Normalize children (support comma)
        $children = $this->normalizeChildren($children);

        $availableChildren = $config['children'] ?? [];

        // CLASS PARENT - hanya jika tidak --view
            if (!$viewOnly && !$onlyChild) {
                $className   = $this->toClassName($name);
                $classSource = "$sourceDir/{$className}.php";
                $classTarget = app_path("View/Components/Ui/{$className}.php");

                // jika file belum selesai dan force = true
                if (!File::exists($classTarget) || $force) {
                    $this->copyFile($classSource, $classTarget, $force);
                    $created[] = $classTarget;
                }
            }

        // Handle child logic
        if (empty($children)) {
            $children = $config['default_children'] ?? [];
        } elseif (in_array('all', $children)) {
            $children = $availableChildren;
        } else {
            foreach ($children as $child) {
                if (!in_array($child, $availableChildren)) {
                    throw new Exception("✗ Invalid child [$child] for [$name].");
                }
            }
        }

        // === HANDLE PARENT ===
        if (!$onlyChild) {

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $indexTarget = "$targetDir/{$bladeName}.blade.php";

            if (!File::exists($indexTarget) || $force) {
                $this->copyFile(
                    "$sourceDir/$bladeName.blade.php",
                    $indexTarget,
                    $force
                );

                $created[] = $indexTarget;
            }

            
        }

        // === HANDLE CHILD ===
        foreach ($children as $child) {

            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $targetFile = "$targetDir/children/{$child}.blade.php";

            // skip kalau sudah ada
            if (File::exists($targetFile) && !$force) {
                continue;
            }

            $this->copyFile(
                "$sourceDir/children/{$child}.blade.php",
                $targetFile,
                $force
            );

            $created[] = $targetFile;
        }

        return $created;
    }

    protected function copyFile(string $source, string $target, bool $force)
    {
        if (!File::exists($source)) {
            throw new Exception("File not found: $source");
        }

        if (File::exists($target)) {
            return;
        }

        File::ensureDirectoryExists(dirname($target));

        File::copy($source, $target);
    }

    // cardHeader → CardHeader (untuk nama class)
protected function toClassName(string $name): string
{
    $kebab = $this->toBladeFileName($name); // cardHeader → card-header
    return str_replace('-', '', ucwords($kebab, '-')); // card-header → CardHeader
}

protected function toBladeFileName(string $name): string
{
     // Jika ALL CAPS → langsung lowercase
    if (strtoupper($name) === $name) {
        return strtolower($name);
    }

    // Handles: camelCase, PascalCase, slash separator, double dash
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
                            preg_replace('/([A-Z])/', '/$1', $name)
                        )
                    )
                )
            ),
            '-'
        )
    );
}

protected function normalizeChildren(array $children): array
{
    return collect($children)
        ->flatMap(fn($item) => explode(',', $item)) // support comma: "card-header,content"
        ->map(fn($item) => trim($item))
        ->filter()
        ->map(fn($item) => $this->toBladeFileName($item)) // ✅ pakai toBladeFileName
        ->unique()
        ->values()
        ->toArray();
}


//     protected function normalizeChildren(array $children): array
//     {
//         $changeText = function ($text) {
//             return strtolower(
//                 trim(
//                     preg_replace(
//                         '/-+/',
//                         '-',
//                         implode(
//                             '-',
//                             explode(
//                                 '/',
//                                 trim(
//                                     preg_replace('/([A-Z])/', '/$1', $text)
//                                 )
//                             )
//                         )
//                     ),
//                     '-'
//                 )
//             );
//         };

//         return collect($children)
//             ->flatMap(fn($item) => explode(',', $item)) // support comma
//             ->map(fn($item) => trim($item))
//             ->filter()
//             ->map($changeText)
//             ->unique()
//             ->values()
//             ->toArray();
//     }
}