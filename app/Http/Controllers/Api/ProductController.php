<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DueAlert;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller {
    public function list(Request $request) {
        $data = [];

        $data['products'] = $product = Product::where('user_id', auth()->user()->user_id)->where('type', 'product')->where('quantity', '>', 0)->get();

        $total_product          = 0;
        $product_purchase_value = 0;

        foreach ($product as $item) {
            $product_purchase_value += ($item->quantity * $item->buying_price);
            $total_product += $item->quantity;
        }

        $data['total_product_purchase_price'] = $product_purchase_value;
        $data['total_product']                = $total_product;

        $data['services']      = $service      = Product::where('user_id', auth()->user()->user_id)->where('type', 'service')->get();
        $data['total_service'] = Product::where('user_id', auth()->user()->user_id)->where('type', 'service')->count();

        $service_purchase_value = 0;

        foreach ($service as $item) {
            $service_purchase_value += ($item->quantity * $item->buying_price);
        }

        $data['total_service_purchase_price'] = $service_purchase_value;

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function store(Request $request) {
        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/product/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            $product                           = new Product();
            $product->user_id                  = auth()->user()->user_id;
            $product->name                     = $request->name;
            $product->type                     = $request->type;
            $product->buying_price             = $request->buying_price;
            $product->retail_price             = $request->retail_price;
            $product->retail_discount          = $request->retail_discount;
            $product->retail_discount_price    = $request->retail_discount_price;
            $product->retail_vat               = $request->retail_vat;
            $product->retail_vat_price         = $request->retail_vat_price;
            $product->wholesale_price          = $request->wholesale_price;
            $product->wholesale_discount       = $request->wholesale_discount;
            $product->wholesale_discount_price = $request->wholesale_discount_price;
            $product->wholesale_vat            = $request->wholesale_vat;
            $product->wholesale_vat_price      = $request->wholesale_vat_price;
            $product->wholesale_min_quantity   = $request->wholesale_min_quantity;
            $product->quantity                 = $request->quantity;
            $product->stock_alert              = $request->stock_alert;
            $product->warrenty_duration        = $request->warrenty_duration;
            $product->warrenty_type            = $request->warrenty_type;
            $product->expire                   = $request->expire;
            $product->unit                     = $request->unit;
            $product->image                    = $final_name1 ?? null;
            $product->save();

            $product->barcode = $product->id;
            $product->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'New <b>' . $product->type . '</b> name <b>' . $product->name . '</b> added by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => $request->type . ' added successfully!!',
                'data'    => $product,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function details(Request $request) {
        $data            = [];
        $data['product'] = Product::where('id', $request->id)
            ->where('user_id', auth()->user()->user_id)
            ->with('purchaseDetails.purchase.returnPurchase.returnPurchaseDetails', 'purchaseDetails.purchase.supplier')
            ->first();
        // $data['purchase'] = PurchaseDetails::where('product_id', $request->id)->with('purchase', 'product')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function barcodeDetails(Request $request) {
        $data            = [];
        $data['product'] = $product = Product::where('user_id', auth()->user()->user_id)
            ->where('barcode', $request->barcode)
            ->with('purchaseDetails')
            ->first();
        // $data['purchase'] = PurchaseDetails::where('product_id', $product->id)->with('purchase', 'product')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function update(Request $request) {
        DB::beginTransaction();

        try {

            $product = Product::where('id', $request->id)->where('user_id', auth()->user()->user_id)->first();

            $product->user_id                  = auth()->user()->user_id;
            $product->name                     = $request->name;
            $product->type                     = $request->type;
            $product->buying_price             = $request->buying_price;
            $product->retail_price             = $request->retail_price;
            $product->retail_discount          = $request->retail_discount;
            $product->retail_discount_price    = $request->retail_discount_price;
            $product->retail_vat               = $request->retail_vat;
            $product->retail_vat_price         = $request->retail_vat_price;
            $product->wholesale_price          = $request->wholesale_price;
            $product->wholesale_discount       = $request->wholesale_discount;
            $product->wholesale_discount_price = $request->wholesale_discount_price;
            $product->wholesale_vat            = $request->wholesale_vat;
            $product->wholesale_vat_price      = $request->wholesale_vat_price;
            $product->wholesale_min_quantity   = $request->wholesale_min_quantity;
            $product->quantity                 = $request->quantity;
            $product->stock_alert              = $request->stock_alert;
            $product->warrenty_duration        = $request->warrenty_duration;
            $product->warrenty_type            = $request->warrenty_type;
            $product->expire                   = $request->expire;
            $product->unit                     = $request->unit;
            $product->save();

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $image_path = public_path($product->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/product/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $product->image = $final_name1;
                    $product->save();
                }

            }

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'New <b>' . $product->type . '</b> name <b>' . $product->name . '</b> updated by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => $request->type . ' updated successfully!!',
                'data'    => $product,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function editVat(Request $request) {
        $product             = Product::findOrFail($request->product_id);
        $product->retail_vat = $request->retail_vat;
        $product->save();

        return response()->json([
            'status'  => true,
            'message' => 'data',
            'data'    => $product,
        ]);

    }

    public function deleteVat($id) {
        $product             = Product::findOrFail($id);
        $product->retail_vat = null;
        $product->save();

        return response()->json([
            'status'  => true,
            'message' => 'data',
            'data'    => $product,
        ]);

    }

    public function lowStockQuantityAlert() {
        $product = Product::where('user_id', auth()->user()->user_id)
            ->whereNotNull('stock_alert')
            ->where('quantity', '<=', DB::raw('stock_alert'))
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'data',
            'data'    => $product,
        ]);
    }

    public function deleteLowStockQuantityAlert(Request $request) {
        $product = Product::where('id', $request->product_id)->where('user_id', auth()->user()->user_id)->first();

        if ($product) {
            $product->stock_alert = null;
            $product->save();

            return response()->json([
                'status'  => true,
                'message' => 'Stock alert deleted successfully',
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'Nothing found',
            ]);
        }

    }

    public function dueAlert() {
        $data = DueAlert::where('user_id', auth()->user()->user_id)->with('customer')->orderBy('id', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'data',
            'data'    => $data,
        ]);
    }

    public function storeDueAlert(Request $request) {
        DueAlert::create([
            'user_id'     => auth()->user()->user_id,
            'customer_id' => $request->customer_id,
            'alert_type'  => $request->alert_type,
            'message'     => $request->message,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'data',
        ]);
    }

    public function deleteDueAlert($id) {
        $data = DueAlert::find($id);
        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'data',
        ]);
    }

}
