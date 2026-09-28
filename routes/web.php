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

    Route::middleware('permission:products.view')->group(function(){ Route::resource('products',ProductController::class)->only(['index']); Route::get('/products/create',[ProductController::class,'create'])->name('products.create'); Route::get('/products/{product}/edit',[ProductController::class,'edit'])->name('products.edit'); });
    Route::middleware('permission:products.manage')->group(function(){ Route::post('/products',[ProductController::class,'store'])->name('products.store'); Route::put('/products/{product}',[ProductController::class,'update'])->name('products.update'); });
    Route::middleware('permission:products.archive')->delete('/products/{product}',[ProductController::class,'destroy'])->name('products.destroy');
    Route::middleware('permission:products.restore')->post('/products/{id}/restore',[ProductController::class,'restore'])->name('products.restore');
    Route::post('/products/{product}/image',[ProductController::class,'image'])->name('products.image');
    Route::delete('/products/{product}/image',[ProductController::class,'removeImage'])->name('products.image.remove');

    Route::get('/barcodes',[BarcodeController::class,'index'])->name('barcodes.index');
    Route::post('/barcodes',[BarcodeController::class,'store'])->name('barcodes.store');
    Route::delete('/barcodes/{barcode}',[BarcodeController::class,'destroy'])->name('barcodes.destroy');

    Route::resource('purchases',PurchaseController::class)->only(['index','create','store','show']);
    Route::middleware('permission:customers.view')->group(function(){ Route::get('/customers',[CustomerController::class,'index'])->name('customers.index'); Route::get('/customers/{customer}/edit',[CustomerController::class,'edit'])->name('customers.edit'); }); Route::middleware('permission:customers.create')->post('/customers',[CustomerController::class,'store'])->name('customers.store'); Route::middleware('permission:customers.create')->get('/customers/create',[CustomerController::class,'create'])->name('customers.create'); Route::middleware('permission:customers.edit')->put('/customers/{customer}',[CustomerController::class,'update'])->name('customers.update'); Route::middleware('permission:customers.delete')->delete('/customers/{customer}',[CustomerController::class,'destroy'])->name('customers.destroy');
    Route::middleware('permission:customers.payment')->post('/customers/{customer}/payment',[CustomerController::class,'payment'])->name('customers.payment');
    Route::middleware('permission:suppliers.view')->group(function(){ Route::get('/suppliers',[SupplierController::class,'index'])->name('suppliers.index'); Route::get('/suppliers/{supplier}/edit',[SupplierController::class,'edit'])->name('suppliers.edit'); }); Route::middleware('permission:suppliers.create')->get('/suppliers/create',[SupplierController::class,'create'])->name('suppliers.create'); Route::middleware('permission:suppliers.create')->post('/suppliers',[SupplierController::class,'store'])->name('suppliers.store'); Route::middleware('permission:suppliers.edit')->put('/suppliers/{supplier}',[SupplierController::class,'update'])->name('suppliers.update'); Route::middleware('permission:suppliers.delete')->delete('/suppliers/{supplier}',[SupplierController::class,'destroy'])->name('suppliers.destroy');
    Route::middleware('permission:suppliers.payment')->post('/suppliers/{supplier}/payment',[SupplierController::class,'payment'])->name('suppliers.payment');
    Route::middleware('permission:expenses.view')->group(function(){ Route::get('/expenses',[ExpenseController::class,'index'])->name('expenses.index'); Route::get('/expenses/{expense}/edit',[ExpenseController::class,'edit'])->name('expenses.edit'); }); Route::middleware('permission:expenses.create')->group(function(){ Route::get('/expenses/create',[ExpenseController::class,'create'])->name('expenses.create'); Route::post('/expenses',[ExpenseController::class,'store'])->name('expenses.store'); }); Route::middleware('permission:expenses.edit')->put('/expenses/{expense}',[ExpenseController::class,'update'])->name('expenses.update'); Route::middleware('permission:expenses.delete')->delete('/expenses/{expense}',[ExpenseController::class,'destroy'])->name('expenses.destroy');
    Route::get('/reports',[ReportController::class,'index'])->name('reports');
    Route::get('/returns',[ReturnController::class,'index'])->name('returns.index');
    Route::post('/returns',[ReturnController::class,'store'])->name('returns.store');

    Route::get('/settings',[SettingsController::class,'index'])->name('settings');
    Route::post('/settings',[SettingsController::class,'update'])->name('settings.update');

    Route::middleware('permission:users.view')->get('/users',[UserManagementController::class,'index'])->name('users.index');
    Route::middleware('permission:users.create')->group(function(){ Route::get('/users/create',[UserManagementController::class,'create'])->name('users.create'); Route::post('/users',[UserManagementController::class,'store'])->name('users.store'); });
    Route::middleware('permission:users.edit')->group(function(){ Route::get('/users/{user}/edit',[UserManagementController::class,'edit'])->name('users.edit'); Route::put('/users/{user}',[UserManagementController::class,'update'])->name('users.update'); });
    Route::middleware('permission:users.delete')->delete('/users/{user}',[UserManagementController::class,'destroy'])->name('users.destroy');
    Route::middleware('permission:roles.manage')->group(function(){
        Route::get('/roles',[UserManagementController::class,'roles'])->name('roles.index');
        Route::post('/roles',[UserManagementController::class,'storeRole'])->name('roles.store');
        Route::put('/roles/{role}',[UserManagementController::class,'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}',[UserManagementController::class,'destroyRole'])->name('roles.destroy');
    });
});
