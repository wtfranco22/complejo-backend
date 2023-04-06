<?php

use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\HourController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('shifts/{date}', [ShiftController::class, 'freeShifts']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::resource('hours', HourController::class);
    Route::resource('users', UserController::class)->except('create', 'edit', 'store');
    Route::resource('workdays', DayController::class)->only('index', 'update', 'destroy');
    Route::resource('courts', CourtController::class)->except('create', 'edit');
    Route::resource('roles', RoleController::class)->except('create', 'edit', 'destroy');
    Route::resource('payments', PaymentController::class)->only('index', 'store', 'show');
    Route::resource('accounts', AccountController::class)->only('index', 'show', 'update');
    Route::get('my-payments',[UserController::class,'myPayments']);
    Route::get('my-shifts',[UserController::class,'myShifts']);
    Route::post('cancel-shift',[ShiftController::class,'cancelShift']);
});;
