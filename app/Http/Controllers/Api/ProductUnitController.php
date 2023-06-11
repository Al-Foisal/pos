<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductUnitController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data = ProductUnit::whereNull('user_id')->orWhere('user_id', auth()->user()->user_id)->orderBy('id', 'asc')->get();

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
            $unit = ProductUnit::create([
                'user_id' => auth()->user()->user_id,
                'name'    => $request->name,
            ]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'New product/service unit <b>' . $unit->name . '</b> added by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Product unit added successfully!!',
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
        $data = ProductUnit::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

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
            $unit = ProductUnit::find($id);
            $unit->update(['name' => $request->name]);

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'New product/service unit <b>' . $unit->name . '</b> updated by <b>' . auth()->user()->name . '</b>';
            $notification->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Product unit updated successfully!!',
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
        $unit = ProductUnit::where('id', $id)->where('user_id', auth()->user()->user_id)->first();
        $unit->delete();

        $notification          = new Notification();
        $notification->user_id = auth()->user()->user_id;
        $notification->name    = 'New product/service unit <b>' . $unit->name . '</b> deleted by <b>' . auth()->user()->name . '</b>';
        $notification->save();

        return response()->json([
            'status'  => true,
            'message' => 'Product unit deleted successfully!!',
        ]);
    }

}
