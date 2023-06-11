<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller {
    public function index() {
        $packages = Package::orderBy('en_name', 'ASC')->get();

        return view('backend.package.index', compact('packages'));
    }

    public function create() {
        $package_feature = PackageFeature::where('status',1)->get();
        return view('backend.package.create',compact('package_feature'));
    }

    public function store(Request $request) {
        // dd($request->feature);
        $validator = Validator::make($request->all(), [
            'en_name'    => 'required',
            'price'      => 'required',
            'duration'   => 'required',
            'user_limit' => 'required',
            'image'      => 'required|image|mimes:jpeg,jpg,png,gif',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');

            if ($image_file) {

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/package/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
            }

        }

        $package = Package::create([
            'en_name'        => $request->en_name,
            'bn_name'        => $request->bn_name,
            'price'          => $request->price,
            'discount'       => $request->discount,
            'discount_price' => $request->discount_price,
            'duration'       => $request->duration,
            'user_limit'     => $request->user_limit,
            'image'          => $final_name1,
            'status'         => 1,
        ]);

        $package->packageFeatures()->sync($request->feature);

        return to_route('admin.package.index')->withToastSuccess('New Package Feature added successfully');
    }

    public function edit(Package $package) {
        $package_feature = PackageFeature::where('status',1)->get();
        return view('backend.package.edit', compact('package','package_feature'));
    }

    public function update(Request $request, Package $package) {
        $validator = Validator::make($request->all(), [
            'en_name'    => 'required',
            'price'      => 'required',
            'duration'   => 'required',
            'user_limit' => 'required',
            'image'      => 'nullable|mimes:jpeg,jpg,png,gif',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $package->en_name        = $request->en_name;
        $package->bn_name        = $request->bn_name;
        $package->price          = $request->price;
        $package->discount       = $request->discount;
        $package->discount_price = $request->discount_price;
        $package->duration       = $request->duration;
        $package->user_limit     = $request->user_limit;
        $package->save();

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');

            if ($image_file) {

                $image_path = public_path($package->image);

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/package/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
                $package->update(
                    [
                        'image' => $final_name1,
                    ]
                );
            }

        }

        $package->packageFeatures()->sync($request->feature);

        return to_route('admin.package.index')->withToastSuccess('Package Feature updated successfully!!');
    }

    public function active(Request $request, Package $package) {
        $package->status = 1;
        $package->save();

        return to_route('admin.package.index')->withToastSuccess('Package Feature activated successfully!!');
    }

    public function inactive(Request $request, Package $package) {
        $package->status = 0;
        $package->save();

        return to_route('admin.package.index')->withToastSuccess('Package Feature inactivated successfully!!');
    }

}
