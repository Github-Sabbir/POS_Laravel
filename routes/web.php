<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,DashboardController,ProductController,BarcodeController,PosController,PurchaseController,CustomerController,SupplierController,ExpenseController,ReportController,ReturnController,SettingsController,UserManagementController,CategoryController,BrandController};

Route::get('/',fn()=>redirect()->route('dashboard'));
Route::get('/login',[AuthController::class,'show'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('login.store');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function(){
    Route::middleware('permission:dashboard.view')->get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::middleware('permission:pos.access')->group(function(){
        Route::get('/pos',[PosController::class,'index'])->name('pos');
        Route::post('/pos/checkout',[PosController::class,'checkout'])->name('pos.checkout');
        Route::get('/pos/product/{query}',[PosController::class,'lookup'])->name('pos.lookup');
        Route::get('/pos/search',[PosController::class,'search'])->name('pos.search');
    });

    Route::middleware('permission:products.view')->group(function(){
        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/products/create',[ProductController::class,'create'])->middleware('permission:products.manage')->name('products.create');
        Route::get('/products/{product}/edit',[ProductController::class,'edit'])->middleware('permission:products.manage')->name('products.edit');
    });
    Route::middleware('permission:products.manage')->group(function(){
        Route::post('/products',[ProductController::class,'store'])->name('products.store');
        Route::put('/products/{product}',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}',[ProductController::class,'destroy'])->name('products.destroy');
        Route::post('/products/{product}/restore',[ProductController::class,'restore'])->name('products.restore');
        Route::post('/products/{product}/image',[ProductController::class,'image'])->name('products.image');
        Route::delete('/products/{product}/image',[ProductController::class,'removeImage'])->name('products.image.remove');
    });

    Route::middleware('permission:categories.manage')->resource('categories',CategoryController::class)->except(['show']);
    Route::middleware('permission:brands.manage')->resource('brands',BrandController::class)->except(['show']);

    Route::middleware('permission:barcodes.manage')->group(function(){
        Route::get('/barcodes',[BarcodeController::class,'index'])->name('barcodes.index');
        Route::post('/barcodes',[BarcodeController::class,'store'])->name('barcodes.store');
        Route::delete('/barcodes/{barcode}',[BarcodeController::class,'destroy'])->name('barcodes.destroy');
    });

    Route::middleware('permission:purchases.manage')->group(function(){
        Route::get('/purchases',[PurchaseController::class,'index'])->name('purchases.index');
        Route::get('/purchases/create',[PurchaseController::class,'create'])->name('purchases.create');
        Route::post('/purchases',[PurchaseController::class,'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}',[PurchaseController::class,'show'])->name('purchases.show');
    });

    Route::middleware('permission:customers.manage')->group(function(){
        Route::resource('customers',CustomerController::class)->except(['show']);
        Route::post('/customers/{customer}/payment',[CustomerController::class,'payment'])->name('customers.payment');
    });
    Route::middleware('permission:suppliers.manage')->group(function(){
        Route::resource('suppliers',SupplierController::class)->except(['show']);
        Route::post('/suppliers/{supplier}/payment',[SupplierController::class,'payment'])->name('suppliers.payment');
    });
    Route::middleware('permission:expenses.manage')->resource('expenses',ExpenseController::class)->except(['show']);
    Route::middleware('permission:reports.view')->get('/reports',[ReportController::class,'index'])->name('reports');
    Route::middleware('permission:returns.manage')->group(function(){Route::get('/returns',[ReturnController::class,'index'])->name('returns.index');Route::post('/returns',[ReturnController::class,'store'])->name('returns.store');});
    Route::middleware('permission:settings.manage')->group(function(){Route::get('/settings',[SettingsController::class,'index'])->name('settings');Route::post('/settings',[SettingsController::class,'update'])->name('settings.update');});

    Route::middleware('permission:users.view')->group(function(){
        Route::get('/users',[UserManagementController::class,'index'])->name('users.index');
        Route::get('/users/create',[UserManagementController::class,'create'])->name('users.create');
        Route::post('/users',[UserManagementController::class,'store'])->name('users.store');
        Route::get('/users/{user}/edit',[UserManagementController::class,'edit'])->name('users.edit');
        Route::put('/users/{user}',[UserManagementController::class,'update'])->name('users.update');
        Route::delete('/users/{user}',[UserManagementController::class,'destroy'])->name('users.destroy');
    });
    Route::middleware('permission:roles.manage')->group(function(){Route::get('/roles',[UserManagementController::class,'roles'])->name('roles.index');Route::post('/roles',[UserManagementController::class,'storeRole'])->name('roles.store');Route::put('/roles/{role}',[UserManagementController::class,'updateRole'])->name('roles.update');Route::delete('/roles/{role}',[UserManagementController::class,'destroyRole'])->name('roles.destroy');});
});
