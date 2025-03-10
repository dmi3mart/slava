<?php

namespace App\Jobs;

use App\Dto\ImportData;
use App\Models\Record;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImportRow implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ImportData
     */
    protected ImportData $rowData;

    public function __construct(ImportData $rowData)
    {
        $this->rowData = $rowData;
    }

    public function handle()
    {
        Record::query()
              ->updateOrCreate(
                  [
                      'custom_id' => $this->rowData->id
                  ],
                  [
                      'custom_id' => $this->rowData->id,
                      'name' => $this->rowData->name,
                      'date' => $this->rowData->date,
                  ]
              );
    }
}
