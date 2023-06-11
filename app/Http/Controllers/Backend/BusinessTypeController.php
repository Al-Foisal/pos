<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessTypeController extends Controller
{
    public function index() {
        $business_types = BusinessType::orderBy('en_name', 'ASC')->get();

        return view('backend.business_type.index', compact('business_types'));
    }

    public function create() {
        return view('backend.business_type.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique:business_types',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        BusinessType::create([
            'en_name' => $request->en_name,
            'bn_name' => $request->bn_name,
            'status'  => 1,
        ]);

        return to_route('admin.business_type.index')->withToastSuccess('New business type added successfully');
    }

    public function edit(BusinessType $business_type) {
        return view('backend.business_type.edit', compact('business_type'));
    }

    public function update(Request $request, BusinessType $business_type) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique:countries,en_name,' . $business_type->id,
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $business_type->en_name = $request->en_name;
        $business_type->bn_name = $request->bn_name;
        $business_type->save();

        return to_route('admin.business_type.index')->withToastSuccess('Business type updated successfully!!');
    }

    public function active(Request $request, BusinessType $business_type) {
        $business_type->status = 1;
        $business_type->save();

        return to_route('admin.business_type.index')->withToastSuccess('Business type activated successfully!!');
    }

    public function inactive(Request $request, BusinessType $business_type) {
        $business_type->status = 0;
        $business_type->save();

        return to_route('admin.business_type.index')->withToastSuccess('Business type inactivated successfully!!');
    }

}
