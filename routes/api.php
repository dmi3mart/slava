<?php

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/records', function (Request $request) {

    return \App\Http\Resources\RecordResource::collection(
        Record::query()->cursorPaginate()
    );

});

Route::get('/records-grouped', function (Request $request) {

    $groupedRecords = [];

    Record::query()->select('id', 'name', 'date')->chunk(100, function ($records) use (&$groupedRecords) {
        foreach ($records as $record) {
            $dateStr = $record->date->format('d.m.Y');
            if (!isset($groupedRecords[$dateStr])) {
                $groupedRecords[$dateStr] = [
                    'date' => $dateStr,
                    'items' => [],
                ];
            }
            $groupedRecords[$dateStr]['items'][] = [
                'id' => $record->id,
                'name' => $record->name,
            ];
        }
    });

    $groupedRecords = array_values($groupedRecords);

    return response()->json($groupedRecords);

});
