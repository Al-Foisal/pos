<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PaymentType;
use App\Models\PreviousCashinHand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentTypeController extends Controller {
    public function index() {
        $data = PaymentType::whereNull('user_id')
            ->orWhere('user_id', auth()->user()->user_id)
            ->with([
                'order',
                'income',
                'purchase',
                'expense',
                'returnPurchase',
                'returnOrder',
                'senderBalanceTransfer',
                'receiverBalanceAccept',
                'previousCashinHand',
            ])
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        DB::beginTransaction();

        try {
            PaymentType::create([
                'user_id' => auth()->user()->user_id,
                'name'    => $request->name,
            ]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Payment type title ' . $request->name . ' added by ' . auth()->user()->name;
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Payment type added successfully!!',
            ]);
        } catch (\Throwable$th) {
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
        $data = PaymentType::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

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
            $payment = PaymentType::find($id);
            $payment->update(['name' => $request->name]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Payment type title change from ' . $payment->name . ' to ' . $request->name . ' by ' . auth()->user()->name;
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Payment type updated successfully!!',
            ]);
        } catch (\Throwable$th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function active(Request $request, $id) {
        DB::beginTransaction();

        try {
            $payment = PaymentType::find($id);
            $payment->update(['status' => 1]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Payment type status activated successfully!!',
            ]);
        } catch (\Throwable$th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function inactive(Request $request, $id) {
        DB::beginTransaction();

        try {
            $payment = PaymentType::find($id);
            $payment->update(['status' => 0]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Payment type status inactivated successfully!!',
            ]);
        } catch (\Throwable$th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function storePreviousCashinHand(Request $request) {
        $data                  = new PreviousCashinHand();
        $data->user_id         = Auth::id();
        $data->payment_type_id = $request->payment_type_id;
        $data->value           = $request->value;
        $data->save();

        return response()->json([
            'status'  => true,
            'message' => 'Previous Payment added successfully!!',
        ]);
    }

}
