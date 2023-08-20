<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ImmoAgenceController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\EventParticipantController;
use App\Http\Controllers\ChallengeParticipantController;
use App\Http\Controllers\EventTicketController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelTicketController;
use App\Http\Controllers\HotelImageController;
use App\Http\Controllers\HotelSelfController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CateringServiceController;
use App\Http\Controllers\TravelAgencyController;
use App\Http\Controllers\RidesSharingController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuPriceController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\SMSAuthController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\TransportMeanController;
use App\Http\Controllers\CateringServiceClientController;
use App\Http\Controllers\AffordableCateringServiceController;

use App\Http\Controllers\AperitifController;
use App\Http\Controllers\AppetizerController;
use App\Http\Controllers\MainDishController;
use App\Http\Controllers\DessertController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\CustomerDemandController;
use App\Http\Controllers\BookTicketController;



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


//Auth routing
// single authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//google authentification

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

//sms authentication

Route::post('/send-verification-code', [SMSAuthController::class, 'sendVerificationCode']);
Route::post('/verify-code', [SMSAuthController::class, 'verifyCode']);



//token routing
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);


Route::group(['middleware' => 'auth:api'], function () {
    // Your protected API routes go here
 
});
//apartments controller
Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
Route::post('/apartments', [ApartmentController::class, 'store']);
Route::put('/apartments/{id}', [ApartmentController::class, 'update']);
Route::delete('/apartments/{id}', [ApartmentController::class, 'destroy']);

//immo agence routing
Route::get('/immo_agences', [ImmoAgenceController::class, 'index']);
Route::post('/immo_agences', [ImmoAgenceController::class, 'store']);
Route::get('/immo_agences/{id}', [ImmoAgenceController::class, 'show']);
Route::put('/immo_agences/{id}', [ImmoAgenceController::class, 'update']);
Route::delete('/immo_agences/{id}', [ImmoAgenceController::class, 'destroy']);

//house routing
Route::get('/houses', [HouseController::class, 'index']);
Route::post('/houses', [HouseController::class, 'store']);
Route::get('/houses/{id}', [HouseController::class, 'show']);
Route::put('/houses/{id}', [HouseController::class, 'update']);
Route::patch('/houses/{id}', [HouseController::class, 'update']);
Route::delete('/houses/{id}', [HouseController::class, 'destroy']);

//second part

//events routing
Route::get('/events', [EventController::class, 'index']);
Route::post('/events', [EventController::class, 'store']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::put('/events/{id}', [EventController::class, 'update']);
Route::delete('/events/{id}', [EventController::class, 'destroy']);

// Book event ticket

Route::get('/book_tickets', [BookTicketController::class, 'index']);
    Route::post('/book_tickets', [BookTicketController::class, 'store']);
    Route::get('/book_tickets/{id}', [BookTicketController::class, 'show']);
    Route::put('/book_tickets/{id}', [BookTicketController::class, 'update']);
    Route::delete('/book_tickets/{id}', [BookTicketController::class, 'destroy']);

//users routing

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

//challenges routing

Route::get('/challenges', [ChallengeController::class, 'index']);
Route::post('/challenges', [ChallengeController::class, 'store']);
Route::get('/challenges/{id}', [ChallengeController::class, 'show']);
Route::put('/challenges/{id}', [ChallengeController::class, 'update']);
Route::delete('/challenges/{id}', [ChallengeController::class, 'destroy']);

//events participants routing

Route::get('/events_participants', [EventParticipantController::class, 'index']);
Route::post('/events_participants', [EventParticipantController::class, 'store']);
Route::get('/events_participants/{id}', [EventParticipantController::class, 'show']);
Route::put('/events_participants/{id}', [EventParticipantController::class, 'update']);
Route::delete('/events_participants/{id}', [EventParticipantController::class, 'destroy']);

//challenges participants routing

Route::get('challenges_participants', [ChallengeParticipantController::class, 'index']);
Route::post('challenges_participants', [ChallengeParticipantController::class, 'store']);
Route::get('challenges_participants/{id}', [ChallengeParticipantController::class, 'show']);
Route::put('challenges_participants/{id}', [ChallengeParticipantController::class, 'update']);
Route::delete('challenges_participants/{id}', [ChallengeParticipantController::class, 'destroy']);

//events tickets routing

Route::get('/event_tickets', [EventTicketController::class, 'index']);
Route::post('/event_tickets', [EventTicketController::class, 'store']);
Route::get('/event_tickets/{id}', [EventTicketController::class, 'show']);
Route::put('/event_tickets/{id}', [EventTicketController::class, 'update']);
Route::delete('/event_tickets/{id}', [EventTicketController::class, 'destroy']);

//hotel routing

Route::get('/hotels', [HotelController::class, 'index']);
Route::post('/hotels', [HotelController::class, 'store']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);
Route::put('/hotels/{id}', [HotelController::class, 'update']);
Route::patch('/hotels/{id}', [HotelController::class, 'update']);
Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);

//hotel tickets routing

Route::get('/hotels_tickets', [HotelTicketController::class, 'index']);
Route::get('/hotels_tickets/{id}', [HotelTicketController::class, 'show']);
Route::post('/hotels_tickets', [HotelTicketController::class, 'store']);
Route::put('/hotels_tickets/{id}', [HotelTicketController::class, 'update']);
Route::delete('/hotels_tickets/{id}', [HotelTicketController::class, 'destroy']);


//hotel image routing
Route::get('hotel-images', [HotelImageController::class, 'index']);
Route::post('hotel-images', [HotelImageController::class, 'store']);
Route::get('hotel-images/{id}', [HotelImageController::class, 'show']);
Route::put('hotel-images/{id}', [HotelImageController::class, 'update']);
Route::delete('hotel-images/{id}', [HotelImageController::class, 'destroy']);


//hotel self routing

Route::group(['prefix' => 'hotels-self'], function () {
    Route::get('/', [HotelSelfController::class, 'index']);
    Route::post('/', [HotelSelfController::class, 'store']);
    Route::get('/{id}', [HotelSelfController::class, 'show']);
    Route::put('/{id}', [HotelSelfController::class, 'update']);
    Route::delete('/{id}', [HotelSelfController::class, 'destroy']);
});





// catering services routing

Route::get('/catering_services', [CateringServiceController::class, 'index']);
Route::post('/catering_services', [CateringServiceController::class, 'store']);
Route::get('/catering_services/{id}', [CateringServiceController::class, 'show']);
Route::put('/catering_services/{id}', [CateringServiceController::class, 'update']);
Route::delete('/catering_services/{id}', [CateringServiceController::class, 'destroy']);

//additional data in catering services controller

Route::apiResource('aperitifs', AperitifController::class);
Route::apiResource('appetizers', AppetizerController::class);
Route::apiResource('main_dishes', MainDishController::class);
Route::apiResource('desserts', DessertController::class);


// Route::get('catering_services', [CateringServiceController::class, 'index'])->name('catering_services.index');
// Route::get('catering_services/create', [CateringServiceController::class, 'create'])->name('catering_services.create');
// Route::post('catering_services', [CateringServiceController::class, 'store'])->name('catering_services.store');
// Route::get('catering_services/{cateringService}', [CateringServiceController::class, 'show'])->name('catering_services.show');
// Route::get('catering_services/{cateringService}/edit', [CateringServiceController::class, 'edit'])->name('catering_services.edit');
// Route::put('catering_services/{cateringService}', [CateringServiceController::class, 'update'])->name('catering_services.update');
// Route::delete('catering_services/{cateringService}', [CateringServiceController::class, 'destroy'])->name('catering_services.destroy');


//travel agences routing

Route::get('/travel-agencies', [TravelAgencyController::class, 'index']);
Route::post('/travel-agencies', [TravelAgencyController::class, 'store']);
Route::get('/travel-agencies/{id}', [TravelAgencyController::class, 'show']);
Route::put('/travel-agencies/{id}', [TravelAgencyController::class, 'update']);
Route::delete('/travel-agencies/{id}', [TravelAgencyController::class, 'destroy']);

//ride sharing routing

Route::get('/rides_sharing', [RidesSharingController::class, 'index']);
Route::post('/rides_sharing', [RidesSharingController::class, 'store']);
Route::get('/rides_sharing/{id}', [RidesSharingController::class, 'show']);
Route::put('/rides_sharing/{id}', [RidesSharingController::class, 'update']);
Route::delete('/rides_sharing/{id}', [RidesSharingController::class, 'destroy']);

//restaurant routing

Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::post('/restaurants', [RestaurantController::class, 'store']);
Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
Route::put('/restaurants/{id}', [RestaurantController::class, 'update']);
Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy']);

//courier routing 
Route::get('couriers', [CourierController::class, 'index']);
Route::post('couriers', [CourierController::class, 'store']);
Route::get('couriers/{id}', [CourierController::class, 'show']);
Route::put('couriers/{id}', [CourierController::class, 'update']);
Route::delete('couriers/{id}', [CourierController::class, 'destroy']);


//menu prices routing

Route::get('/menu_prices', [MenuPriceController::class, 'index']);
Route::post('/menu_prices', [MenuPriceController::class, 'store']);
Route::get('/menu_prices/{id}', [MenuPriceController::class, 'show']);
Route::put('/menu_prices/{id}', [MenuPriceController::class, 'update']);
Route::delete('/menu_prices/{id}', [MenuPriceController::class, 'destroy']);

//Menu routing
Route::get('/menu', [MenuController::class, 'index']);
Route::post('/menu', [MenuController::class, 'store']);
Route::get('/menu/{id}', [MenuController::class, 'show']);
Route::put('/menu/{id}', [MenuController::class, 'update']);
Route::delete('/menu/{id}', [MenuController::class, 'destroy']);

//drink routing
Route::get('/drink', [DrinkController::class, 'index']);
Route::post('/drink', [DrinkController::class, 'store']);
Route::get('/drink/{id}', [DrinkController::class, 'show']);
Route::put('/drink/{id}', [DrinkController::class, 'update']);
Route::delete('/drink/{id}', [DrinkController::class, 'destroy']);

//customer routing
Route::prefix('customer-demands')->group(function () {
    Route::get('/', [CustomerDemandController::class, 'index']);
    Route::post('/', [CustomerDemandController::class, 'store']);
    Route::get('/{id}', [CustomerDemandController::class, 'show']);
    Route::put('/{id}', [CustomerDemandController::class, 'update']);
    Route::delete('/{id}', [CustomerDemandController::class, 'destroy']);
});


//vehicule routing
Route::get('/vehicule', [VehiculeController::class, 'index']);
Route::post('/vehicule', [VehiculeController::class, 'store']);
Route::get('/vehicule/{id}', [VehiculeController::class, 'show']);
Route::put('/vehicule/{id}', [VehiculeController::class, 'update']);
Route::delete('/vehicule/{id}', [VehiculeController::class, 'destroy']);

//transport means routing

Route::resource('transport-means', TransportMeanController::class);



// Routes for CateringServiceClientController

Route::post('/catering-service-clients/demand', [CateringServiceClientController::class, 'demand']);
Route::apiResource('catering-service-clients', CateringServiceClientController::class);

//answer routing


Route::resource('answers', AnswerController::class);
Route::put('answers/{answer}/update-sum', [AnswerController::class, 'updateSum']);



// Routes for AffordableCateringServiceController
Route::get('/affordable-catering-services/{id}', [AffordableCateringServiceController::class, 'show']);




