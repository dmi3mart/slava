<?php

namespace App\Console\Commands;

use App\Services\Import\ExcelImportService;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:excel {file}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from an Excel file';

    /**
     * @param  ExcelImportService  $importService
     */
    public function __construct(protected ExcelImportService $importService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filePath = $this->argument('file');
        $errors = $this->importService->import($filePath);

        if (empty($errors)) {
            $this->info('Импорт успешно завершен.');
        } else {
            $this->error('Импорт завершен с ошибками.');
            $this->info('Ошибки сохранены в файл.');
        }
    }
}
