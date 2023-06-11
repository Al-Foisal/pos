<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ReturnOrder;
use App\Models\ReturnOrderDetails;
use App\Models\ReturnPurchase;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller {
    public function returnInvoice($invoice_no) {
        $data = ReturnOrder::where('invoice_no', $invoice_no)->where('user_id', auth()->user()->user_id)->with('orderDetails.product', 'businessAccount', 'customer')->first();

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

    public function returnList() {
        $data                    = [];
        $data['return_order']    = ReturnOrder::where('user_id', auth()->user()->user_id)->with('orderDetails.product', 'businessAccount', 'customer', 'order.orderDetails.product')->orderBy('id', 'desc')->paginate();
        $data['customer_return'] = ReturnOrder::where('user_id', auth()->user()->user_id)->sum('total');
        $data['supplier_return'] = ReturnPurchase::where('user_id', auth()->user()->user_id)->sum('total');

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);

    }

    public function customerwiseSalelist() {
        $data = Order::where('user_id', auth()->user()->user_id)
            ->selectRaw('sum(total) as total_sale, customer_id')
            ->groupBy('customer_id')
            ->with('customer')
            ->get();

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

    public function itemwiseSalelist() {
        $data               = [];
        $data['order_list'] = Order::where('user_id', auth()->user()->user_id)
            ->with('orderDetails.product', 'customer')
            ->orderBy('id', 'desc')
            ->paginate();
        $data['total_sale'] = Order::where('user_id', auth()->user()->user_id)->sum('total');
        $total_quantity     = 0;
        $selling_product    = Order::where('user_id', auth()->user()->user_id)->get();

        foreach ($selling_product as $product) {

            foreach ($product->orderDetails as $details) {
                $total_quantity += $details->quantity;
            }

        }

        $data['total_product'] = $total_quantity;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfullys',
            'data'    => $data,
        ]);

    }

    public function store(Request $request) {
        DB::beginTransaction();

        try {

            foreach ($request->cart as $cart) {
                $product = Product::where('id', $cart['product_id'])->select(['id', 'name', 'quantity'])->first();

                if ($product->quantity < $cart["quantity"]) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Insufficient quantity',
                        'product' => $product,
                    ]);
                }

            }

            $order_invoice = Order::where('user_id', auth()->user()->user_id)->select('invoice_no')->orderBy('id', 'desc')->first();

            if ($order_invoice) {
                $in_no = $order_invoice->invoice_no + 1;
            } else {
                $in_no = 1;
            }

            $order                  = new Order();
            $order->user_id         = auth()->user()->user_id;
            $order->sales_man       = auth()->user()->name;
            $order->customer_id     = $request->customer_id;
            $order->order_type      = $request->order_type;
            $order->invoice_no      = $in_no;
            $order->invoice_date    = $request->invoice_date;
            $order->discount        = $request->discount;
            $order->discount_price  = $request->discount_price;
            $order->vat_price       = $request->vat_price;
            $order->total           = $request->total; //order total
            $order->payment_type_id = $request->payment_type_id;
            $order->received_amount = $request->received_amount;
            $order->balance         = $request->balance;
            $order->note            = $request->note;
            $order->save();

            $order_customer = Customer::find($request->customer_id);
            $customer_name  = $order_customer->name ?? 'N/A';

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Order placed to <b>' . $customer_name . '</b>';
            $notification->save();

            //storing transaction history
            $th              = new TransactionHistory();
            $th->user_id     = auth()->user()->user_id;
            $th->order_id    = $order->id;
            $th->customer_id = $order->customer_id;
            $th->save();

            foreach ($request->cart as $cart) {
                $product = Product::find($cart['product_id']);

                OrderDetails::create([
                    'order_id'       => $order->id,
                    'product_id'     => $cart['product_id'],
                    'type'           => $cart['type'],
                    'name'           => $cart['name'],
                    'quantity'       => $cart['quantity'],
                    'unit_price'     => $cart['unit_price'],
                    'buying_price'   => $cart['buying_price'],
                    'vat'            => $cart['vat'],
                    'vat_price'      => $cart['vat_price'],
                    'warrenty'       => $cart['warrenty'],
                    'discount'       => $cart['discount'],
                    'discount_price' => $cart['discount_price'],
                    'is_retail'      => $cart['is_retail'],
                ]);

                //update product quantity
                $product_quantity  = $product->quantity - $cart['quantity'];
                $product->quantity = $product_quantity > 0 ? $product_quantity : 0;
                $product->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order placed successfully!!',
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

            $order = Order::where('id', $request->id)->with('orderDetails')->first();

            /**
             *update product quantity as previous and deleting
             */

            foreach ($order->orderDetails as $cart) {
                $product           = Product::find($cart->product_id);
                $product->quantity = $product->quantity + $cart->quantity;
                $product->save();
                $cart->delete();
            }

            foreach ($request->cart as $cart) {
                $product = Product::where('id', $cart['product_id'])->select(['id', 'name', 'quantity'])->first();

                if ($product->quantity < $cart["quantity"]) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Insufficient quantity',
                        'product' => $product,
                    ]);
                }

            }

            $order->user_id         = auth()->user()->user_id;
            $order->sales_man       = auth()->user()->name;
            $order->customer_id     = $request->customer_id;
            $order->order_type      = $request->order_type;
            $order->invoice_date    = $request->invoice_date;
            $order->discount        = $request->discount;
            $order->discount_price  = $request->discount_price;
            $order->vat_price       = $request->vat_price;
            $order->total           = $request->total; //order total
            $order->payment_type_id = $request->payment_type_id;
            $order->received_amount = $request->received_amount;
            $order->balance         = $request->balance;
            $order->note            = $request->note;
            $order->save();

            foreach ($request->cart as $cart) {
                $product = Product::find($cart['product_id']);

                OrderDetails::create([
                    'order_id'       => $order->id,
                    'product_id'     => $cart['product_id'],
                    'type'           => $cart['type'],
                    'name'           => $cart['name'],
                    'quantity'       => $cart['quantity'],
                    'unit_price'     => $cart['unit_price'],
                    'buying_price'   => $cart['buying_price'],
                    'vat'            => $cart['vat'],
                    'vat_price'      => $cart['vat_price'],
                    'warrenty'       => $cart['warrenty'],
                    'discount'       => $cart['discount'],
                    'discount_price' => $cart['discount_price'],
                    'is_retail'      => $cart['is_retail'],
                ]);

                //update product quantity
                $product_quantity  = $product->quantity - $cart['quantity'];
                $product->quantity = $product_quantity > 0 ? $product_quantity : 0;
                $product->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order updated successfully!!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function returnOrder(Request $request) {

        // return json_decode($request->cart);
        DB::beginTransaction();

        try {
            $carts = json_decode($request->cart);

            foreach ($carts as $cart) {
                $order_product = OrderDetails::where('order_id', $request->order_id)->where('product_id', $cart->product_id)->first();

                if (!$order_product) {
                    return response()->json([
                        'status'   => false,
                        'maessage' => 'Invalid product for this order invoice.',
                    ]);
                }

            }

            $return_order = ReturnOrder::where('user_id', auth()->user()->user_id)->select('invoice_no')->orderBy('id', 'desc')->first();

            if ($return_order) {
                $in_no = $return_order->invoice_no + 1;
            } else {
                $in_no = 1;
            }

            $order                  = new ReturnOrder();
            $order->user_id         = auth()->user()->user_id;
            $order->sales_man       = auth()->user()->name;
            $order->customer_id     = $request->customer_id;
            $order->order_id        = $request->order_id;
            $order->order_type      = $request->order_type;
            $order->invoice_no      = $in_no;
            $order->invoice_date    = $request->invoice_date;
            $order->discount        = $request->discount;
            $order->discount_price  = $request->discount_price;
            $order->vat_price       = $request->vat_price;
            $order->total           = $request->total; //order total
            $order->payment_type_id = $request->payment_type_id;
            $order->received_amount = $request->received_amount;
            $order->balance         = $request->balance;
            $order->note            = $request->note;
            $order->save();

            $main_order            = Order::find($request->order_id);
            $main_order->is_return = 1;
            $main_order->save();
            //storing transaction history
            $th                  = new TransactionHistory();
            $th->user_id         = auth()->user()->user_id;
            $th->return_order_id = $order->id;
            $th->customer_id     = $order->customer_id;
            $th->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Order return to ' . $order->invoice_no . '';
            $notification->save();

            foreach ($carts as $cart) {
                $product = Product::find($cart->product_id);

                ReturnOrderDetails::create([
                    'return_order_id' => $order->id,
                    'product_id'      => $cart->product_id,
                    'type'            => $cart->type,
                    'name'            => $cart->name,
                    'quantity'        => $cart->quantity,
                    'unit_price'      => $cart->unit_price,
                    'buying_price'    => $cart->buying_price,
                    'vat'             => $cart->vat,
                    'vat_price'       => $cart->vat_price,
                    'warrenty'        => $cart->warrenty,
                ]);

                //update product quantity
                $product_quantity  = $product->quantity + $cart->quantity;
                $product->quantity = $product_quantity;
                $product->save();

            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order return successfully!!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function updateReturnOrder(Request $request) {
        DB::beginTransaction();

        try {

            $carts = json_decode($request->cart);
            $order = ReturnOrder::where('id', $request->id)->where('user_id', auth()->user()->user_id)->with('orderDetails')->first();

            foreach ($carts as $cart) {

                $return_order_details = ReturnOrderDetails::where('return_order_id', $order->id)->where('product_id', $cart->product_id)->first();

                if (!$return_order_details) {
                    $customer_product = OrderDetails::where('order_id', $request->order_id)->where('product_id', $cart->product_id)->first();

                    if (!$customer_product) {
                        return response()->json([
                            'status'   => false,
                            'maessage' => 'Invalid product for this order invoice.',
                        ]);
                    }

                }

            }

            if ($order) {

                foreach ($order->orderDetails as $cart) {
                    $product = Product::find($cart->product_id);

                    if ($product) {
                        //update product quantity
                        $product_quantity  = $product->quantity - $cart->quantity;
                        $product->quantity = $product_quantity > 0 ? $product_quantity : 0;
                        $product->save();
                    }

                    $cart->delete();

                }

            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Somethis went wrong',
                ]);
            }

            $order->user_id         = auth()->user()->user_id;
            $order->sales_man       = auth()->user()->name;
            $order->customer_id     = $request->customer_id;
            $order->order_id        = $request->order_id;
            $order->order_type      = $request->order_type;
            $order->invoice_date    = $request->invoice_date;
            $order->discount        = $request->discount;
            $order->discount_price  = $request->discount_price;
            $order->vat_price       = $request->vat_price;
            $order->total           = $request->total; //order total
            $order->payment_type_id = $request->payment_type_id;
            $order->received_amount = $request->received_amount;
            $order->balance         = $request->balance;
            $order->note            = $request->note;
            $order->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Order invoice #' . $order->invoice_no . ' returned';
            $notification->save();

            foreach ($carts as $cart) {
                $product = Product::find($cart->product_id);

                ReturnOrderDetails::create([
                    'return_order_id' => $order->id,
                    'product_id'      => $cart->product_id,
                    'name'            => $cart->name,
                    'type'            => $cart->type,
                    'quantity'        => $cart->quantity,
                    'unit_price'      => $cart->unit_price,
                    'buying_price'    => $cart->buying_price,
                    'vat'             => $cart->vat,
                    'vat_price'       => $cart->vat_price,
                    'warrenty'        => $cart->warrenty,
                ]);

                if ($product) {
                    //update product quantity
                    $product_quantity  = $product->quantity + $cart->quantity;
                    $product->quantity = $product_quantity;
                    $product->save();

                }

            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order return successfully!!',
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([

                'status'  => false,

                'message' => $th,

            ]);
        }

    }

    public function invoice(Request $request) {
        $data          = [];
        $data['order'] = $order = Order::where('id', $request->id)->with('orderDetails', 'businessAccount', 'customer')->first();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function delete($id) {
        $order = Order::where('id', $id)->with('orderDetails')->first();

        $order_customer = Customer::find($order->customer_id);
        $customer_name  = $order_customer->name ?? 'N/A';

        $notification          = new Notification();
        $notification->user_id = auth()->user()->user_id;
        $notification->name    = 'Order deleted for <b>' . $customer_name . '</b>';
        $notification->save();

        foreach ($order->orderDetails as $item) {
            $product           = Product::find($item->product_id);
            $product->quantity = $product->quantity + $item->quantity;
            $product->save();
            $item->delete();
        }

        $transaction = TransactionHistory::where('order_id', $order->id)->first();
        $transaction->delete();
        $order->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Order deleted successfully!!',
        ]);
    }

    public function salesReport(Request $request) {
        $data = Order::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date);

        if ($request->customer_id) {
            $data = $data->where('customer_id', $request->customer_id);
        }

        if ($request->sales_man) {
            $data = $data->where('sales_man', $request->sales_man);
        }

        $data = $data->with('orderDetails.product', 'customer', 'businessAccount')
            ->orderBy('id', 'desc')
            ->get();

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

    public function getLastInvoice() {
        $data = Order::where('user_id', auth()->user()->user_id)->orderBy('id', 'desc')->first();

        $invoice_number = 1;

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data->invoice_no + 1,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $invoice_number,
            ]);
        }

    }

    public function getReturnLastInvoice() {
        $data = ReturnOrder::where('user_id', auth()->user()->user_id)->orderBy('id', 'desc')->first();

        $invoice_number = 1;

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data->invoice_no + 1,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $invoice_number,
            ]);
        }

    }

}
