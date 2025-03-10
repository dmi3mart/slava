<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessImportFile;
use App\Services\Import\ExcelImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(protected ExcelImportService $importService)
    {

    }

    public function showImportForm()
    {
        return view('import.form');
    }

    public function import(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $filePath = $request->file('file')->store('imports');
        ProcessImportFile::dispatch(storage_path('app/private/' . $filePath));

        return redirect()->back()->with('success', 'Импорт начат. Прогресс можно отслеживать ниже.');
    }

    /**
     * Возвращает прогресс импорта в формате JSON.
     */
    public function getImportProgress(): JsonResponse
    {
        $progress = $this->importService->getImportProgress();
        return response()->json(['progress' => $progress]);
    }
}
