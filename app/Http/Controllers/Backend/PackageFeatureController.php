<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PackageFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageFeatureController extends Controller {
    public function index() {
        $package_features = PackageFeature::orderBy('en_name', 'ASC')->get();

        return view('backend.package_feature.index', compact('package_features'));
    }

    public function create() {
        return view('backend.package_feature.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique:package_features',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        PackageFeature::create([
            'en_name' => $request->en_name,
            'bn_name' => $request->bn_name,
            'status'  => 1,
        ]);

        return to_route('admin.package_feature.index')->withToastSuccess('New Package Feature added successfully');
    }

    public function edit(PackageFeature $package_feature) {
        return view('backend.package_feature.edit', compact('package_feature'));
    }

    public function update(Request $request, PackageFeature $package_feature) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique:package_features,en_name,' . $package_feature->id,
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $package_feature->en_name = $request->en_name;
        $package_feature->bn_name = $request->bn_name;
        $package_feature->save();

        return to_route('admin.package_feature.index')->withToastSuccess('Package Feature updated successfully!!');
    }

    public function active(Request $request, PackageFeature $package_feature) {
        $package_feature->status = 1;
        $package_feature->save();

        return to_route('admin.package_feature.index')->withToastSuccess('Package Feature activated successfully!!');
    }

    public function inactive(Request $request, PackageFeature $package_feature) {
        $package_feature->status = 0;
        $package_feature->save();

        return to_route('admin.package_feature.index')->withToastSuccess('Package Feature inactivated successfully!!');
    }

}
