<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpensePurpose;
use App\Models\Income;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Purchase;
use App\Models\Purpose;
use App\Models\ReturnOrder;
use App\Models\ReturnPurchase;
use App\Models\SubscriptionHistory;
use App\Models\Supplier;
use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller {
    public function presentActive() {
        $data              = [];
        $data['show_data'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->whereDate('validity', '>=', today())
            ->where('status', 1)
            ->orderBy('validity', 'asc')
            ->paginate(100);

        return view('backend.client.present-active', $data);
    }

    public function presentInactive() {
        $data              = [];
        $data['show_data'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->where('status', 0)
            ->paginate(100);

        return view('backend.client.present-inactive', $data);
    }

    public function presentExpired() {
        $data              = [];
        $data['show_data'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->whereDate('validity', '<', today())
            ->paginate(100);

        return view('backend.client.present-expired', $data);
    }

    public function details($id) {
        $business_account = User::find($id);
        $users            = User::where('user_id', $business_account->user_id)->with(
            'role',
            'businessType',
            'country',
            'state',
            'policeStation'
        )->get();

        return view('backend.client.profile-details', compact('business_account', 'users'));
    }

    public function subscriptionHistory($id) {
        $subscription = SubscriptionHistory::where('user_id', $id)->orderBy('id', 'desc')->with('subscriptionReminder')->paginate(100);

        return view('backend.client.subscription-history', compact('subscription'));
    }

    public function customerList($id) {
        $customers = Customer::where('user_id', $id)->paginate(100);

        return view('backend.client.customer-list', compact('customers'));
    }

    public function supplierList($id) {
        $supplier = Supplier::where('user_id', $id)->paginate(100);

        return view('backend.client.supplier-list', compact('supplier'));
    }

    public function productList($id) {
        $products = Product::where('user_id', $id)->where('type', 'product')->paginate(100);

        return view('backend.client.product-list', compact('products'));
    }

    public function serviceList($id) {
        $service = Product::where('user_id', $id)->where('type', 'service')->paginate(100);

        return view('backend.client.service-list', compact('service'));
    }

    public function placedOrder($id) {
        $orders = Order::where('user_id', $id)->withCount('orderDetails')->paginate(100);

        return view('backend.client.placed-order', compact('orders'));
    }

    public function returnOrder($id) {
        $orders = ReturnOrder::where('user_id', $id)->withCount('orderDetails')->paginate(100);

        return view('backend.client.return-order', compact('orders'));
    }

    public function placedPurchase($id) {
        $purchase = Purchase::where('user_id', $id)->withCount('purchaseDetails')->paginate(100);

        return view('backend.client.placed-purchase', compact('purchase'));
    }

    public function returnPurchase($id) {
        $purchase = ReturnPurchase::where('user_id', $id)->withCount('returnPurchaseDetails')->paginate(100);

        return view('backend.client.return-purchase', compact('purchase'));
    }

    public function productUnit($id) {
        $unit = ProductUnit::where('user_id', $id)->paginate(100);

        return view('backend.client.product-unit', compact('unit'));
    }

    public function incomeType($id) {
        $income_type = Income::where('user_id', $id)->paginate(100);

        return view('backend.client.income-type', compact('income_type'));
    }

    public function incomePurpose($id) {
        $income_purpose = Purpose::where('user_id', $id)->paginate(100);

        return view('backend.client.income-purpose', compact('income_purpose'));
    }

    public function expenseType($id) {
        $expense_type = Expense::where('user_id', $id)->paginate(100);

        return view('backend.client.expense-type', compact('expense_type'));
    }

    public function expensePurpose($id) {
        $expense_purpose = ExpensePurpose::where('user_id', $id)->paginate(100);

        return view('backend.client.expense-purpose', compact('expense_purpose'));
    }

    public function balanceTransfer($id) {
        $balance_transfer = BalanceTransfer::where('user_id', $id)->paginate(100);

        return view('backend.client.balance-transfer', compact('balance_transfer'));
    }

    public function finance($id) {
        $data                      = [];
        $total_transaction_history = TransactionHistory::where('user_id', $id)
            ->with(
                'order',
                'purchase',
                'income_fc',
                'expense_ts',
                'returnOrder',
                'returnPurchase'
            )->get();

        $total_income_sell              = 0;
        $total_income_purchase_discount = 0;
        $total_income_purchase_return   = 0;
        $total_income_others_income     = 0;
        $total_sell_return_income       = 0;

        $total_expense_purchase       = 0;
        $total_expense_purchase_vat   = 0;
        $total_expense_sell_return    = 0;
        $total_expense_other_expenses = 0;

        foreach ($total_transaction_history as $transaction) {

            if ($transaction->order) {
                $total_income_sell += $transaction->order->total;

                foreach ($transaction->order->orderDetails as $orderDetails) {
                    $total_expense_purchase += ($orderDetails->quantity * $orderDetails->buying_price);
                }

            } elseif ($transaction->income_fc) {

                if ($transaction->income_fc->income_id == 12) {
                    $total_income_others_income += 0;
                } else {
                    $total_income_others_income += $transaction->income_fc->amount;
                }

            } elseif ($transaction->purchase) {
                $total_income_purchase_discount += $transaction->purchase->discount_price;
                $total_expense_purchase_vat += $transaction->purchase->vat_price;

            } elseif ($transaction->expense_ts) {

                if ($transaction->expense_ts->expense_id == 26) {
                    $total_expense_other_expenses += 0;
                } else {
                    $total_expense_other_expenses += $transaction->expense_ts->amount;
                }

            } elseif ($transaction->returnPurchase) {
                $total_income_purchase_return += $transaction->returnPurchase->total;
            } elseif ($transaction->returnOrder) {
                $total_expense_sell_return += $transaction->returnOrder->total;

                foreach ($transaction->returnOrder->orderDetails as $details) {

                    $total_sell_return_income += ($details->quantity * $details->buying_price);

                }

            }

        }

        $data['total_income']  = $total_income_sell + $total_income_purchase_return + $total_income_others_income;
        $data['total_expense'] = $total_expense_purchase + $total_expense_sell_return + $total_expense_other_expenses;
        //daily

        $daily_transaction_history = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', today())
            ->with(
                'order',
                'purchase',
                'income_fc',
                'expense_ts',
                'returnOrder',
                'returnPurchase'
            )->get();

        $daily_income_sell              = 0;
        $daily_income_purchase_discount = 0;
        $daily_income_purchase_return   = 0;
        $daily_income_others_income     = 0;
        $daily_sell_return_income       = 0;

        $daily_expense_purchase       = 0;
        $daily_expense_purchase_vat   = 0;
        $daily_expense_sell_return    = 0;
        $daily_expense_other_expenses = 0;

        foreach ($daily_transaction_history as $transaction) {

            if ($transaction->order) {
                $daily_income_sell += $transaction->order->total;

                foreach ($transaction->order->orderDetails as $orderDetails) {
                    $daily_expense_purchase += ($orderDetails->quantity * $orderDetails->buying_price);
                }

            } elseif ($transaction->income_fc) {

                if ($transaction->income_fc->income_id == 12) {
                    $daily_income_others_income += 0;
                } else {
                    $daily_income_others_income += $transaction->income_fc->amount;
                }

            } elseif ($transaction->purchase) {
                $daily_income_purchase_discount += $transaction->purchase->discount_price;
                $daily_expense_purchase_vat += $transaction->purchase->vat_price;

            } elseif ($transaction->expense_ts) {

                if ($transaction->expense_ts->expense_id == 26) {
                    $daily_expense_other_expenses += 0;
                } else {
                    $daily_expense_other_expenses += $transaction->expense_ts->amount;
                }

            } elseif ($transaction->returnPurchase) {
                $daily_income_purchase_return += $transaction->returnPurchase->total;
            } elseif ($transaction->returnOrder) {
                $daily_expense_sell_return += $transaction->returnOrder->total;

                foreach ($transaction->returnOrder->orderDetails as $details) {

                    $daily_sell_return_income += ($details->quantity * $details->buying_price);

                }

            }

        }

        $data['daily_income']  = $daily_income_sell + $daily_income_purchase_return + $daily_income_others_income;
        $data['daily_expense'] = $daily_expense_purchase + $daily_expense_sell_return + $daily_expense_other_expenses;

        //daily end

        $data['total_order']        = Order::where('user_id', $id)->withCount('orderDetails')->get();
        $data['today_order']        = Order::where('user_id', $id)->whereDate('created_at', date("Y-m-d"))->withCount('orderDetails')->get();
        $data['total_order_return'] = ReturnOrder::where('user_id', $id)->withCount('orderDetails')->get();
        $data['today_order_return'] = ReturnOrder::where('user_id', $id)->whereDate('created_at', date("Y-m-d"))->withCount('orderDetails')->get();

        $data['total_purchase']        = Purchase::where('user_id', $id)->withCount('purchaseDetails')->get();
        $data['today_purchase']        = Purchase::where('user_id', $id)->whereDate('created_at', date("Y-m-d"))->withCount('purchaseDetails')->get();
        $data['total_purchase_return'] = ReturnPurchase::where('user_id', $id)->withCount('returnPurchaseDetails')->get();
        $data['today_purchase_return'] = ReturnPurchase::where('user_id', $id)->whereDate('created_at', date("Y-m-d"))->withCount('returnPurchaseDetails')->get();

        $data['total_user']     = User::where('user_id', $id)->count();
        $data['package']        = SubscriptionHistory::where('user_id', $id)->orderBy('id', 'desc')->first();
        $data['total_customer'] = Customer::where('user_id', $id)->count();
        $data['total_supplier'] = Supplier::where('user_id', $id)->count();
        $data['product']        = Product::where('user_id', $id)->count();

        $data['package_history'] = SubscriptionHistory::where('user_id', $id)->orderBy('id', 'desc')->paginate(100);

        return view('backend.client.finance', $data);
    }

    public function active(Request $request, User $user) {
        $user->status = 1;
        $user->save();

        //active all user under this owner
        $allUser = User::where('user_id', $user->id)->get();

        foreach ($allUser as $item) {
            $item->status = 1;
            $item->save();
        }

        return back()->withToastSuccess('Client activated successfully with related users!!');
    }

    public function inactive(Request $request, User $user) {
        $user->status = 0;
        $user->save();

        //inactive all user under this owner
        $allUser = User::where('user_id', $user->id)->get();

        foreach ($allUser as $item) {
            $item->status = 0;
            $item->save();
        }

        return back()->withToastSuccess('Client inactivated successfully with related users!!');
    }

}
