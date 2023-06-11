<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeExpense;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PaymentType;
use App\Models\PreviousCashinHand;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnOrder;
use App\Models\ReturnPurchase;
use App\Models\Supplier;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;

class DayBookController extends Controller {
    public function dayBook(Request $request) {

        $data = [];

        $daily_transaction_history = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', $request->current_date)
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
                $daily_income_sell += $transaction->order->received_amount;
            } elseif ($transaction->income_fc) {
                $daily_income_others_income += $transaction->income_fc->amount;
            } elseif ($transaction->purchase) {
                $daily_income_purchase_discount += $transaction->purchase->discount_price;
                $daily_expense_purchase_vat += $transaction->purchase->vat_price;
                $daily_expense_purchase += $transaction->purchase->payment_amount;
            } elseif ($transaction->expense_ts) {
                $daily_expense_other_expenses += $transaction->expense_ts->amount;
            } elseif ($transaction->returnPurchase) {
                $daily_income_purchase_return += $transaction->returnPurchase->payment_amount;
            } elseif ($transaction->returnOrder) {
                $daily_expense_sell_return += $transaction->returnOrder->received_amount;

                foreach ($transaction->returnOrder->orderDetails as $details) {

                    $daily_sell_return_income += ($details->quantity * $details->buying_price);

                }

            }

        }

        $data['daily_income_sell']              = $daily_income_sell;
        $data['daily_income_purchase_discount'] = $daily_income_purchase_discount;
        $data['daily_income_purchase_return']   = $daily_income_purchase_return;
        $data['daily_income_others_income']     = $daily_income_others_income;
        $data['daily_sell_return_income']       = $daily_sell_return_income;

        $data['daily_expense_purchase']       = $daily_expense_purchase;
        $data['daily_expense_purchase_vat']   = $daily_expense_purchase_vat;
        $data['daily_expense_sell_return']    = $daily_expense_sell_return;
        $data['daily_expense_other_expenses'] = $daily_expense_other_expenses;
        $data['previous_cash_in_hand']        = PreviousCashinHand::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', today())
            ->get();

        $data['previous_cash_in_hand'] = PreviousCashinHand::where('user_id', auth()->user()->user_id)->whereDate('created_at', $request->current_date)->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function dailyProfitAndLoss(Request $request) {
        //ok//
        $profilt_and_loss_by_date = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->get()->groupBy(function ($query) {
            return $query->created_at->format('Y-m-d');
        });

        // return $profilt_and_loss_by_date;
        $data = [];

        foreach ($profilt_and_loss_by_date as $key => $profit_and_loss) {
            $group_date = $profit_and_loss->first()->created_at->format('Y-m-d');

            /**
             * daily income
             */

            $daily_transaction_history = TransactionHistory::where('user_id', auth()->user()->user_id)
                ->whereDate('created_at', $group_date)
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

            //daily income
            $data[$key]['daily_income_sell']              = $daily_income_sell;
            $data[$key]['daily_income_purchase_discount'] = $daily_income_purchase_discount;
            $data[$key]['daily_income_purchase_return']   = $daily_income_purchase_return;
            $data[$key]['daily_income_others_income']     = $daily_income_others_income;
            $data[$key]['daily_sell_return_income']       = $daily_sell_return_income;

            //daily expense
            $data[$key]['daily_expense_purchase']       = $daily_expense_purchase;
            $data[$key]['daily_expense_purchase_vat']   = $daily_expense_purchase_vat;
            $data[$key]['daily_expense_sell_return']    = $daily_expense_sell_return;
            $data[$key]['daily_expense_other_expenses'] = $daily_expense_other_expenses;

        }

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function dayBetweenBook(Request $request) {
        //ok//
        $data = [];

        $total_transaction_history = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
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

        //total income
        $data['total_income_sell']              = $total_income_sell;
        $data['total_income_purchase_discount'] = $total_income_purchase_discount;
        $data['total_income_purchase_return']   = $total_income_purchase_return;
        $data['total_income_others_income']     = $total_income_others_income;
        $data['total_sell_return_income']       = $total_sell_return_income;

        //total expense
        $data['total_expense_purchase']       = $total_expense_purchase;
        $data['total_expense_purchase_vat']   = $total_expense_purchase_vat;
        $data['total_expense_sell_return']    = $total_expense_sell_return;
        $data['total_expense_other_expenses'] = $total_expense_other_expenses;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function dashboard(Request $request) {

        $data = [];

        $data['strategy'] = dailyTotalIe();

        //cash in hand start
        $data['cash_in_hand'] = PaymentType::whereNull('user_id')
            ->orWhere('user_id', auth()->user()->user_id)
            ->with([
                'order',
                'purchase',
                'returnOrder',
                'returnPurchase',
                'income',
                'expense',
                'previousCashinHand' => function ($query) {
                    return $query->where('user_id', auth()->user()->user_id);
                },
                'senderBalanceTransfer',
                'receiverBalanceAccept',
            ])
            ->get();

        $data['total_purchase'] = Purchase::where('user_id', auth()->user()->user_id)->sum('total');

        $data['daily_purchase'] = Purchase::where('user_id', auth()->user()->user_id)->whereDate('created_at', date("Y-m-d"))->sum('total');

        //total and daily due start
        $check_customer = Customer::where('user_id', auth()->user()->user_id)->get();

        $customer           = [];
        $total_customer_due = 0;

        foreach ($check_customer as $customer_item) {
            $check_customer_due = 0;

            $check_customer_due += $customer_item->amount;

            foreach ($customer_item->orders as $co_item) {

                if ($co_item->balance != 0) {
                    $check_customer_due += $co_item->balance;
                }

            }

            foreach ($customer_item->incomeExpenses as $cie_item) {

                if ($cie_item->income_id == 12) {
                    $check_customer_due -= $cie_item->amount;
                }

            }

            foreach ($customer_item->returnOrders as $return) {

                if ($return->balance != 0) {
                    $check_customer_due -= $return->balance;
                }

            }

            $customer_item['due'] = $check_customer_due;
            $customer[]           = $customer_item;

            if ($check_customer_due > 0) {
                $total_customer_due += $check_customer_due;
            }

        }

        $data['total_due']            = $total_customer_due;
        $data['total_due_collection'] = IncomeExpense::where('user_id', auth()->user()->user_id)->where('income_id', 12)->sum('amount');

        $todays_due                     = 0;
        $todays_due                     = Order::where('user_id', auth()->user()->user_id)->whereDate('created_at', date('Y-m-d'))->where('balance', '>', 0)->sum('balance');
        $customer_previous_amount_today = Customer::where('user_id', auth()->user()->user_id)->whereDate('modify_date', today())->get();

        foreach ($customer_previous_amount_today as $tcpa) {
            $todays_due += $tcpa->amount;
        }

        $data['todays_due']            = $todays_due;
        $data['todays_due_collection'] = IncomeExpense::where('user_id', auth()->user()->user_id)->whereDate('created_at', date('Y-m-d'))->where('income_id', 12)->sum('amount');

        //stock starts
        $stock       = 0;
        $stock_value = 0;
        $product     = Product::where('user_id', auth()->user()->user_id)->where('type', 'product')->where('quantity', '>', 0)->get();

        foreach ($product as $item) {
            $stock += ($item->quantity);
            $stock_value += ($item->buying_price * $item->quantity);
        }

        $data['total_stock'] = $stock;
        $data['stock_value'] = $stock_value;

        //stock ends

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function dayBookSingleDay(Request $request) {
        $data                 = [];
        $data['cash_in_hand'] = PaymentType::whereNull('user_id')
            ->orWhere('user_id', auth()->user()->user_id)
            ->with([
                'order',
                'purchase',
                'returnOrder',
                'returnPurchase',
                'income',
                'expense',
                'previousCashinHand',
                'senderBalanceTransfer',
                'receiverBalanceAccept',
            ])
            ->get();

        $data['strategy'] = dailyTotalIe();

        /**
         * income
         */

        $order_sales = Order::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('total');

        $order_dues = Order::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('balance', '>', 0)
            ->sum('balance');

        $due_collect         = IncomeExpense::where('user_id', auth()->user()->user_id)->where('income_id', 12)->whereDate('created_at', date('Y-m-d'))->sum('amount');
        $data['order_sales'] = (double) $order_sales;
        $data['order_cash']  = Order::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('received_amount');
        $data['order_dues'] = Order::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('balance', '>', 0)
            ->sum('balance');

        $data['due_received'] = $due_collect;

        /**
         * income end
         *
         */

        /**
         * expense
         */

        $purchase_total = Purchase::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('total');

        $purchase_dues = Purchase::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('balance', '>', 0)
            ->sum('balance');

        $due_pay                = IncomeExpense::where('user_id', auth()->user()->user_id)->where('expense_id', 26)->whereDate('created_at', date('Y-m-d'))->sum('amount');
        $data['purchase_total'] = (double) $purchase_total;
        $data['purchase_cash']  = Purchase::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('payment_amount');
        $data['purchase_dues'] = $purchase_dues;
        /**
         * expense end
         */

        /**
         * supplier return
         */

        $supplier_return_total = ReturnPurchase::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('total');

        $supplier_return_dues = ReturnPurchase::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('balance', '>', 0)
            ->sum('balance');

        $data['supplier_return_total'] = (double) $supplier_return_total;
        $data['supplier_return_cash']  = $supplier_return_total - $supplier_return_dues;
        $data['supplier_return_dues']  = $supplier_return_dues;

        /**
         * supplier return end
         */

        /**
         * customer return
         */
        $order_return_sales = ReturnOrder::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('total');

        $order_return_dues = ReturnOrder::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('balance', '>', 0)
            ->sum('balance');

        $data['order_return_sales'] = (double) $order_return_sales;
        $data['order_return_cash']  = $order_return_sales - $order_return_dues;
        $data['order_return_dues']  = $order_return_dues;
        /**
         * customer return end
         */

        $data['invest']         = IncomeExpense::where('user_id', auth()->user()->user_id)->where('income_id', 13)->sum('amount');
        $data['withdraw']       = IncomeExpense::where('user_id', auth()->user()->user_id)->where('expense_id', 49)->sum('amount');
        $data['daily_purchase'] = Purchase::where('user_id', auth()->user()->user_id)->whereDate('created_at', today())->sum('total');

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function userNotification(Request $request) {
        $data = Notification::whereNull('user_id')->orderBy('id', 'desc')->paginate();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

}
