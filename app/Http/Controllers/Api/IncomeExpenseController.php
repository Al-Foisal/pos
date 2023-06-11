<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\IncomeExpense;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class IncomeExpenseController extends Controller {

    public function transactionHistory(Request $request) {
        $data           = [];
        $data['income'] = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereNull('purchase_id')
            ->whereNull('expense_id')
            ->whereNull('return_order_id')
            ->orderBy('id', 'desc')
            ->select('id', 'user_id', 'order_id', 'return_purchase_id', 'income_id', 'created_at', 'updated_at')
            ->with([
                'order.orderDetails.product',
                'order.paymentType',
                'order.customer',
                'businessAccount',
                'income_fc' => function ($query) {
                    return $query->where('income_id', '!=', 12);
                },
                'income_fc.customer',
                'income_fc.income',
                'income_fc.paymentType',
                'returnPurchase.supplier',
                'returnPurchase.returnPurchaseDetails.product',
            ])
            ->paginate(100);
        $data['expense'] = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereNull('order_id')
            ->whereNull('income_id')
            ->whereNull('return_purchase_id')
            ->orderBy('id', 'desc')
            ->select('id', 'user_id', 'purchase_id', 'return_order_id', 'expense_id', 'created_at', 'updated_at')
            ->with([
                'purchase.purchaseDetails.product',
                'purchase.paymentType',
                'purchase.supplier',
                'businessAccount',
                'expense_ts.supplier',
                'expense_ts' => function ($query) {
                    return $query->where('expense_id', '!=', 26);
                },
                'expense_ts.expense',
                'expense_ts.paymentType',
                'returnOrder.orderDetails.product',
                'returnOrder.customer',
            ])
            ->paginate(100);

        $data['total_purchase'] = Purchase::where('user_id', auth()->user()->user_id)->sum('total');
        $data['daily_purchase'] = Purchase::where('user_id', auth()->user()->user_id)->whereDate('created_at', today())->sum('total');

        /////////////////
        $data['strategy'] = DAILY_TOTAL_IE();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    //due list
    public function dueOverview(Request $request) {
        $data = [];

        $customer_previous_amount = Customer::where('user_id', auth()->user()->user_id)->whereDate('modify_date', date("Y-m-d"))->get();
        $supplier_previous_amount = Supplier::where('user_id', auth()->user()->user_id)->whereDate('modify_date', date("Y-m-d"))->get();

        //customer due details
        $orders = Order::where('user_id', auth()->user()->user_id)->whereDate('created_at', date("Y-m-d"))->get();

        $todays_customer_due = 0;

        foreach ($customer_previous_amount as $cpa) {
            $todays_customer_due += $cpa->amount;
        }

        foreach ($orders as $order) {

            if ($order->balance > 0) {
                $todays_customer_due += $order->balance;
            }

        }

        //supplier due details
        $purchase = Purchase::where('user_id', auth()->user()->user_id)->whereDate('created_at', date("Y-m-d"))->get();

        $todays_supplier_due = 0;

        foreach ($supplier_previous_amount as $spa) {
            $todays_supplier_due += $spa->amount;
        }

        foreach ($purchase as $p) {

            if ($p->balance > 0) {
                $todays_supplier_due += $p->balance;
            }

        }

        //customer and supplier collection
        $expenses = IncomeExpense::where('user_id', auth()->user()->user_id)->whereDate('created_at', date("Y-m-d"))->get();

        $todays_customer_collection = 0;
        $todays_supplier_collection = 0;

        foreach ($expenses as $exp) {

            if ($exp->customer_id && $exp->income_id == 12) {
                $todays_customer_collection += $exp->amount;
            } elseif ($exp->supplier_id && $exp->expense_id == 26) {
                $todays_supplier_collection += $exp->amount;
            }

        }

        $total_expenses = IncomeExpense::where('user_id', auth()->user()->user_id)->get();

        $total_customer_collection = 0;
        $total_supplier_collection = 0;

        foreach ($total_expenses as $exp) {

            if ($exp->customer_id && $exp->income_id == 12) {
                $total_customer_collection += $exp->amount;
            } elseif ($exp->supplier_id && $exp->expense_id == 26) {
                $total_supplier_collection += $exp->amount;
            }

        }

        $data['todays_customer_due'] = $todays_customer_due;
        // $data['total_customer_due']  = Customer::where('user_id', auth()->user()->user_id)->where('amount', '>', 0)->sum('amount');
        $data['todays_supplier_due'] = $todays_supplier_due;
        // $data['total_supplier_due']  = Supplier::where('user_id', auth()->user()->user_id)->where('amount', '>', 0)->sum('amount');

        $data['todays_customer_collection'] = $todays_customer_collection;
        $data['todays_supplier_collection'] = $todays_supplier_collection;

        $data['total_customer_collection'] = $total_customer_collection;
        $data['total_supplier_collection'] = $total_supplier_collection;

        $check_customer = Customer::where('user_id', auth()->user()->user_id)->get();
        $check_supplier = Supplier::where('user_id', auth()->user()->user_id)->get();

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

        $supplier           = [];
        $total_supplier_due = 0;

        foreach ($check_supplier as $supplier_item) {
            $check_supplier_due = 0;

            $check_supplier_due += $supplier_item->amount;

            foreach ($supplier_item->purchase as $sp_item) {

                if ($sp_item->balance > 0) {
                    $check_supplier_due += $sp_item->balance;
                }

            }

            foreach ($supplier_item->incomeExpenses as $sie_item) {

                if ($sie_item->supplier_id && $sie_item->expense_id == 26) {
                    $check_supplier_due -= $sie_item->amount;
                }

            }

            foreach ($supplier_item->returnPurchase as $purchase) {

                if ($purchase->balance != 0) {
                    $check_supplier_due -= $purchase->balance;
                }

            }

            $supplier_item['due'] = $check_supplier_due;
            $supplier[]           = $supplier_item;

            if ($check_supplier_due > 0) {
                $total_supplier_due += $check_supplier_due;
            }

        }

        $data['total_supplier_due'] = $total_supplier_due;
        $data['total_customer_due'] = $total_customer_due;
        $data['supplier']           = $supplier;
        $data['customer']           = $customer;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);

    }

    //due collect
    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $invoice = IncomeExpense::where('user_id', auth()->user()->user_id)->orderBy('id', 'desc')->first();

            if ($invoice) {
                $invoice_no = $invoice->invoice_no + 1;
            } else {
                $invoice_no = 1;
            }

            if ($request->hasFile('voucher_image')) {

                $image_file = $request->file('voucher_image');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/income_expense/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            $ie = IncomeExpense::create([
                'user_id'         => auth()->user()->user_id,
                'customer_id'     => $request->customer_id,
                'supplier_id'     => $request->supplier_id,
                'accepted_as'     => $request->accepted_as,
                'invoice_no'      => $invoice_no,
                'invoice_date'    => $request->invoice_date ?? date("Y-m-d"),
                'income_id'       => $request->income_id,
                'expense_id'      => $request->expense_id,
                'amount'          => $request->amount,
                'purpose_id'      => $request->purpose_id,
                // 'expense_purpose_id' => $request->expense_purpose_id,
                'voucher_image'   => $final_name1 ?? null,
                'note'            => $request->note,
                'voucher_no'      => $request->voucher_no,
                'payment_type_id' => $request->payment_type_id,
            ]);

            if ($ie->accepted_as == 1) {
                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'Income added for invoice #' . $ie->invoice_no;
                $notification->save();
                //storing transaction history for income
                $th              = new TransactionHistory();
                $th->user_id     = auth()->user()->user_id;
                $th->income_id   = $ie->id;
                $th->customer_id = $ie->customer_id;
                $th->save();
            } else {
                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'Expense added for invoice #' . $ie->invoice_no;
                $notification->save();
                //storing transaction history for expenses
                $th              = new TransactionHistory();
                $th->user_id     = auth()->user()->user_id;
                $th->expense_id  = $ie->id;
                $th->supplier_id = $ie->supplier_id;
                $th->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Data added successfully!!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function update(Request $request) {

        DB::beginTransaction();
        try {

            $ie = IncomeExpense::find($request->id);

            

            if ($request->hasFile('voucher_image')) {

                $image_file = $request->file('voucher_image');

                if ($image_file) {

                    $image_path = public_path($ie->voucher_image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/income_expense/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $ie->voucher_image = $final_name1;
                    $ie->save();
                }

            }

            $ie->customer_id     = $request->customer_id;
            $ie->supplier_id     = $request->supplier_id;
            $ie->accepted_as     = $request->accepted_as;
            $ie->income_id       = $request->income_id;
            $ie->expense_id      = $request->expense_id;
            $ie->amount          = $request->amount;
            $ie->purpose_id      = $request->purpose_id;
            $ie->payment_type_id = $request->payment_type_id;
            $ie->note            = $request->note;
            $ie->save();

            if ($ie->accepted_as == 1) {
                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'Income updated for invoice #' . $ie->invoice_no;
                $notification->save();
            } else {
                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'Expense updated for invoice #' . $ie->invoice_no;
                $notification->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Data updated successfully!!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function customerSupplierTransactionDetails(Request $request) {
        $data = [];

        if ($request->customer_id) {
            $data['customer'] = $customer = Customer::where('id', $request->customer_id)
                ->where('user_id', auth()->user()->user_id)
                ->with([
                    'orders.orderDetails.product',
                    'incomeExpenses.income',
                    'returnOrders.orderDetails.product',
                ])
                ->first();

            $total_sale = 0;
            $total_due  = 0;

            foreach ($customer->orders as $order) {
                $total_sale += $order->total;

                if ($order->balance > 0) {
                    $total_due += $order->balance;
                }

            }

            $total_return_sale = 0;
            $total_return_due  = 0;

            foreach ($customer->returnOrders as $return) {
                $total_return_sale += $return->total;

                if ($return->balance > 0) {
                    $total_return_due += $return->balance;
                }

            }

            $data['total_sale']     = $total_sale - $total_return_sale;
            $data['total_due']      = $total_due - $total_return_due + $customer->amount;
            $data['total_receives'] = $total_sale - $total_return_sale + $total_due;

        } elseif ($request->supplier_id) {
            $data['supplier'] = $supplier = Supplier::where('id', $request->supplier_id)
                ->where('user_id', auth()->user()->user_id)
                ->with([
                    'incomeExpenses.expense',
                    'purchase.purchaseDetails.product',
                    'returnPurchase.returnPurchaseDetails.product',
                ])
                ->first();

            $total_sale = 0;
            $total_due  = 0;

            if ($supplier->purchase) {

                foreach ($supplier->purchase as $purchase) {
                    $total_sale += $purchase->total;

                    if ($purchase->balance < 0) {
                        $total_due += $purchase->balance;
                    }

                }

            }

            $total_return_sale = 0;
            $total_return_due  = 0;

            foreach ($supplier->returnPurchase as $return) {
                $total_return_sale += $return->total;

                if ($return->balance < 0) {
                    $total_return_due += $return->balance;
                }

            }

            $data['total_sale']     = $total_sale - $total_return_sale;
            $data['total_due']      = $total_due + $total_return_due + $supplier->previous_due;
            $data['total_receives'] = $total_return_sale + $total_due;
        }

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function customerSupplierDueList(Request $request) {
        $data              = [];
        $data['customers'] = IncomeExpense::where('user_id', auth()->user()->user_id)
            ->whereNotNull('customer_id')
            ->where('income_id', 12)
            ->with('customer', 'income', 'paymentType')
            ->paginate(100);
        $data['supplier'] = IncomeExpense::where('user_id', auth()->user()->user_id)
            ->whereNotNull('supplier_id')
            ->where('expense_id', 26)
            ->with('supplier', 'expense', 'paymentType')
            ->paginate(100);

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function customerDueReport(Request $request) {
        $data = [];

        $customer = Customer::where('user_id', auth()->user()->user_id)
            ->whereDate('modify_date', '>=', $request->start_date)
            ->whereDate('modify_date', '<=', $request->end_date)
            ->where('amount', '>', 0);

        if ($request->customer_id) {
            $customer = $customer->where('id', $request->customer_id);
        }

        if ($request->group_id) {
            $customer = $customer->where('group_id', $request->group_id);
        }

        $customer = $customer->with('user');

        $customer = $customer->paginate();

        $data['customers'] = $customer;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function supplierDueReport(Request $request) {
        $data = [];

        $supplier = Supplier::where('user_id', auth()->user()->user_id)
            ->whereDate('modify_date', '>=', $request->start_date)
            ->whereDate('modify_date', '<=', $request->end_date)
            ->where('amount', '>', 0);

        if ($request->supplier_id) {
            $supplier = $supplier->where('id', $request->supplier_id);
        }

        if ($request->group_id) {
            $supplier = $supplier->where('group_id', $request->group_id);
        }

        $supplier = $supplier->with('user');

        $supplier = $supplier->paginate();

        $data['suppliers'] = $supplier;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function showIncomeExpenseById($id) {
        $data = IncomeExpense::where('user_id', auth()->user()->user_id)
            ->where('id', $id)
            ->with(
                'businessAccount',
                'income',
                'expense',
                'customer',
                'paymentType'
            )
            ->first();

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function incomeExpenceReport(Request $request) {
        $data = IncomeExpense::where('user_id', auth()->user()->user_id);

        if ($request->type == 1) {
            $data = $data->where('accepted_as', 1)->whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date);

            if ($request->income_id) {
                $data = $data->where('income_id', $request->income_id);
            }

            if ($request->user_id) {
                $data = $data->where('user_id', $request->user_id);
            }

            $data = $data->with(
                'businessAccount',
                'income',
                'expense',
                'customer',
                'paymentType'
            )->get();

        } elseif ($request->type == 2) {
            $data = $data->where('accepted_as', 2)->whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date);

            if ($request->expense_id) {
                $data = $data->where('expense_id', $request->expense_id);
            }

            if ($request->user_id) {
                $data = $data->where('user_id', $request->user_id);
            }

            $data = $data->with(
                'businessAccount',
                'income',
                'expense',
                'supplier',
                'paymentType'
            )->get();

        }

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function latestIncomeExpenseInvoiceNumber() {
        $invoice = IncomeExpense::where('user_id', auth()->user()->user_id)
            ->select(['user_id', 'invoice_no'])
            ->orderBy('id', 'desc')
            ->first();

        $latest_invoice = 1;

        if ($invoice) {
            $latest_invoice = $invoice->invoice_no + 1;

            return response()->json([
                'status'  => true,
                'message' => 'Latest invoice number.',
                'invoice' => $latest_invoice,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'Latest invoice number.',
                'invoice' => $latest_invoice,
            ]);
        }

    }

}
