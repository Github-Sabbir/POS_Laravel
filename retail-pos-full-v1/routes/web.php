<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;

Route::get('/', fn()=>redirect()->route('dashboard'));
Route::get('/login',[AuthController::class,'show'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('login.store');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function(){
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/pos',[PosController::class,'index'])->name('pos');
    Route::post('/pos/checkout',[PosController::class,'checkout'])->name('pos.checkout');
    Route::get('/pos/product/{query}',[PosController::class,'lookup'])->name('pos.lookup');

    Route::resource('products',ProductController::class);
    Route::post('/products/{product}/image',[ProductController::class,'image'])->name('products.image');
    Route::delete('/products/{product}/image',[ProductController::class,'removeImage'])->name('products.image.remove');

    Route::get('/barcodes',[BarcodeController::class,'index'])->name('barcodes.index');
    Route::post('/barcodes',[BarcodeController::class,'store'])->name('barcodes.store');
    Route::delete('/barcodes/{barcode}',[BarcodeController::class,'destroy'])->name('barcodes.destroy');

    Route::resource('purchases',PurchaseController::class)->only(['index','create','store','show']);
    Route::resource('customers',CustomerController::class);
    Route::post('/customers/{customer}/payment',[CustomerController::class,'payment'])->name('customers.payment');
    Route::resource('suppliers',SupplierController::class);
    Route::post('/suppliers/{supplier}/payment',[SupplierController::class,'payment'])->name('suppliers.payment');
    Route::resource('expenses',ExpenseController::class)->except(['show']);
    Route::get('/reports',[ReportController::class,'index'])->name('reports');
    Route::get('/returns',[ReturnController::class,'index'])->name('returns.index');
    Route::post('/returns',[ReturnController::class,'store'])->name('returns.store');

    Route::get('/settings',[SettingsController::class,'index'])->name('settings');
    Route::post('/settings',[SettingsController::class,'update'])->name('settings.update');

    Route::middleware('permission:users.view')->group(function(){
        Route::get('/users',[UserManagementController::class,'index'])->name('users.index');
        Route::get('/users/create',[UserManagementController::class,'create'])->name('users.create');
        Route::post('/users',[UserManagementController::class,'store'])->name('users.store');
        Route::get('/users/{user}/edit',[UserManagementController::class,'edit'])->name('users.edit');
        Route::put('/users/{user}',[UserManagementController::class,'update'])->name('users.update');
        Route::delete('/users/{user}',[UserManagementController::class,'destroy'])->name('users.destroy');
    });
    Route::middleware('permission:roles.manage')->group(function(){
        Route::get('/roles',[UserManagementController::class,'roles'])->name('roles.index');
        Route::post('/roles',[UserManagementController::class,'storeRole'])->name('roles.store');
        Route::put('/roles/{role}',[UserManagementController::class,'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}',[UserManagementController::class,'destroyRole'])->name('roles.destroy');
    });
});
