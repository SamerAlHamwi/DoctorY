<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\reservationController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WeeklyScheduleController;
use App\Http\Controllers\Api\DoctorAvailabilityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorAuthController;
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
Route::prefix('/my-doctor')->middleware(['blockSql','throttle:60,1'])->group(function () {

    ///user api not auth
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::post('/register', [UserAuthController::class, 'register']);

        /// user log out
        Route::middleware('jwt.auth')->group(function () {
            Route::post('/logout', [UserAuthController::class, 'logout']);
        });
    });



    ////user auth api
    Route::middleware('jwt.auth')->group(function () {
        Route::prefix('/user/reservations')->group(function () {

            Route::get('/', [ReservationController::class, 'userReservations']);
            //Route::get('/{doctor}/available-reservations', [ReservationController::class, 'availableForDoctor']);
            Route::get('/{doctor}/availability', [DoctorAvailabilityController::class, 'index']);
            //Route::post('/{id}/assign', [ReservationController::class, 'assignToUser']);
            Route::post('/book', [ReservationController::class, 'bookSlot']);
            Route::post('/{id}/cancel', [ReservationController::class, 'cancel']);

        });
        Route::prefix('/user/article')->group(function () {

            Route::get('/', [ArticleController::class, 'index']);

        });
        Route::prefix('/user')->group(function () {

            Route::get('/get-doctors', [DoctorController::class, 'index']);
            Route::get('/doctors/search', [DoctorController::class, 'search']);

            Route::post('/review-doctor/{id}', [DoctorController::class, 'addReview']);
            Route::post('/unreview-doctor/{id}', [DoctorController::class, 'unReview']);
            Route::put('/update-name', [UserController::class, 'updateName']);
            Route::put('/update-phone', [UserController::class, 'updatePhone']);
            Route::put('/update-password', [UserController::class, 'updatePassword']);
            Route::put('/update-user', [UserController::class, 'updateUser']);

        });
    });



    ///doctor api not auth
    Route::prefix('/doctor-auth')->group(function () {
        Route::post('/login', [DoctorAuthController::class, 'login']);
        Route::post('/register', [DoctorAuthController::class, 'register']);

        ///doctor log out
        Route::middleware(['auth:doctor', 'doctor.token'])->group(function () {
            Route::post('/logout', [DoctorAuthController::class, 'logout']);
        });
    });


    ////doctor auth api
    Route::middleware(['auth:doctor', 'doctor.token'])->group(function () {
        Route::prefix('/doctor/reservations')->group(function () {
            Route::get('/', [ReservationController::class, 'doctorReservations']);
            //Route::delete('/{id}', [ReservationController::class, 'deleteReservation']);
            //Route::post('/bulk-create', [ReservationController::class, 'bulkCreateReservations']);
        });

        Route::prefix('doctor/schedules')->group(function () {
            Route::get('/', [WeeklyScheduleController::class, 'index']);
            Route::post('/', [WeeklyScheduleController::class, 'store']);
            Route::put('/{id}', [WeeklyScheduleController::class, 'update']);
            Route::delete('/{id}', [WeeklyScheduleController::class, 'destroy']);
        });

        Route::prefix('/doctor')->group(function () {
            Route::put('/update-name', [DoctorController::class, 'updateName']);
            Route::put('/update-phone', [DoctorController::class, 'updatePhone']);
            Route::put('/update-password', [DoctorController::class, 'updatePassword']);
            Route::put('/update-specialty', [DoctorController::class, 'updateSpecialty']);
            Route::put('/update-doctor', [DoctorController::class, 'updateDoctor']);
            Route::post('/upload-image', [DoctorController::class, 'uploadImage']);
        });
    });
});
