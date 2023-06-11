<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Country;
use App\Models\GroupInfo;
use App\Models\IncomeExpense;
use App\Models\Order;
use App\Models\PaymentType;
use App\Models\PoliceStation;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\State;
use Illuminate\Http\Request;

class GeneralController extends Controller {

    public function availableBalance($id) {

        $data             = 0;
        $balance_transfer = PaymentType::whereNull('user_id')
            ->orWhere('user_id', auth()->user()->user_id)
            ->where('id', $id)
            ->with([
                'order',
                'income',
                'purchase',
                'expense',
                'receiverBalanceAccept',
                'senderBalanceTransfer',
                'returnOrder',
                'returnPurchase',
                'previousCashinHand',
            ])
            ->get();

        foreach ($balance_transfer as $item) {

            foreach ($item->order as $order) {
                $data += $order->received_amount;
            }

            foreach ($item->income as $income) {

                $data += $income->amount;

            }

            foreach ($item->purchase as $purchase) {
                $data -= $purchase->payment_amount;
            }

            foreach ($item->expense as $expense) {

                $data -= $expense->amount;

            }

            foreach ($item->returnOrder as $return_order) {
                $data -= $return_order->received_amount;
            }

            foreach ($item->returnPurchase as $return_purchase) {
                $data += $return_purchase->payment_amount;
            }

            foreach ($item->previousCashinHand as $sbt) {
                $data += $sbt->value;
            }

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

    public function paymentType() {
        $data = PaymentType::whereNull('user_id')->orWhere('user_id', auth()->user()->user_id)->get();

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

    public function groupList() {
        $data = GroupInfo::where('user_id', auth()->user()->user_id)->orderBy('id', 'desc')->get();

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

    public function storeGroup(Request $request) {
        $data = GroupInfo::create([
            'user_id' => auth()->user()->user_id,
            'name'    => $request->name,
        ]);

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Group created successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'Something went wrong',
                'data'    => $data,
            ]);
        }

    }

    public function activeRole() {
        $data = Role::where('status', 1)->orderBy('name', 'asc')->get();

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

    public function activeCountry() {
        $data = Country::where('status', 1)->orderBy('en_name', 'asc')->get();

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

    public function activeState($country_id) {
        $data = State::where('status', 1)->where('country_id', $country_id)->orderBy('en_name', 'asc')->get();

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

    public function activePoliceStation($country_id, $state_id) {
        $data = PoliceStation::where('status', 1)->where('country_id', $country_id)->where('state_id', $state_id)->orderBy('en_name', 'asc')->get();

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

    public function salesManList() {
        $data = Order::where('user_id', auth()->user()->user_id)->distinct('sales_man')->select('sales_man')->orderBy('sales_man', 'asc')->get();

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

    public function purchaseManList() {
        $data = Purchase::where('user_id', auth()->user()->user_id)->distinct('purchase_man')->select('purchase_man')->orderBy('purchase_man', 'asc')->get();

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

    public function stockReport(Request $request) {

        $data = [];

        $purchase = Purchase::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date);

        if ($request->product_id) {
            $purchase = $purchase->with(['purchaseDetails' => function ($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            },

            ]);
        }

        if ($request->supplier_id) {
            $purchase = $purchase->where('supplier_id', $request->supplier_id);
        }

        if ($request->purchase_man) {
            $purchase = $purchase->where('purchase_man', $request->purchase_man);
        }

        $data['products'] = $purchase = $purchase->with(
            'purchaseDetails.product',
            'supplier',
            'businessAccount')->get();
        $total_product        = 0;
        $total_purchase_price = 0;

        foreach ($purchase as $item) {

            if ($item->purchaseDetails->count() > 0) {

                $total_purchase_price += $item->total;
            }

            foreach ($item->purchaseDetails as $details) {
                $total_product += $details->quantity;
            }

        }

        $data['total_product']        = $total_product;
        $data['total_purchase_price'] = $total_purchase_price;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);

    }

    public function customerLastDueInvoice($customer_id) {
        $data = Order::where('user_id', auth()->user()->user_id)->where('customer_id', $customer_id)->where('balance', '>', 0)->with('orderDetails.product', 'customer', 'businessAccount')->orderBy('id', 'desc')->first();

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

    public function supplierLastDueInvoice($supplier_id) {
        $data = Purchase::where('user_id', auth()->user()->user_id)->where('supplier_id', $supplier_id)->where('balance', '>', 0)->with('purchaseDetails.product', 'supplier', 'businessAccount')->orderBy('id', 'desc')->first();

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

    public function orderInvoice() {
        $data = Order::where('user_id', auth()->user()->user_id)->orderBy('id', 'desc')->with('orderDetails.product', 'customer')->paginate();

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

    public function supplierInvoice() {
        $data = Purchase::where('user_id', auth()->user()->user_id)->orderBy('id', 'desc')->with('purchaseDetails.product', 'supplier')->paginate();

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

    public function otherInvoice() {
        $data = IncomeExpense::where('user_id', auth()->user()->user_id)->with(
            'businessAccount',
            'income',
            'expense',
            'customer',
            'paymentType',
            'supplier'
        )
            ->paginate();

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

    public function businessType() {
        $data = BusinessType::all();

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

    public function checkCashInHandById($payment_type_id) {
        $balance_transfer = PaymentType::where('id', $payment_type_id)->whereNull('user_id')
            ->orWhere('user_id', auth()->user()->user_id)
            ->with([
                'order',
                'income',
                'purchase',
                'expense',
                'returnOrder',
                'returnPurchase',
                'previousCashinHand',
            ])
            ->first();

        $amount = 0;

        foreach ($balance_transfer->order as $balance) {
            $amount += $balance->received_amount;
        }

        foreach ($balance_transfer->income as $balance) {
            $amount += $balance->amount;
        }

        foreach ($balance_transfer->purchase as $balance) {
            $amount -= $balance->payment_amount;
        }

        foreach ($balance_transfer->expense as $balance) {
            $amount -= $balance->amount;
        }

        foreach ($balance_transfer->returnOrder as $balance) {
            $amount -= $balance->received_amount;
        }

        foreach ($balance_transfer->returnPurchase as $balance) {
            $amount += $balance->payment_amount;
        }

        foreach ($balance_transfer->previousCashinHand as $balance) {
            $amount += $balance->value;
        }

        if ($balance_transfer) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $amount,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $amount,
            ]);
        }

    }

}
