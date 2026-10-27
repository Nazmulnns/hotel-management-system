<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomImageController;
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

//CRUD Operations for rooms
Route::get('/rooms', [RoomController::class, 'getRooms']);
Route::get('/rooms/{id}', [RoomController::class, 'findRoomById']);
Route::post('/rooms', [RoomController::class, 'createRoom']);
Route::put('/rooms/{id}', [RoomController::class, 'updateRoom']);
Route::delete('/rooms/{id}', [RoomController::class, 'deleteRoom']);

//CRUD Operations for RoomImages
Route::post('/rooms/{id}/images', [RoomImageController::class, 'addNewRoomImages']);
Route::delete('/room-images/{id}', [RoomImageController::class, 'deleteRoomImageById']);

//CRUD Operation for Guests
Route::get('/guests/{id}', [GuestController::class, 'findGuestById']);
Route::get('/guests', [GuestController::class, 'getGuests']);
Route::get('/guests/{identity_number}', [GuestController::class, 'findGuestByIdentity']);
Route::post('/guests', [GuestController::class, 'createGuest']);
