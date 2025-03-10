<?php

namespace App\Jobs;

use App\Dto\ImportData;
use App\Services\Import\ExcelImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessImportFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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

        foreach ($rows as $index => $row) {
            $data = $importService->mapColumns($row);

            $validationResult = $importService->validateData($data);

            if ($validationResult === true) {
                $rowData = new ImportData(
                    id: $data['id'],
                    name: $data['name'],
                    date:$data['date']
                );

                ProcessImportRow::dispatch($rowData);
            } else {
                $importService->handleRowError($index + 1, $validationResult);
            }

            $importService->updateImportProgress($index + 1);
        }
    }
}
