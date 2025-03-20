<?php

namespace App\Jobs;

use App\Dto\ImportData;
use App\Services\Import\ExcelImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessImportFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 0;

    protected string $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }


    public function handle(ExcelImportService $importService): void
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        array_shift($rows); // Убираем первую строку, т.к это шапка файла

        foreach ($rows as $index => $row) {
            $data = $importService->mapColumns($row);

            $validationResult = $importService->validateData($data);

            if ($validationResult === true) {
                Log::info("no errors");
                $rowData = new ImportData(
                    id: $data['id'],
                    name: $data['name'],
                    date:$data['date']
                );

                //ProcessImportRow::dispatch($rowData);
            } else {
                Log::info(json_encode($validationResult, JSON_PRETTY_PRINT));
                $importService->handleRowError($index + 1, $validationResult);
            }

            $importService->updateImportProgress($index + 1);
        }
    }
}
