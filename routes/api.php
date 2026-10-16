<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/users/{id}', [UserController::class, 'getUserById']);

//CRUD Operations for facilities
Route::get('/facilities', [FacilityController::class, 'getAllFacilities']);
Route::post('/facilities', [FacilityController::class, 'addNewFacility']);
Route::put('/facilities/{id}', [FacilityController::class, 'updateFacility']);
Route::delete('/facilities/{facilityId}', [FacilityController::class, 'deleteFacilityById']);

//CRUD Operations for room types
Route::get('/room-types', [RoomTypeController::class, 'getAllRoomTypes']);
Route::post('/room-types', [RoomTypeController::class, 'addNewRoomType']);
Route::put('/room-types/{id}', [RoomTypeController::class, 'updateRoomType']);
Route::delete('/room-types/{id}', [RoomTypeController::class, 'deleteRoomTypeById']);

Route::get('/rooms', [RoomController::class, 'getRooms']);
