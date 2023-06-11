<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomeExpense;
use App\Models\Notification;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SupplierController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data                    = [];
        $data['todays_supplier'] = Supplier::where('user_id', auth()->user()->user_id)->whereDate('created_at', '=', today())->where('type', 1)->count();
        $data['total_supplier']  = Supplier::where('user_id', auth()->user()->user_id)->count();
        $data['suppliers']       = Supplier::where('user_id', auth()->user()->user_id)->with('group', 'user')->orderBy('id', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data found successfully',
            'data'    => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        DB::beginTransaction();

        try {
            $request->validate([
                'name'  => 'required',
                'phone' => 'required',
            ]);

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/supplier/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            $supplier = Supplier::create([
                'user_id'     => auth()->user()->user_id,
                'name'        => $request->name,
                'phone'       => $request->phone,
                'group_id'    => $request->group_id,
                'email'       => $request->email,
                'address'     => $request->address,
                'amount'      => $request->amount ?? 0,
                'modify_date' => $request->modify_date,
                'image'       => $final_name1 ?? null,
            ]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'New supplier ' . $supplier->name . ' added by ' . auth()->user()->name;
            $notification->save();

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Supplier created successfully!!',
                'supplier' => $supplier,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $data = [];

        $data['supplier'] = $ss = Supplier::where('id', $id)->with('user')->first();

        $data['transaction_history'] = TransactionHistory::where('supplier_id', $id)->where('user_id', auth()->user()->user_id)
            ->orderBy('id', 'desc')
            ->with(
                'purchase.purchaseDetails.product',
                'returnPurchase.returnPurchaseDetails.product',
                'returnPurchase.purchase',
                'expense_ts'
            )->get();

        $total_purchase = 0;
        $total_due      = 0;
        $purchase       = Purchase::where('user_id', auth()->user()->user_id)->where('supplier_id', $id)->get();

        foreach ($purchase as $item) {
            $total_purchase += $item->total;

// foreach ($item->returnPurchase as $return) {

//     $total_purchase -= $return->total;
            // }

        }

        $check_supplier_due = 0;

        $check_supplier_due += $ss->amount;

        foreach ($ss->purchase as $sp_item) {

            if ($sp_item->balance != 0) {
                $check_supplier_due += $sp_item->balance;
            }

        }

        foreach ($ss->incomeExpenses as $sie_item) {

            if ($sie_item->expense_id == 26) {
                $check_supplier_due -= $sie_item->amount;
            }

        }

        foreach ($ss->returnPurchase as $purchase) {

            if ($purchase->balance > 0) {
                $check_supplier_due -= $purchase->balance;
            } else {
                $check_supplier_due += $purchase->balance;
            }

        }

        $total_payment = (int) IncomeExpense::where('user_id', auth()->user()->user_id)->where('supplier_id', $id)->sum('amount');

        $data['total_purchase'] = $total_purchase;
        $data['total_balance']  = $check_supplier_due;

// $data['total_payment']  = $total_payment;

// $data['total_balance']  = $total_due + $ss->amount - $total_payment;

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        DB::beginTransaction();

        try {

            $request->validate([
                'name'  => 'required',
                'phone' => 'required',
            ]);

            $supplier      = Supplier::where('id', $id)->first();
            $supplier_name = $supplier->name;

            if (!isset($supplier)) {
                return 'Supplier not found';
            }

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $image_path = public_path($supplier->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/supplier/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $supplier->image = $final_name1;
                    $supplier->save();
                }

            }

            $supplier->name        = $request->name;
            $supplier->user_id     = auth()->user()->user_id;
            $supplier->phone       = $request->phone;
            $supplier->group_id    = $request->group_id;
            $supplier->email       = $request->email;
            $supplier->address     = $request->address;
            $supplier->amount      = $request->amount ?? 0;
            $supplier->modify_date = $request->modify_date;
            $supplier->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Supplier <b>' . $supplier_name . '</b> changed to <b>' . $supplier->name . '</b> by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Supplier updated successfully!!',
                'supplier' => $supplier,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        if (auth()->user()->id != auth()->user()->user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        }

        $supplier = Supplier::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

        $image_path = public_path($supplier->image);

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        if (!$supplier) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        } else {
            $supplier->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Supplier deleted successfully!!',
            ]);
        }

    }

}
