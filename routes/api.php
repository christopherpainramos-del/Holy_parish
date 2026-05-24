<?php

use App\Models\Sacrament;
use Illuminate\Support\Facades\Route;

// Endpoint: http://192.168.100.43:8000/api/sacraments
Route::get('/sacraments', function () {
    // Returns only records 2000 and above for the Android app
    return Sacrament::where('year', '>=', 2000)
                    ->orderBy('created_at', 'desc')
                    ->get();
});

// Endpoint for QR Verification: http://192.168.100.43:8000/api/verify/{id}
Route::get('/verify/{id}', function ($id) {
    $record = Sacrament::find($id);
    return $record ? response()->json($record) : response()->json(['error' => 'Not Found'], 404);
});