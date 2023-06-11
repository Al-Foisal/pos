<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data = Income::whereNull('user_id')->orWhere('user_id', auth()->user()->user_id)->orderBy('id', 'asc')->get();

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
            Income::create([
                'user_id' => auth()->user()->user_id,
                'name'    => $request->name,
            ]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Income title ' . $request->name . ' added by ' . auth()->user()->name;
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Income added successfully!!',
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
        $data = Income::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

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
            $income = Income::find($id);
            $income->update(['name' => $request->name]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Income title change from ' . $income->name . ' to ' . $request->name . ' by ' . auth()->user()->name;
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Income updated successfully!!',
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $income = Income::where('id', $id)->first();
        $income->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Income deleted successfully!!',
        ]);
    }

}
