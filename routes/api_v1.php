<?php

use App\Http\Controllers\Api\v1\StatementAPIController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('statement', [StatementAPIController::class,'store']);
Route::middleware('auth:sanctum')->group(function() {
    Route::get('statement/{statement:uuid}', [StatementAPIController::class,'show'])->name('api.v1.statement.show')->can('view statements');
    Route::get('statement/existing-puid/{puid}', [StatementAPIController::class,'existingPuid'])->name('api.v1.statement.existing-puid')->can('view statements');
});

