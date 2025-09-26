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
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\NotificationController;
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

	Route::get('/change-password', [ChangePasswordController::class, 'index'])->name('change.password');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change.password.update');

	// View page with DataTable
    Route::get('/packages/all', [PackageController::class, 'all'])->name('packages.all');
    // Ajax feed for DataTables
   Route::get('/packages/download/excel', [PackageController::class, 'downloadExcel'])
     ->name('packages.download.excel');

	 //User Management
	 Route::get('/users/management', [UserManagementController::class, 'index'])
        ->name('users.management');

    // Create / Store
    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])
        ->name('users.store');

    // Edit / Update
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->name('users.update');

    // Password update (from modal)
    Route::post('/users/{user}/password', [UserManagementController::class, 'updatePassword'])
        ->name('users.password.update');

    //Notification
    Route::get('/notifications/list', [NotificationController::class, 'list'])
    ->name('notifications.list'); // returns last 100 (HTML partial)

    Route::post('/notifications/mark-all-seen', [NotificationController::class, 'markAllSeen'])
    ->name('notifications.markAllSeen'); // marks all unseen -> seen
	
});


Route::group(['middleware' => 'guest'], function () {
    // (Optional) disable registration by commenting these two in single-user setup
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);

});

// Keep one canonical login route name for redirects
Route::get('/login', function () {
    return view('session/login-session');
})->name('login');
