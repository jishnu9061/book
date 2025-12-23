<?php

namespace App\Console\Commands;

use App\Services\CodeIntegrityChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateIntegrityHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:generate-hashes {--save : Save hashes to a secure location}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate integrity hashes for license protection files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CodeIntegrityChecker $integrityChecker)
    {
        $this->info('Generating integrity hashes for license protection files...');
        $this->newLine();

        $hashes = $integrityChecker->generateFileHashes();

        if (empty($hashes)) {
            $this->error('No files found to generate hashes for.');
            return 1;
        }

        $this->table(
            ['File', 'Hash'],
            collect($hashes)->map(function ($hash, $file) {
                return [$file, substr($hash, 0, 20) . '...'];
            })->toArray()
        );

        if ($this->option('save')) {
            $this->saveHashes($hashes);
        }

        $this->newLine();
        $this->info('Integrity hashes generated successfully!');
        $this->warn('Store these hashes securely and use them for integrity validation.');

        return 0;
    }

    private function saveHashes($hashes)
    {
        $hashFile = storage_path('app/integrity_hashes.json');
        
        // Encrypt the hashes before saving
        $encryptedHashes = encrypt(json_encode($hashes));
        
        File::put($hashFile, $encryptedHashes);
        
        $this->info("Hashes saved to: {$hashFile}");
        $this->warn('Keep this file secure and backed up!');
    }
}