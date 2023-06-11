<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\IncomeExpense;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnOrder;
use App\Models\ReturnPurchase;
use App\Models\SendToOrderList;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller {
    public function purchaseSearch(Request $request) {
        $search = $request->search;

        $data = Purchase::where('purchases.user_id', auth()->user()->user_id)
            ->Join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select('purchases.*');

        if (is_numeric($search)) {
            $data = $data->where('purchases.invoice_no', 'LIKE', '%' . $search . '%');

        } else {
            $data = $data->where('suppliers.name', 'LIKE', '%' . $search . '%');
        }

        $data = $data->with('purchaseDetails.product', 'businessAccount', 'supplier', 'paymentType')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function orderList(Request $request) {
        $search = $request->search;
        $data   = SendToOrderList::where('user_id', auth()->user()->user_id)->where('name', 'LIKE', '%' . $search . '%')->with('product')->get();

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function itemVatList(Request $request) {
        $search = $request->search;
        $data   = Product::where('user_id', auth()->user()->user_id)->where('retail_vat', '>', 0)->where('name', 'LIKE', '%' . $search . '%')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function salesList(Request $request) {
        $search = $request->search;
        $data   = Order::Join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.user_id', auth()->user()->user_id)
            ->select('orders.*', 'customers.name as customer_name');

        if (is_numeric($search)) {
            $data = $data->where('orders.invoice_no', 'LIKE', '%' . $search . '%');

        } else {
            $data = $data->where('customers.name', 'LIKE', '%' . $search . '%');
        }

        $data = $data->with('orderDetails.product', 'businessAccount', 'customer')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfullyqq',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function stockList(Request $request) {
        $search = $request->search;
        $data   = Product::where('user_id', auth()->user()->user_id)->where('name', 'LIKE', '%' . $search . '%')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function lowStockAlertLis(Request $request) {
        $search = $request->search;
        $data   = Product::where('user_id', auth()->user()->user_id)
            ->whereNotNull('stock_alert')
            ->where('quantity', '<=', DB::raw('stock_alert'))
            ->where('name', 'LIKE', '%' . $search . '%')
            ->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function customerReturn(Request $request) {
        $search = $request->search;
        $data   = ReturnOrder::where('return_orders.user_id', auth()->user()->user_id)
            ->Join('customers', 'return_orders.customer_id', '=', 'customers.id')
            ->select('return_orders.*');

        if (is_numeric($search)) {
            $data = $data->where('return_orders.invoice_no', 'LIKE', '%' . $search . '%');

        } else {
            $data = $data->where('customers.name', 'LIKE', '%' . $search . '%');
        }

        $data = $data->with('orderDetails.product', 'businessAccount', 'customer', 'paymentType')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function supplierReturn(Request $request) {
        $search = $request->search;
        $data   = ReturnPurchase::where('return_purchases.user_id', auth()->user()->user_id)
            ->Join('suppliers', 'return_purchases.supplier_id', '=', 'suppliers.id')
            ->select('return_purchases.*');

        if (is_numeric($search)) {
            $data = $data->where('return_purchases.invoice_no', 'LIKE', '%' . $search . '%');

        } else {
            $data = $data->where('suppliers.name', 'LIKE', '%' . $search . '%');
        }

        $data = $data->with('returnPurchaseDetails.product', 'businessAccount', 'supplier', 'paymentType')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function customerDuePaymentList(Request $request) {
        $search = $request->search;

        $data = IncomeExpense::where('income_expenses.user_id', auth()->user()->user_id)
            ->where('income_expenses.accepted_as', 1)
            ->join('customers', 'income_expenses.customer_id', 'customers.id');

        if (is_numeric($search)) {
            $data = $data->where('income_expenses.invoice_no', 'LIKE', '%' . $search . '%');

        } else {
            $data = $data->where('customers.name', 'LIKE', '%' . $search . '%');
        }

        $data = $data->with('income', 'paymentType', 'customer')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfullyss',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function supplierDuePaymentList(Request $request) {
        $search = $request->search;
        $data   = IncomeExpense::where('income_expenses.user_id', auth()->user()->user_id)
            ->where('income_expenses.accepted_as', 2)
            ->join('suppliers', 'income_expenses.supplier_id', 'suppliers.id');

        if (is_numeric($search)) {
            $data = $data->where('income_expenses.invoice_no', 'LIKE', '%' . $search . '%');

        } else {
            $data = $data->where('suppliers.name', 'LIKE', '%' . $search . '%');
        }

        $data = $data->with('expense', 'paymentType', 'supplier')->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function balanceTransfer(Request $request) {
        $search = $request->search;
        $data   = BalanceTransfer::where('user_id', auth()->user()->user_id)
            ->where('transfer_person', 'LIKE', '%' . $search . '%')
            ->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function otherInvoiceList(Request $request) {
        $search = $request->search;
        $data   = IncomeExpense::where('income_expenses.user_id', auth()->user()->user_id)
            ->where('invoice_no', 'LIKE', '%' . $search . '%')
            ->paginate(100);

        if ($data) {
            return response()->json([
                'status'  => true,
                'count'   => count($data),
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function incomeSearch(Request $request) {
        $search = $request->search;
        $data   = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereNull('purchase_id')
            ->whereNull('expense_id')
            ->whereNull('return_order_id')
            ->orderBy('id', 'desc')
            ->select('id', 'user_id', 'order_id', 'return_purchase_id', 'income_id', 'created_at', 'updated_at')
            ->with([
                'order.orderDetails.product',
                'order.paymentType',
                'order.customer'          => function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                },
                'businessAccount',
                'income_fc.customer'      => function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                },
                'income_fc'               => function ($query) {
                    return $query->where('income_id', '!=', 12);
                },
                'income_fc.income',
                'income_fc.paymentType',
                'returnPurchase.supplier' => function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                },
                'returnPurchase.returnPurchaseDetails.product',
            ])
            ->get();
        $filtered_data = [];

        foreach ($data as $item) {

            if ($item->order_id && $item->order->customer) {
                $filtered_data[] = $item;
            } else

            if ($item->return_purchase_id && $item->returnPurchase->supplier) {
                $filtered_data[] = $item;
            } else

            if ($item->income_id && $item->income_fc && $item->income_fc->customer) {
                $filtered_data[] = $item;
            }

        }

        if ($filtered_data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $filtered_data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $filtered_data,
            ]);
        }

    }

    public function expenseSearch(Request $request) {
        $search = $request->search;
        $data   = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereNull('order_id')
            ->whereNull('income_id')
            ->whereNull('return_purchase_id')
            ->orderBy('id', 'desc')
            ->select('id', 'user_id', 'purchase_id', 'return_order_id', 'expense_id', 'created_at', 'updated_at')
            ->with([
                'purchase.purchaseDetails.product',
                'purchase.paymentType',
                'purchase.supplier'    => function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                },
                'businessAccount',
                'expense_ts.supplier'  => function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                },
                'expense_ts'           => function ($query) {
                    return $query->where('expense_id', '!=', 26);
                },
                'expense_ts.expense',
                'expense_ts.paymentType',
                'returnOrder.orderDetails.product',
                'returnOrder.customer' => function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                },

            ])->get();

        $filtered_data = [];

        foreach ($data as $item) {

            if ($item->purchase_id && $item->purchase->supplier) {
                $filtered_data[] = $item;
            } else

            if ($item->return_order_id && $item->returnOrder->customer) {
                $filtered_data[] = $item;
            } else

            if ($item->expense_id && $item->expense_ts && $item->expense_ts->supplier) {
                $filtered_data[] = $item;
            }

        }

        if ($filtered_data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $filtered_data,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
                'data'    => $filtered_data,
            ]);
        }

    }

}
