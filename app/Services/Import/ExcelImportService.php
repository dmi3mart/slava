<?php

namespace App\Services\Import;

use App\Dto\ImportData;
use App\Jobs\ProcessImportRow;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class ExcelImportService
{
    /**
     * @var array|array[]
     */
    protected array $rules = [
        'id' => ['sometimes', 'integer', 'min:1'],
        'name' => ['required', 'string', 'min:3', 'max:255'],
        'date' => ['required', 'date', 'after:now-25 years'],
    ];

    /**
     * @var array
     */
    protected array $errors = [];

    /**
     * @var string
     */
    protected string $redisKey;

    public function __construct()
    {
        $this->redisKey = 'import_progress:' . uniqid();
    }

    /**
     * @param $filePath
     * @return array
     * @throws \RedisException
     */
    public function import($filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            $data = $this->mapColumns($row);

            $validationResult = $this->validateData($data);

            if ($validationResult === true) {
                $rowData = new ImportData($data['name'], $data['email'], $data['status']);

                ProcessImportRow::dispatch($rowData);
            } else {
                $this->errors[] = [
                    'row' => $index + 1,
                    'messages' => $validationResult
                ];
            }

            $this->updateImportProgress($index + 1);
        }

        if (!empty($this->errors)) {
            $this->handleErrors($this->errors);
        }

        return $this->errors;
    }

    /**
     * @param $row
     * @return array|null[]
     */
    public function mapColumns($row): array
    {
        return [
            'id' => $row[0] ?? null,
            'name' => $row[1] ?? null,
            'date' => $row[2] ?? null,
        ];
    }

    /**
     * @param $data
     * @return array|true
     */
    public function validateData($data): true|array
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return true;
    }

    /**
     * @param  array  $errors
     * @return void
     */
    public function handleErrors(array $errors): void
    {
        $contents = '';

        foreach ($errors as $error) {
            $contents .= sprintf("Строка #%s: %s", $error['row'], join(', ', $error['messages']));
            $contents .= "\n";
        }

        $fileName = 'import_' . time() . '.txt';
        Storage::disk('local')->put($fileName, $contents);
    }

    /**
     * @param  int  $rowNumber
     * @param  array  $errors
     * @return void
     */
    public function handleRowError(int $rowNumber, array $errors)
    {
        $errorText = "Строка #$rowNumber: " . join(', ', $errors) . "\n";
        Storage::disk('local')->append('import_errors.txt', $errorText);
    }

    /**
     * @param  int  $number
     * @return void
     * @throws \RedisException
     */
    public function updateImportProgress(int $number)
    {
        Redis::set($this->redisKey, $number);
    }

    public function getImportProgress()
    {
        return Redis::get($this->redisKey);
    }
}
