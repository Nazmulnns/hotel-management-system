<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\RoomMaintenanceController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportRequestController;
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

//CRUD Operations for Guests
Route::get('/guests/{id}', [GuestController::class, 'findGuestById']);
Route::get('/guests', [GuestController::class, 'getGuests']);
Route::post('/guests', [GuestController::class, 'createGuest']);

//CRUD Operations for Services
Route::get('/services', [ServiceController::class, 'getServices']);
Route::get('/services/{id}', [ServiceController::class, 'findServiceById']);
Route::post('/services', [ServiceController::class, 'createNewService']);
Route::put('/services/{id}', [ServiceController::class, 'updateService']);
Route::delete('/services/{id}', [ServiceController::class, 'deleteService']);

//CRUD Operations for Discounts
Route::get('/discounts', [DiscountController::class, 'getDiscounts']);
Route::get('/discounts/{id}', [DiscountController::class, 'findDiscountById'])->whereNumber('id');
Route::post('/discounts', [DiscountController::class, 'addNewDiscount']);
Route::put('/discounts/{id}', [DiscountController::class, 'updateDiscount'])->whereNumber('id');
Route::delete('/discounts/{id}', [DiscountController::class, 'deleteDiscountById'])->whereNumber('id');

//CRUD Operations for Room Maintenances
Route::get('/room-maintenances', [RoomMaintenanceController::class, 'getMaintenances']);
Route::get('/room-maintenances/{id}', [RoomMaintenanceController::class, 'findMaintenanceById'])->whereNumber('id');
Route::put('/room-maintenances/{id}', [RoomMaintenanceController::class, 'updateMaintenance'])->whereNumber('id');
Route::get('/rooms/{id}/maintenances', [RoomMaintenanceController::class, 'getRoomMaintenances'])->whereNumber('id');
Route::post('/rooms/{id}/maintenances', [RoomMaintenanceController::class, 'reportMaintenance'])->whereNumber('id');

//CRUD Operations for Bookings
Route::get('/bookings', [BookingController::class, 'getBookings']);
Route::get('/bookings/{id}', [BookingController::class, 'findBookingById'])->whereNumber('id');
Route::post('/bookings', [BookingController::class, 'createBooking']);
Route::patch('/bookings/{id}/status', [BookingController::class, 'updateBookingStatus'])->whereNumber('id');

//CRUD Operations for Reviews
Route::get('/reviews', [ReviewController::class, 'getReviews']);
Route::post('/bookings/{id}/review', [ReviewController::class, 'addReview'])->whereNumber('id');

//CRUD Operations for Support Requests
Route::get('/support-requests', [SupportRequestController::class, 'getSupportRequests']);
Route::get('/support-requests/{id}', [SupportRequestController::class, 'findSupportRequestById'])->whereNumber('id');
Route::patch('/support-requests/{id}', [SupportRequestController::class, 'updateSupportRequest'])->whereNumber('id');
Route::post('/guests/{id}/support-requests', [SupportRequestController::class, 'createSupportRequest'])->whereNumber('id');

//CRUD Operations for Invoices
Route::get('/invoices', [InvoiceController::class, 'getInvoices']);
Route::get('/invoices/{id}', [InvoiceController::class, 'findInvoiceById'])->whereNumber('id');
Route::patch('/invoices/{id}/status', [InvoiceController::class, 'updateInvoiceStatus'])->whereNumber('id');
Route::post('/bookings/{id}/invoice', [InvoiceController::class, 'generateInvoice'])->whereNumber('id');
