<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FavoriteOrder;
use App\Models\FavoriteOrderDetails;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteOrderController extends Controller {
    public function list() {
        $data = FavoriteOrder::where('user_id', auth()->user()->user_id)->with('customer', 'favoriteOrderDetails.product')->get();

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

    public function store(Request $request) {
        DB::beginTransaction();

        try {

            $order                       = new FavoriteOrder();
            $order->user_id              = auth()->user()->user_id;
            $order->sales_man            = auth()->user()->name;
            $order->customer_id          = $request->customer_id;
            $order->order_type           = $request->order_type;
            $order->invoice_date         = $request->invoice_date;
            $order->discount             = $request->discount;
            $order->discount_price       = $request->discount_price;
            $order->vat_price            = $request->vat_price;
            $order->total                = $request->total; //order total
            $order->payment_type_id      = $request->payment_type_id;
            $order->received_amount      = $request->received_amount;
            $order->balance              = $request->balance;
            $order->note                 = $request->note;
            $order->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Favorite order added by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            foreach ($request->cart as $cart) {
                $product = Product::find($cart['product_id']);

                if ($product->warrenty) {
                    $warrenty = date("Y-m-d", strtotime('+' . $product->duration . ' days'));
                }

                FavoriteOrderDetails::create([
                    'favorite_order_id' => $order->id,
                    'product_id'        => $cart['product_id'],
                    'name'              => $cart['name'],
                    'type'              => $cart['type'],
                    'quantity'          => $cart['quantity'],
                    'unit_price'        => $cart['unit_price'],
                    'buying_price'      => $cart['buying_price'],
                    'vat'               => $product->vat,
                    'vat_price'         => $product->vat_price,
                    'warrenty'          => $warrenty ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order added to favorite list!!',
            ]);
        } catch (\Throwable$th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function details(Request $request) {
        $data = FavoriteOrder::where('id', $request->id)->with('customer', 'favoriteOrderDetails.product')->first();

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

    public function delete($id) {
        $favorite_order = FavoriteOrder::where('id', $id)->with('favoriteOrderDetails')->first();

        foreach ($favorite_order->favoriteOrderDetails as $order) {
            $order->delete();
        }

        $favorite_order->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Favorite order deleted successfully!!',
        ]);
    }

}
