<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BalanceTransferController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data = BalanceTransfer::where('user_id', auth()->user()->user_id)->with('sender', 'reciver')->orderBy('id', 'desc')->paginate();

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        DB::beginTransaction();

        try {
            $item = PaymentType::where('id', $request->sender_account)
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
                ->first();

            $data = 0;

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

            if ($request->amount > $data) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient amount',
                ]);
            }

            if ($request->hasFile('voucher_image')) {

                $image_file = $request->file('voucher_image');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/balance/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            $balance                   = new BalanceTransfer();
            $balance->user_id          = auth()->user()->user_id;
            $balance->transfer_date    = $request->transfer_date;
            $balance->amount           = $request->amount;
            $balance->sender_account   = $request->sender_account;
            $balance->receiver_account = $request->receiver_account;
            $balance->voucher_image    = $final_name1 ?? null;
            $balance->voucher_no       = $request->voucher_no;
            $balance->transfer_person  = $request->transfer_person;
            $balance->note             = $request->note;
            $balance->save();
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $data = BalanceTransfer::findOrFail($id);

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

            $balance = BalanceTransfer::find($id);

            if ($request->hasFile('voucher_image')) {

                $image_file = $request->file('voucher_image');

                if ($image_file) {

                    $image_path = public_path($balance->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/balance/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $balance->voucher_image = $final_name1;
                    $balance->save();
                }

            }

            $balance->transfer_date    = $request->transfer_date;
            $balance->amount           = $request->amount;
            $balance->sender_account   = $request->sender_account;
            $balance->receiver_account = $request->receiver_account;
            $balance->voucher_no       = $request->voucher_no;
            $balance->transfer_person  = $request->transfer_person;
            $balance->note             = $request->note;
            $balance->save();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $balance = BalanceTransfer::findOrFail($id);

        $image_path = public_path($balance->image);

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        $balance->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data deleted successfully!!',
        ]);
    }

}
