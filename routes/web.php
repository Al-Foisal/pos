<?php

use App\Http\Controllers\Backend\AdminAuthController;
use App\Http\Controllers\Backend\AdminForgotPasswordController;
use App\Http\Controllers\Backend\AdminResetPasswordController;
use App\Http\Controllers\Backend\AppFeatureController;
use App\Http\Controllers\Backend\BusinessTypeController;
use App\Http\Controllers\Backend\ClientController;
use App\Http\Controllers\Backend\CompanyInfoController;
use App\Http\Controllers\Backend\CountryController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\PackageController;
use App\Http\Controllers\Backend\PackageFeatureController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\PoliceStationController;
use App\Http\Controllers\Backend\SliderController;
use App\Http\Controllers\Backend\StateController;
use App\Http\Controllers\GeneralHelperController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GeneralHelperController::class, 'home']);
Route::get('/links/{slug}', [GeneralHelperController::class, 'pageDetails'])->name('page_details');

Route::prefix('/admin')->name('admin.auth.')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/store-login', [AdminAuthController::class, 'storeLogin'])->name('storeLogin');

    Route::get('/forgot-password', [AdminForgotPasswordController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('/forgot-password', [AdminForgotPasswordController::class, 'storeForgotPassword'])->name('storeForgotPassword');

    Route::get('/reset-password/{token}', [AdminResetPasswordController::class, 'resetPassword'])->name('resetPassword');
    Route::post('/reset-password', [AdminResetPasswordController::class, 'storeForgotPassword'])->name('storeResetPassword');
});

Route::middleware('auth:admin')->prefix('/admin')->name('admin.')->group(function () {

    //admin management
    Route::controller(AdminAuthController::class)->name('auth.')->group(function () {
        Route::get('/admin-list', 'adminList')->name('adminList');
        Route::get('/create-admin', 'createAdmin')->name('createAdmin');
        Route::post('/store-admin', 'storeAdmin')->name('storeAdmin');
        Route::get('/edit-admin/{admin}', 'editAdmin')->name('editAdmin');
        Route::post('/update-admin/{admin}', 'updateAdmin')->name('updateAdmin');
        Route::post('/active-admin/{admin}', 'activeAdmin')->name('activeAdmin');
        Route::post('/inactive-admin/{admin}', 'inactiveAdmin')->name('inactiveAdmin');
        Route::delete('/delete-admin/{admin}', 'deleteAdmin')->name('deleteAdmin');

        Route::get('/customer-list', 'customerList')->name('customerList');
        Route::post('/update-custom-subscription', 'updateCustomSubscription')->name('updateCustomSubscription');
    });

    Route::resource('/feature', AppFeatureController::class);
    Route::resource('/slider', SliderController::class);

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('auth.logout');

    Route::controller(BusinessTypeController::class)->prefix('/business_type')->name('business_type.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{business_type}', 'edit')->name('edit');
        Route::put('/update/{business_type}', 'update')->name('update');
        Route::post('/active/{business_type}', 'active')->name('active');
        Route::post('/inactive/{business_type}', 'inactive')->name('inactive');
    });

    Route::controller(CountryController::class)->prefix('/country')->name('country.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{country}', 'edit')->name('edit');
        Route::put('/update/{country}', 'update')->name('update');
        Route::post('/active/{country}', 'active')->name('active');
        Route::post('/inactive/{country}', 'inactive')->name('inactive');
    });

    Route::controller(StateController::class)->prefix('/state')->name('state.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{state}', 'edit')->name('edit');
        Route::put('/update/{state}', 'update')->name('update');
        Route::post('/active/{state}', 'active')->name('active');
        Route::post('/inactive/{state}', 'inactive')->name('inactive');
    });

    Route::controller(PoliceStationController::class)->prefix('/police-station')->name('p_s.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{p_s}', 'edit')->name('edit');
        Route::put('/update/{p_s}', 'update')->name('update');
        Route::post('/active/{p_s}', 'active')->name('active');
        Route::post('/inactive/{p_s}', 'inactive')->name('inactive');
    });

    Route::controller(PackageFeatureController::class)->prefix('/package_feature')->name('package_feature.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{package_feature}', 'edit')->name('edit');
        Route::put('/update/{package_feature}', 'update')->name('update');
        Route::post('/active/{package_feature}', 'active')->name('active');
        Route::post('/inactive/{package_feature}', 'inactive')->name('inactive');
    });

    Route::controller(PackageController::class)->prefix('/package')->name('package.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{package}', 'edit')->name('edit');
        Route::put('/update/{package}', 'update')->name('update');
        Route::post('/active/{package}', 'active')->name('active');
        Route::post('/inactive/{package}', 'inactive')->name('inactive');
    });

    Route::controller(PageController::class)->prefix('/page')->name('page.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{page}', 'edit')->name('edit');
        Route::put('/update/{page}', 'update')->name('update');
        Route::post('/active/{page}', 'active')->name('active');
        Route::post('/inactive/{page}', 'inactive')->name('inactive');
        Route::delete('/delete/{page}', 'delete')->name('delete');
    });

    Route::get('/company-info', [CompanyInfoController::class, 'showCompanyInfo'])->name('showCompanyInfo');
    Route::post('/company-info', [CompanyInfoController::class, 'storeCompanyInfo'])->name('storeCompanyInfo');
    Route::get('/company-notification', [CompanyInfoController::class, 'getCompanyNotification'])->name('getCompanyNotification');
    Route::post('/store-company-notification', [CompanyInfoController::class, 'storeCompanyNotification'])->name('storeCompanyNotification');

    Route::controller(ClientController::class)->prefix('/clients')->name('client.')->group(function () {
        Route::get('/present-active', 'presentActive')->name('presentActive');
        Route::get('/present-inactive', 'presentInactive')->name('presentInactive');
        Route::get('/present-expired', 'presentExpired')->name('presentExpired');
        Route::get('/profile-details/{id}', 'details')->name('details');
        Route::get('/subscription-history/{id}', 'subscriptionHistory')->name('subscriptionHistory');
        Route::get('/customer-list/{id}', 'customerList')->name('customerList');
        Route::get('/supplier-list/{id}', 'supplierList')->name('supplierList');
        Route::get('/product-list/{id}', 'productList')->name('productList');
        Route::get('/service-list/{id}', 'serviceList')->name('serviceList');
        Route::get('/placed-order/{id}', 'placedOrder')->name('placedOrder');
        Route::get('/return-order/{id}', 'returnOrder')->name('returnOrder');
        Route::get('/placed-purchase/{id}', 'placedPurchase')->name('placedPurchase');
        Route::get('/return-purchase/{id}', 'returnPurchase')->name('returnPurchase');
        Route::get('/product-unit/{id}', 'productUnit')->name('productUnit');
        Route::get('/income-type/{id}', 'incomeType')->name('incomeType');
        Route::get('/income-purpose/{id}', 'incomePurpose')->name('incomePurpose');
        Route::get('/expense-type/{id}', 'expenseType')->name('expenseType');
        Route::get('/expense-purpose/{id}', 'expensePurpose')->name('expensePurpose');
        Route::get('/balance-transfer/{id}', 'balanceTransfer')->name('balanceTransfer');
        Route::get('/finance/{id}', 'finance')->name('finance');
        Route::post('/active/{user}', 'active')->name('active');
        Route::post('/inactive/{user}', 'inactive')->name('inactive');
    });
});

Route::controller(GeneralHelperController::class)->prefix('/general')->group(function () {
    Route::get('/get-state/{id}', 'getState');
});
