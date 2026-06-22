<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FixBladePhpStrings extends Command
{
    protected $signature   = 'blade:fix-php-strings {--dry-run : Preview changes without writing files}';
    protected $description = 'Fix multiline string values inside @php blocks in all blade files';

    public function handle(): int
    {
        $viewsPath = resource_path('views');
        $files     = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
        $fixed     = 0;
        $dryRun    = $this->option('dry-run');

        foreach ($files as $file) {
            if ($file->isDir() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $original = file_get_contents($file->getPathname());
            $result   = $this->fixContent($original);

            if ($result === $original) {
                continue;
            }

            $relativePath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());

            if ($dryRun) {
                $this->warn("[DRY-RUN] Would fix: {$relativePath}");
            } else {
                file_put_contents($file->getPathname(), $result);
                $this->info("Fixed: {$relativePath}");
            }

            $fixed++;
        }

        $label = $dryRun ? 'files would be fixed' : 'files fixed';
        $this->info("{$fixed} {$label}.");

        return self::SUCCESS;
    }

    private function fixContent(string $content): string
    {
        // Split into @php ... @endphp blocks and process only those
        return preg_replace_callback(
            '/@php(.*?)@endphp/s',
            function (array $matches): string {
                $block = $matches[1];

                // Join lines where a string value was wrapped after =>
                // Pattern: '=>   'some text  (line ending without closing quote)
                //          continuation text'   (next line finishing the string)
                $block = preg_replace_callback(
                    "/(['\"])([^'\"\\n]*?)\\s*\\n\\s*([^'\"\\n]*?)\\1/",
                    fn (array $m): string => $m[1] . $m[2] . ' ' . $m[3] . $m[1],
                    $block
                );

                return '@php' . $block . '@endphp';
            },
            $content
        );
    }
}
