<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\IncomeExpense;
use App\Models\Notification;
use App\Models\Order;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CustomerController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data                    = [];
        $data['todays_customer'] = Customer::where('user_id', auth()->user()->user_id)->whereDate('created_at', '=', today())->where('type', 1)->count();
        $data['total_customer']  = Customer::where('user_id', auth()->user()->user_id)->count();
        $data['customers']       = Customer::where('user_id', auth()->user()->user_id)->with(['group', 'user' => function ($query) {
            return $query->select(['id', 'business_name'])->get();
        },
        ])->orderBy('id', 'desc')->get();

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

        $request->validate([
            'name'  => 'required',
            'phone' => 'required',
        ]);

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');

            if ($image_file) {

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/customer/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
            }

        }

        try {
            $customer = Customer::create([
                'name'        => $request->name,
                'user_id'     => auth()->user()->user_id,
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
            $notification->name    = 'New customer <b>' . $request->name . ' added by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Customer created successfully!!',
                'customer' => $customer,
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
        $data             = [];
        $data['customer'] = $cc = Customer::where('id', $id)->where('user_id', auth()->user()->user_id)
            ->with('user')->first();
        $data['transaction_history'] = TransactionHistory::where('customer_id', $id)->where('user_id', auth()->user()->user_id)
            ->orderBy('id', 'desc')
            ->with(
                'order.orderDetails.product',
                'returnOrder.orderDetails.product',
                'returnOrder.order',
                'income_fc'
            )->get();

        $total_sell = 0;
        // $total_due  = 0;
        $sell = Order::where('user_id', auth()->user()->user_id)->where('customer_id', $id)->get();

        foreach ($sell as $item) {
            $total_sell += $item->total;

// foreach ($item->returnOrder as $return) {

//     $total_sell -= $return->total;
            // }

        }

        $check_customer_due = 0;

        $check_customer_due += $cc->amount;

        foreach ($cc->orders as $co_item) {

            if ($co_item->balance != 0) {
                $check_customer_due += $co_item->balance;
            }

        }

        foreach ($cc->incomeExpenses as $cie_item) {

            if ($cie_item->income_id == 12) {
                $check_customer_due -= $cie_item->amount;
            }

        }

        foreach ($cc->returnOrders as $rr) {

            if ($rr->balance > 0) {

                $check_customer_due -= $rr->balance;

            } else {

                $check_customer_due += $rr->balance;

            }

        }

        $total_payment = (int) IncomeExpense::where('user_id', auth()->user()->user_id)->where('customer_id', $id)->sum('amount');
// return $total_payment;
        $data['total_sell']    = $total_sell;
        $data['total_balance'] = $check_customer_due;

// $data['total_payment'] = $total_payment;

// $data['total_balance'] = $total_due + $cc->amount - $total_payment;

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
        $request->validate([
            'name'  => 'required',
            'phone' => 'required',
        ]);
        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($id);

            $customer->name        = $request->name;
            $customer->user_id     = auth()->user()->user_id;
            $customer->phone       = $request->phone;
            $customer->group_id    = $request->group_id;
            $customer->email       = $request->email;
            $customer->address     = $request->address;
            $customer->amount      = $request->amount ?? 0;
            $customer->modify_date = $request->modify_date;
            $customer->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Customer name change from <b>' . $customer->name . '</br> to <b>' . $request->name . '</b> by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $image_path = public_path($customer->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/customer/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $customer->image = $final_name1;
                    $customer->save();
                }

            }

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Customer updated successfully!!',
                'customer' => $customer,
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

        $customer = Customer::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

        $image_path = public_path($customer->image);

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        } else {
            $customer->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Customer deleted successfully!!',
            ]);
        }

    }

}
