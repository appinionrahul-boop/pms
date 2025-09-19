<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\TechnicalSpecController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);

	Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');


    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');

	// keep your existing user profile endpoints
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);

    // 🚫 REMOVE this conflicting /login inside auth (it shadowed the real login)
    // Route::get('/login', function () { return view('dashboard'); })->name('sign-up');

    // ✅ APP Management — Packages
    // List / Create / Store / Edit / Update / Delete
    Route::resource('packages', PackageController::class)->except(['show']);

    // Bulk Upload (Excel)
    Route::post('packages/bulk-upload', [PackageController::class, 'bulkUpload'])->name('packages.bulkUpload');

	//Requisition
	// Route::resource('requisitions', RequisitionController::class)->except(['show','index']);

	Route::resource('requisitions', RequisitionController::class);

	// Optional nested create (from a package)
	Route::get('packages/{package}/requisitions/create', [RequisitionController::class,'create'])
		->name('packages.requisitions.create');

	//Technical Spec
	Route::get('technical-specs', [TechnicalSpecController::class, 'index'])->name('techspecs.index');
	Route::get('technical-specs/create', [TechnicalSpecController::class, 'create'])->name('techspecs.create'); // optional: global create
	Route::post('technical-specs', [TechnicalSpecController::class, 'store'])->name('techspecs.store');

	Route::get('technical-specs/package/{package}', [TechnicalSpecController::class, 'show'])
		->name('techspecs.show'); // list all specs for this package

	Route::get('technical-specs/{spec}/edit', [TechnicalSpecController::class, 'edit'])->name('techspecs.edit');
	Route::put('technical-specs/{spec}', [TechnicalSpecController::class, 'update'])->name('techspecs.update');
	Route::delete('technical-specs/{spec}', [TechnicalSpecController::class, 'destroy'])->name('techspecs.destroy');

	// quick “Add new for a package”
	Route::get('technical-specs/package/{package}/create', [TechnicalSpecController::class, 'createForPackage'])
		->name('techspecs.createForPackage');

	//Report
	Route::get('/reports/procurements', [ReportController::class, 'index'])
    ->name('reports.procurements');

	Route::get('reports/procurements/export', [ReportController::class, 'export'])
    ->name('reports.procurements.export');


	
});

Route::group(['middleware' => 'guest'], function () {
    // (Optional) disable registration by commenting these two in single-user setup
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

// Keep one canonical login route name for redirects
Route::get('/login', function () {
    return view('session/login-session');
})->name('login');
