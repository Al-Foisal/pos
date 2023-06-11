<?php

use App\Http\Controllers\Api\BalanceTransferController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DayBookController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpensePurposeController;
use App\Http\Controllers\Api\FavoriteOrderController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\IncomeExpenseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductUnitController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\PurposController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SubscriptionOperationController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/logout', function (Request $request) {
    $user = $request->user();
    $user->tokens()->delete();
    Auth::guard('web')->logout();

    return ['status' => true, 'message' => 'Logout Successful!'];
});

Route::controller(UserAuthController::class)->prefix('/auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/login', 'login');
    Route::post('/store-forgot-password', 'storeForgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::post('/resend-otp', 'resendOTP');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:sanctum')->controller(UserProfileController::class)->prefix('/profile')->group(function () {
    Route::get('/user', 'user');
    Route::post('/user-list', 'userList');
    Route::post('/user-details', 'userDetails');
    Route::post('/store', 'store');
    Route::post('/update', 'update');
    Route::post('/active', 'active');
    Route::post('/inactive', 'inactive');
    Route::delete('/delete/{id}', 'delete');
    Route::post('/rr', 'rr');
});

Route::middleware('auth:sanctum')->controller(SubscriptionOperationController::class)->prefix('/package')->group(function () {
    Route::post('/list', 'packageList');
    Route::post('/feature', 'packageFeature');
    Route::post('/details', 'packageDetails');
    Route::post('/subscription', 'packageSubscription');
    Route::post('/subscription-history', 'subscriptionHistory');
    Route::post('/present-subscription', 'presentSubscription');
    Route::post('/set-reminder', 'setReminder');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'customers'        => CustomerController::class,
        'suppliers'        => SupplierController::class,
        'product-units'    => ProductUnitController::class,
        'incomes'          => IncomeController::class,
        'expenses'         => ExpenseController::class,
        'purposes'         => PurposController::class,
        'expense_purposes' => ExpensePurposeController::class,
        'balance_transfer' => BalanceTransferController::class,
    ]);
    Route::post('/user-notification', [DayBookController::class, 'userNotification']);
});
Route::middleware('auth:sanctum')->controller(ProductController::class)->prefix('/due-alert')->group(function () {
    Route::get('/due-alert', 'dueAlert');
    Route::post('/store-due-alert', 'storeDueAlert');
    Route::delete('/delete-due-alert/{id}', 'deleteDueAlert');
});
Route::middleware('auth:sanctum')->controller(ProductController::class)->prefix('/product')->group(function () {
    Route::post('/list', 'list');
    Route::post('/store', 'store');
    Route::post('/details', 'details');
    Route::post('/barcode-details', 'barcodeDetails');
    Route::put('/update', 'update');
    Route::post('/edit-vat', 'editVat');
    Route::post('/delete-vat/{id}', 'deleteVat');
    Route::post('/low-stock-suantity-alert', 'lowStockQuantityAlert');
    Route::post('/delete-low-stock-suantity-alert', 'deleteLowStockQuantityAlert');
});

Route::middleware('auth:sanctum')->controller(OrderController::class)->prefix('/order')->group(function () {
    Route::post('/customerwise-salelist', 'customerwiseSalelist');
    Route::post('/itemwise-salelist', 'itemwiseSalelist');
    Route::post('/store', 'store');
    Route::put('/update', 'update');
    Route::post('/invoice', 'invoice');
    Route::post('/return', 'returnOrder');
    Route::post('/update-return', 'updateReturnOrder');
    Route::post('/return-invoice/{invoice_no}', 'returnInvoice');
    Route::post('/return-list', 'returnList');
    Route::delete('/delete/{id}', 'delete');
    Route::post('/sales-report', 'salesReport');
    Route::get('/get-last-invoice', 'getLastInvoice');
    Route::get('/get-return-last-invoice', 'getReturnLastInvoice');
});

Route::middleware('auth:sanctum')->controller(FavoriteOrderController::class)->prefix('/favorite-order')->group(function () {
    Route::post('/list', 'list');
    Route::post('/store', 'store');
    Route::post('/details', 'details');
    Route::delete('/delete/{id}', 'delete');
});

Route::middleware('auth:sanctum')->controller(PaymentTypeController::class)->prefix('/payment-type')->group(function () {
    Route::post('/index', 'index');
    Route::post('/store', 'store');
    Route::post('/show/{id}', 'show');
    Route::post('/update/{id}', 'update');
    Route::post('/active/{id}', 'active');
    Route::post('/inactive/{id}', 'inactive');
    Route::post('/store-previous-cash-in-hand', 'storePreviousCashinHand');
});

Route::middleware('auth:sanctum')->controller(PurchaseController::class)->prefix('/purchase')->group(function () {
    Route::post('/supplierwise-purchaselist', 'supplierwisePurchaselist');
    Route::post('/purchaselist', 'Purchaselist');
    Route::post('/store', 'store');
    Route::put('/update', 'update');
    Route::post('/invoice', 'invoice');
    Route::post('/return', 'returnPurchase');
    Route::post('/update-return', 'updateReturnPurchase');
    Route::delete('/delete/{id}', 'delete');
    Route::post('/sales-report', 'salesReport');
    Route::post('/purchases-report', 'purchasesReport');
    Route::post('/purchases-voucher-image', 'purchasesVoucherImage');
    Route::post('/list-from-and-to', 'listfromAndTo');
    Route::post('/purchase-from-supplier-details', 'purchaseFromSupplierDetails');
    Route::post('/latest-purchase-invoice-number', 'latestPurchaseInvoiceNumber');
    Route::post('/latest-purchase-return-invoice-number', 'latestPurchaseReturnInvoiceNumber');
    Route::post('/send-to-order-list', 'sendToOrderList');
    Route::post('/store-send-to-order-list', 'storeSendToOrderList');
    Route::post('/delete-send-to-order-list/{id}', 'deleteSendToOrderList');
    Route::post('/return-invoice/{invoice_no}', 'returnInvoice');
    Route::post('/return-list', 'returnList');
});

Route::middleware('auth:sanctum')->controller(IncomeExpenseController::class)->prefix('/income_expense')->group(function () {
    Route::post('/transaction-history', 'transactionHistory');
    Route::post('/due-orderview', 'dueOverview');
    Route::post('/store', 'store');
    Route::post('/update', 'update');
    Route::post('/customer-supplier-transaction-details', 'customerSupplierTransactionDetails');
    Route::post('/customer-supplier-due-list', 'customerSupplierDueList');
    Route::post('/customer-due-report', 'customerDueReport');
    Route::post('/supplier-due-report', 'supplierDueReport');
    Route::get('/show-income-expense-by-id/{id}', 'showIncomeExpenseById');
    Route::post('/income-expence-report', 'incomeExpenceReport');
    Route::get('/latest-income-expense-invoice-number', 'latestIncomeExpenseInvoiceNumber');
});

Route::middleware('auth:sanctum')->controller(DayBookController::class)->prefix('/day-book')->group(function () {
    Route::post('/', 'dayBook');
    Route::post('/daily-profit-and-loss', 'dailyProfitAndLoss');
    Route::post('/day-between-book', 'dayBetweenBook');
    Route::post('/dashboard', 'dashboard');
    Route::post('/pp', 'pp');
    Route::post('/day-book-single-day', 'dayBookSingleDay');
});

Route::controller(GeneralController::class)->middleware('auth:sanctum')->prefix('/general')->group(function () {
    Route::get('/available-balance/{payment_type_id}', 'availableBalance');
    Route::get('/payment_type', 'paymentType');
    Route::get('/group/list', 'groupList');
    Route::post('/group/store', 'storeGroup');
    Route::get('/active/role', 'activeRole');
    Route::get('/active/country', 'activeCountry');
    Route::get('/active/state/{country_id}', 'activeState');
    Route::get('/active/police-station/{country_id}/{state_id}', 'activePoliceStation');
    Route::get('/sales-man-list', 'salesManList');
    Route::get('/purchase-man-list', 'purchaseManList');
    Route::post('/stock-report', 'stockReport');
    Route::get('/customer-last-due-invoice/{customer_id}', 'customerLastDueInvoice');
    Route::get('/supplier-last-due-invoice/{supplier_id}', 'supplierLastDueInvoice');
    Route::get('/order-invoice', 'orderInvoice');
    Route::get('/supplier-invoice', 'supplierInvoice');
    Route::get('/other-invoice', 'otherInvoice');
    Route::get('/business-type', 'businessType');
    Route::get('/check-cash-in-hand-by-id/{payment_type_id}', 'checkCashInHandById');
});

Route::controller(SearchController::class)->middleware('auth:sanctum')->prefix('/search')->group(function () {
    Route::get('/purchase', 'purchaseSearch');
    Route::get('/order-list', 'orderList');
    Route::get('/item-vat-list', 'itemVatList');
    Route::get('/sale-list', 'salesList');
    Route::get('/stock-list', 'stockList');
    Route::get('/low-stock-alert-list', 'lowStockAlertLis');
    Route::get('/customer-return', 'customerReturn');
    Route::get('/supplier-return', 'supplierReturn');
    Route::get('/customer-due-payment-list', 'customerDuePaymentList');
    Route::get('/supplier-due-payment-list', 'supplierDuePaymentList');
    Route::get('/balance-transfer', 'balanceTransfer');
    Route::get('/other-invoice-list', 'otherInvoiceList');
    Route::get('/income-search', 'incomeSearch');
    Route::get('/expense-search', 'expenseSearch');

});
