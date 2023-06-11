<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data = Expense::whereNull('user_id')
            ->orWhere('user_id', auth()->user()->user_id)
            ->orderBy('name', 'asc')
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
            Expense::create([
                'user_id' => auth()->user()->user_id,
                'name'    => $request->name,
            ]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Expense title ' . $request->name . ' added by ' . auth()->user()->name;
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Expense added successfully!!',
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
        $data = Expense::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

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
            $expense = Expense::where('id', $id)->where('user_id', auth()->user()->user_id)->first();
            $expense->update(['name' => $request->name]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'Expense title change from <b>' . $expense->name . '</br> to <b>' . $request->name . '</b> by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Expense updated successfully!!',
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
        $expense = Expense::where('id', $id)->first();
        $expense->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Expense deleted successfully!!',
        ]);
    }

}
