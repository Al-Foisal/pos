<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\ReturnOrder;
use App\Models\ReturnPurchase;
use App\Models\ReturnPurchaseDetails;
use App\Models\SendToOrderList;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PurchaseController extends Controller {
    public function returnInvoice($invoice_no) {
        $data = ReturnPurchase::where('invoice_no', $invoice_no)->where('user_id', auth()->user()->user_id)->with('returnPurchaseDetails.product', 'businessAccount', 'supplier')->first();

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
        $data['return_order']    = ReturnPurchase::where('user_id', auth()->user()->user_id)->with('returnPurchaseDetails.product', 'businessAccount', 'supplier', 'purchase.purchaseDetails.product')->orderBy('id', 'desc')->paginate();
        $data['customer_return'] = ReturnOrder::where('user_id', auth()->user()->user_id)->sum('total');
        $data['supplier_return'] = ReturnPurchase::where('user_id', auth()->user()->user_id)->sum('total');

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);

    }

    public function supplierwisePurchaselist() {
        $data = [];

        $data['todays_purchase'] = Purchase::where('user_id', auth()->user()->user_id)->whereDate('created_at', today())->sum('total');
        $data['total_purchase']  = Purchase::where('user_id', auth()->user()->user_id)->sum('total');
        $data['purchases']       = Purchase::where('user_id', auth()->user()->user_id)
            ->selectRaw('sum(total) as total_purchase, supplier_id')
            ->groupBy('supplier_id')
            ->with('supplier')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function Purchaselist() {
        $data                      = [];
        $data['total_category']    = Product::where('user_id', auth()->user()->user_id)->where('type', 'product')->count();
        $total_item                = Purchase::where('user_id', auth()->user()->user_id)->withCount('purchaseDetails')->get();
        $total_purchase_item_count = 0;

        foreach ($total_item as $item) {
            $total_purchase_item_count += $item->purchase_details_count;
        }

        $data['total_purchase_item_count'] = $total_purchase_item_count;
        $data['todays_purchase']           = Purchase::where('user_id', auth()->user()->user_id)->whereDay('created_at', '=', today())->sum('total');
        $data['total_purchase']            = Purchase::where('user_id', auth()->user()->user_id)->sum('total');
        $data['data']                      = Purchase::where('user_id', auth()->user()->user_id)
            ->orderBy('id', 'desc')
            ->with(
                'purchaseDetails.product',
                'businessAccount',
                'supplier',
                'expense'
            )
            ->paginate();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);

    }

    public function purchasesVoucherImage(Request $request) {
        $data = Purchase::where('id', $request->id)
            ->where('user_id', auth()->user()->user_id)
            ->select(['id', 'voucher_image', 'created_at'])
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

    public function store(Request $request) {
        DB::beginTransaction();

        try {
            /**
             * invoice will generate according to business owner
             */
            $purchase_invoice = Purchase::select('user_id', 'invoice_no')
                ->where('user_id', auth()->user()->user_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($purchase_invoice) {
                $in_no = $purchase_invoice->invoice_no + 1;
            } else {
                $in_no = 1;
            }

            if ($request['voucher_image']) {

                $image_file = base64_decode($request['voucher_image']);
                $b64_image  = '/images/purchase/' . time() . '.' . 'png';
                $success    = file_put_contents(public_path() . $b64_image, $image_file);

            }

            $purchase                  = new purchase();
            $purchase->user_id         = auth()->user()->user_id;
            $purchase->purchase_man    = auth()->user()->name;
            $purchase->supplier_id     = $request->supplier_id;
            $purchase->purchase_type   = $request->purchase_type;
            $purchase->invoice_no      = $in_no;
            $purchase->invoice_date    = $request->invoice_date;
            $purchase->discount        = $request->discount;
            $purchase->discount_price  = $request->discount_price;
            $purchase->vat_price       = $request->vat_price;
            $purchase->total           = $request->total;
            $purchase->payment_type_id = $request->payment_type_id;
            $purchase->payment_amount  = $request->payment_amount;
            $purchase->balance         = $request->balance;
            $purchase->note            = $request->note;
            $purchase->voucher_image   = $b64_image ?? null;
            $purchase->voucher_number  = $request->voucher_number ?? null;
            $purchase->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Purchase invoice <b>#' . $purchase->invoice_no . '</b> placed';
            $notification->save();

            //storing transaction history

            $th              = new TransactionHistory();
            $th->user_id     = auth()->user()->user_id;
            $th->purchase_id = $purchase->id;
            $th->supplier_id = $purchase->supplier_id;
            $th->save();

            foreach ($request->cart as $cart) {

                if ($cart['product_id'] == null) {

                    if ($cart['image']) {

                        $image_file = base64_decode($cart['image']);
                        $b64_image  = '/images/product/' . time() . uniqid() . '.' . 'png';
                        $success    = file_put_contents(public_path() . $b64_image, $image_file);
                    }

                    $product                           = new Product();
                    $product->user_id                  = auth()->user()->user_id;
                    $product->name                     = $cart['name'];
                    $product->type                     = $cart['type'];
                    $product->buying_price             = $cart['buying_price'];
                    $product->retail_price             = $cart['retail_price'];
                    $product->retail_discount          = $cart['retail_discount'];
                    $product->retail_discount_price    = $cart['retail_discount_price'];
                    $product->retail_vat               = $cart['retail_vat'];
                    $product->retail_vat_price         = $cart['retail_vat_price'];
                    $product->wholesale_price          = $cart['wholesale_price'];
                    $product->wholesale_discount       = $cart['wholesale_discount'];
                    $product->wholesale_discount_price = $cart['wholesale_discount_price'];
                    $product->wholesale_vat            = $cart['wholesale_vat'];
                    $product->wholesale_vat_price      = $cart['wholesale_vat_price'];
                    $product->wholesale_min_quantity   = $cart['wholesale_min_quantity'];
                    $product->quantity                 = $cart['quantity'];
                    $product->stock_alert              = $cart['stock_alert'];
                    $product->warrenty_duration        = $cart['warrenty_duration'];
                    $product->warrenty_type            = $cart['warrenty_type'];
                    $product->expire                   = $cart['expire'];
                    $product->unit                     = $cart['unit'];
                    $product->image                    = $b64_image ?? null;
                    $product->save();

                    $product->barcode = $product->id;
                    $product->save();

                } else {

                    if ($cart['image']) {

                        $image_file = base64_decode($cart['image']);
                        $b64_image  = '/images/product/' . time() . uniqid() . '.' . 'png';
                        $success    = file_put_contents(public_path() . $b64_image, $image_file);
                    }

                    $product = Product::find($cart['product_id']);

                    $average_price    = 0;
                    $total            = $cart['buying_price'] * $cart['quantity'];
                    $quantity         = $cart['quantity'];
                    $purchase_details = PurchaseDetails::where('product_id', $product->id)->get();

                    if ($purchase_details) {

                        foreach ($purchase_details as $details) {
                            $total += ($details->quantity * $details->buying_price);
                            $quantity += $details->quantity;
                        }

                    }

                    $average_price = $total / $quantity;

                    $product->buying_price             = $average_price;
                    $product->retail_price             = $cart['retail_price'];
                    $product->retail_discount          = $cart['retail_discount'];
                    $product->retail_discount_price    = $cart['retail_discount_price'];
                    $product->retail_vat               = $cart['retail_vat'];
                    $product->retail_vat_price         = $cart['retail_vat_price'];
                    $product->wholesale_price          = $cart['wholesale_price'];
                    $product->wholesale_discount       = $cart['wholesale_discount'];
                    $product->wholesale_discount_price = $cart['wholesale_discount_price'];
                    $product->wholesale_vat            = $cart['wholesale_vat'];
                    $product->wholesale_vat_price      = $cart['wholesale_vat_price'];
                    $product->wholesale_min_quantity   = $cart['wholesale_min_quantity'];
                    $product->quantity                 = $product->quantity + $cart['quantity'];
                    $product->stock_alert              = $cart['stock_alert'];
                    $product->warrenty_duration        = $cart['warrenty_duration'];
                    $product->warrenty_type            = $cart['warrenty_type'];
                    $product->expire                   = $cart['expire'];
                    $product->unit                     = $cart['unit'];
                    $product->image                    = $b64_image ?? null;
                    $product->save();

                }

                $cart['image'] = null;
                $b64_image     = null;

                PurchaseDetails::create([
                    'purchase_id'           => $purchase->id,
                    'product_id'            => $product->id,
                    'name'                  => $product->name,
                    'type'                  => $product->type,
                    'quantity'              => $cart['quantity'],
                    'unit_price'            => $product->retail_price,
                    'buying_price'          => $cart['buying_price'],
                    'vat'                   => $product->retail_vat,
                    'vat_price'             => $product->retail_vat_price,
                    'warrenty'              => $product->warrenty . $product->warrenty_type,
                    'product_quantity_type' => $cart["product_quantity_type"],
                ]);

                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'New ' . $product->type . ' name ' . $cart['name'] . ' buy from supplier';
                $notification->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Purchase placed successfully!!',
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
            $purchase = Purchase::where('id', $request->id)->with('purchaseDetails')->first();

            foreach ($purchase->purchaseDetails as $cart) {
                $product           = Product::find($cart['product_id']);
                $product_quantity  = $product->quantity - $cart['quantity'];
                $product->quantity = $product_quantity;
                $product->save();

                $cart->delete();
            }

            $purchase->user_id         = auth()->user()->user_id;
            $purchase->purchase_man    = auth()->user()->name;
            $purchase->supplier_id     = $request->supplier_id;
            $purchase->purchase_type   = $request->purchase_type;
            $purchase->invoice_no      = $purchase->invoice_no;
            $purchase->invoice_date    = $request->invoice_date;
            $purchase->discount        = $request->discount;
            $purchase->discount_price  = $request->discount_price;
            $purchase->vat_price       = $request->vat_price;
            $purchase->total           = $request->total; //purchase total
            $purchase->payment_type_id = $request->payment_type_id;
            $purchase->payment_amount  = $request->payment_amount;
            $purchase->balance         = $request->balance;
            $purchase->note            = $request->note;
            $purchase->voucher_number  = $request->voucher_number;
            $purchase->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Purchase invoice #' . $purchase->invoice_no . ' updated';
            $notification->save();

            if ($request["voucher_image"]) {
                $image_path = public_path($purchase->voucher_image) ?? '';

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $image_file              = base64_decode($request['voucher_image']);
                $b64_image               = '/images/purchase/' . time() . '.' . 'png';
                $success                 = file_put_contents(public_path() . $b64_image, $image_file);
                $purchase->voucher_image = $b64_image;
                $purchase->save();

            }

            foreach ($request->cart as $cart) {

                if ($cart['product_id'] == null) {

                    if ($cart['image']) {

                        $image_file = base64_decode($cart['image']);
                        $b64_image  = '/images/product/' . time() . uniqid() . '.' . 'png';
                        $success    = file_put_contents(public_path() . $b64_image, $image_file);
                    }

                    $product                           = new Product();
                    $product->user_id                  = auth()->user()->user_id;
                    $product->name                     = $cart['name'];
                    $product->type                     = $cart['type'];
                    $product->buying_price             = $cart['buying_price'];
                    $product->retail_price             = $cart['retail_price'];
                    $product->retail_discount          = $cart['retail_discount'];
                    $product->retail_discount_price    = $cart['retail_discount_price'];
                    $product->retail_vat               = $cart['retail_vat'];
                    $product->retail_vat_price         = $cart['retail_vat_price'];
                    $product->wholesale_price          = $cart['wholesale_price'];
                    $product->wholesale_discount       = $cart['wholesale_discount'];
                    $product->wholesale_discount_price = $cart['wholesale_discount_price'];
                    $product->wholesale_vat            = $cart['wholesale_vat'];
                    $product->wholesale_vat_price      = $cart['wholesale_vat_price'];
                    $product->wholesale_min_quantity   = $cart['wholesale_min_quantity'];
                    $product->quantity                 = $cart['quantity'];
                    $product->stock_alert              = $cart['stock_alert'];
                    $product->warrenty_duration        = $cart['warrenty_duration'];
                    $product->warrenty_type            = $cart['warrenty_type'];
                    $product->expire                   = $cart['expire'];
                    $product->unit                     = $cart['unit'];
                    $product->image                    = $b64_image ?? null;
                    $product->save();

                    $product->barcode = $product->id;
                    $product->save();

                } else {

                    if ($cart['image']) {

                        $image_file = base64_decode($cart['image']);
                        $b64_image  = '/images/product/' . time() . uniqid() . '.' . 'png';
                        $success    = file_put_contents(public_path() . $b64_image, $image_file);
                    }

                    $product = Product::find($cart['product_id']);

                    $average_price    = 0;
                    $total            = $cart['buying_price'] * $cart['quantity'];
                    $quantity         = $cart['quantity'];
                    $purchase_details = PurchaseDetails::where('product_id', $product->id)->get();

                    if ($purchase_details) {

                        foreach ($purchase_details as $details) {
                            $total += ($details->quantity * $details->buying_price);
                            $quantity += $details->quantity;
                        }

                    }

                    $average_price = $total / $quantity;

                    $product->buying_price             = $average_price;
                    $product->retail_price             = $cart['retail_price'];
                    $product->retail_discount          = $cart['retail_discount'];
                    $product->retail_discount_price    = $cart['retail_discount_price'];
                    $product->retail_vat               = $cart['retail_vat'];
                    $product->retail_vat_price         = $cart['retail_vat_price'];
                    $product->wholesale_price          = $cart['wholesale_price'];
                    $product->wholesale_discount       = $cart['wholesale_discount'];
                    $product->wholesale_discount_price = $cart['wholesale_discount_price'];
                    $product->wholesale_vat            = $cart['wholesale_vat'];
                    $product->wholesale_vat_price      = $cart['wholesale_vat_price'];
                    $product->wholesale_min_quantity   = $cart['wholesale_min_quantity'];
                    $product->quantity                 = $product->quantity + $cart['quantity'];
                    $product->stock_alert              = $cart['stock_alert'];
                    $product->warrenty_duration        = $cart['warrenty_duration'];
                    $product->warrenty_type            = $cart['warrenty_type'];
                    $product->expire                   = $cart['expire'];
                    $product->unit                     = $cart['unit'];
                    $product->image                    = $b64_image ?? null;
                    $product->save();

                }

                PurchaseDetails::create([
                    'purchase_id'           => $purchase->id,
                    'product_id'            => $product->id,
                    'name'                  => $product->name,
                    'type'                  => $product->type,
                    'quantity'              => $cart['quantity'],
                    'unit_price'            => $product->retail_price,
                    'buying_price'          => $cart['buying_price'],
                    'vat'                   => $product->retail_vat,
                    'vat_price'             => $product->retail_vat_price,
                    'warrenty'              => $product->warrenty . $product->warrenty_type,
                    'product_quantity_type' => $cart["product_quantity_type"],
                ]);

                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'New ' . $product->type . ' name ' . $cart['name'] . ' buy from supplier';
                $notification->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Purchase updated successfully!!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function returnPurchase(Request $request) {
        DB::beginTransaction();

        try {

            //checking if the product is for this invoice
            foreach ($request->cart as $cart) {
                $supplier_product = PurchaseDetails::where('purchase_id', $request->purchase_id)->where('product_id', $cart['product_id'])->first();

                if (!$supplier_product) {
                    return response()->json([
                        'status'   => false,
                        'maessage' => 'Invalid product for this purchase invoice.',
                    ]);
                } else {
                    $supplier_product = PurchaseDetails::where('purchase_id', $request->purchase_id)->where('product_id', $cart['product_id'])->first();

                    if ($supplier_product->quantity < $cart['quantity']) {

                        return response()->json([
                            'status'   => false,
                            'maessage' => 'Product limit over.',
                        ]);
                    }

                }

            }

            //generating invoice number
            $return_purchase = ReturnPurchase::select('user_id', 'invoice_no')
                ->where('user_id', auth()->user()->user_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($return_purchase) {
                $in_no = $return_purchase->invoice_no + 1;
            } else {
                $in_no = 1;
            }

            $purchase                  = new ReturnPurchase();
            $purchase->user_id         = auth()->user()->user_id;
            $purchase->purchase_man    = auth()->user()->name;
            $purchase->supplier_id     = $request->supplier_id;
            $purchase->purchase_id     = $request->purchase_id;
            $purchase->purchase_type   = $request->purchase_type;
            $purchase->invoice_no      = $in_no;
            $purchase->invoice_date    = $request->invoice_date;
            $purchase->discount        = $request->discount;
            $purchase->discount_price  = $request->discount_price;
            $purchase->vat_price       = $request->vat_price;
            $purchase->total           = $request->total; //purchase total
            $purchase->payment_type_id = $request->payment_type_id;
            $purchase->payment_amount  = $request->payment_amount;
            $purchase->balance         = $request->balance;
            $purchase->note            = $request->note;
            $purchase->save();

            //storing return purchase transaction history
            $th                     = new TransactionHistory();
            $th->user_id            = auth()->user()->user_id;
            $th->return_purchase_id = $purchase->id;
            $th->supplier_id        = $purchase->supplier_id;
            $th->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Purchase invoice <b>#' . $purchase->invoice_no . '</b> returned';
            $notification->save();

            foreach ($request->cart as $cart) {
                $product = Product::find($cart['product_id']);

                ReturnPurchaseDetails::create([
                    'return_purchase_id' => $purchase->id,
                    'product_id'         => $product->id,
                    'name'               => $product->name,
                    'type'               => $cart['type'],
                    'quantity'           => $cart['quantity'],
                    'unit_price'         => $product->retail_price,
                    'buying_price'       => $cart['buying_price'],
                    'vat'                => $cart['vat'],
                    'vat_price'          => $cart['vat_price'],
                    'warrenty'           => $cart['warrenty'],
                ]);

                if ($product) {
                    //update product quantity
                    $product_quantity  = $product->quantity - $cart['quantity'];
                    $product->quantity = $product_quantity > 0 ? $product_quantity : 0;
                    $product->save();

                }

            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Purchase return successfully!!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function updateReturnPurchase(Request $request) {
        DB::beginTransaction();

        try {

            $purchase = ReturnPurchase::where('id', $request->id)->where('user_id', auth()->user()->user_id)->with('returnPurchaseDetails')->first();

            foreach ($request->cart as $cart) {

                $return_purchase_details = ReturnPurchaseDetails::where('return_purchase_id', $purchase->id)->where('product_id', $cart['product_id'])->first();

                if (!$return_purchase_details) {
                    $supplier_product = PurchaseDetails::where('purchase_id', $request->purchase_id)->where('product_id', $cart['product_id'])->first();

                    if (!$supplier_product) {
                        return response()->json([
                            'status'   => false,
                            'maessage' => 'Invalid product for this purchase invoice.',
                        ]);
                    }

                } else {
                    $supplier_product = PurchaseDetails::where('purchase_id', $request->purchase_id)->where('product_id', $cart['product_id'])->first();

                    if ($supplier_product->quantity < $cart['quantity']) {

                        return response()->json([
                            'status'   => false,
                            'maessage' => 'Product limit over.',
                        ]);
                    }

                }

            }

            if ($purchase) {

                foreach ($purchase->returnPurchaseDetails as $cart) {
                    $product = Product::find($cart['product_id']);

                    if ($product) {
                        //update product quantity
                        $product_quantity  = $product->quantity + $cart['quantity'];
                        $product->quantity = $product_quantity;
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

            $purchase->user_id         = auth()->user()->user_id;
            $purchase->purchase_man    = auth()->user()->name;
            $purchase->supplier_id     = $request->supplier_id;
            $purchase->purchase_id     = $request->purchase_id;
            $purchase->purchase_type   = $request->purchase_type;
            $purchase->invoice_date    = $request->invoice_date;
            $purchase->discount        = $request->discount;
            $purchase->discount_price  = $request->discount_price;
            $purchase->vat_price       = $request->vat_price;
            $purchase->total           = $request->total; //purchase total
            $purchase->payment_type_id = $request->payment_type_id;
            $purchase->payment_amount  = $request->payment_amount;
            $purchase->balance         = $request->balance;
            $purchase->note            = $request->note;
            $purchase->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Purchase invoice #' . $purchase->invoice_no . ' returned';
            $notification->save();

            foreach ($request->cart as $cart) {
                $product = Product::find($cart['product_id']);

                ReturnPurchaseDetails::create([
                    'return_purchase_id' => $purchase->id,
                    'product_id'         => $product->id,
                    'name'               => $product->name,
                    'type'               => $cart['type'],
                    'quantity'           => $cart['quantity'],
                    'unit_price'         => $product->retail_price,
                    'buying_price'       => $cart['buying_price'],
                    'vat'                => $cart['vat'],
                    'vat_price'          => $cart['vat_price'],
                    'warrenty'           => $cart['warrenty'],
                ]);

                if ($product) {
                    //update product quantity
                    $product_quantity  = $product->quantity - $cart['quantity'];
                    $product->quantity = $product_quantity > 0 ? $product_quantity : 0;
                    $product->save();

                }

            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Purchase return successfully!!',
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
        $data             = [];
        $data['purchase'] = $purchase = Purchase::where('id', $request->id)->with('purchaseDetails.product', 'businessAccount', 'supplier')->first();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    public function delete($id) {
        $purchase = Purchase::where('id', $id)->with('purchaseDetails')->first();

        foreach ($purchase->purchaseDetails as $item) {
            $item->delete();
        }

        $image_path = public_path($purchase->voucher_image);

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        $purchase->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Purchase deleted successfully!!',
        ]);
    }

    public function salesReport(Request $request) {

        if ($request->supplier_id) {
            $data = Purchase::where('user_id', auth()->user()->user_id)
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date)
                ->where('supplier_id', $request->supplier_id);

            if ($request->purchase_man) {
                $data = $data->where('purchase_man', $request->purchase_man);
            }

            $data = $data->with('purchaseDetails.product', 'supplier', 'businessAccount')->orderBy('id', 'desc')->get();

            $total_product = 0;

            foreach ($data as $item) {

                foreach ($item->purchaseDetails as $item) {
                    $total_product += ($item->quantity);
                }

            }

            $total_purchase_amount = $data->sum('total');

        } else {
            $data = Purchase::where('user_id', auth()->user()->user_id)
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date);

            if ($request->purchase_man) {
                $data = $data->where('purchase_man', $request->purchase_man);
            }

            $data          = $data->with('purchaseDetails.product', 'supplier', 'businessAccount')->orderBy('id', 'desc')->get();
            $total_product = 0;

            foreach ($data as $data_item) {

                foreach ($data_item->purchaseDetails as $item) {
                    $total_product += ($item->quantity);
                }

            }

            $total_purchase_amount = $data->sum('total');

        }

        if ($data) {
            return response()->json([
                'status'                => true,
                'message'               => 'Data found successfully',
                'data'                  => $data,
                'total_product'         => $total_product,
                'total_purchase_amount' => $total_purchase_amount,
                'total_purchase_count'  => $data->count(),
            ]);
        } else {
            return response()->json([
                'status'                => true,
                'message'               => 'No data found',
                'data'                  => $data,
                'total_product'         => $total_product,
                'total_purchase_amount' => $total_purchase_amount,
                'total_purchase_count'  => $data->count(),
            ]);
        }

    }

    public function listfromAndTo(Request $request) {
        $data = DB::table('products')
            ->where('user_id', auth()->user()->user_id)
            ->rightJoin('purchase_details', 'purchase_details.product_id', '=', 'products.id')
            ->whereDate('purchase_details.created_at', '>=', $request->start_date)
            ->whereDate('purchase_details.created_at', '<=', $request->end_date)
            ->select('products.id as product_id', 'purchase_details.*')
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

    public function purchaseFromSupplierDetails(Request $request) {

        if ($request->supplier_id) {
            $data = Purchase::where('user_id', auth()->user()->user_id)
                ->where('supplier_id', $request->supplier_id)
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date)
                ->with('purchaseDetails.product', 'businessAccount', 'supplier', 'expense')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $data = Purchase::where('user_id', auth()->user()->user_id)
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date)
                ->with('purchaseDetails.product', 'businessAccount', 'supplier', 'expense')
                ->orderBy('id', 'desc')
                ->get();
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

    public function latestPurchaseInvoiceNumber() {
        $invoice = Purchase::where('user_id', auth()->user()->user_id)
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

    public function latestPurchaseReturnInvoiceNumber() {
        $invoice = ReturnPurchase::where('user_id', auth()->user()->user_id)
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

    public function sendToOrderList(Request $request) {

        if ($request->start_date && $request->end_date) {
            $data = SendToOrderList::where('user_id', auth()->user()->user_id)->whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date)->with('product')->paginate();
        } else {
            $data = SendToOrderList::where('user_id', auth()->user()->user_id)->with('product')->paginate();
        }

        return response()->json([
            'status'  => true,
            'message' => 'data',
            'data'    => $data,
        ]);
    }

    public function storeSendToOrderList(Request $request) {
        $product = Product::find($request->product_id);
        $data    = SendToOrderList::create([
            'user_id'    => auth()->user()->user_id,
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
            'name'       => $product->name,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'data',
            'data'    => $data,
        ]);
    }

    public function deleteSendToOrderList($id) {
        $data = SendToOrderList::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

        if ($data) {
            $data->delete();

            return response()->json([
                'status'  => true,
                'message' => 'data deleted successfully',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No data found',
            ]);
        }

    }

}
