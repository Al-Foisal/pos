<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Package;
use App\Models\SubscriptionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller {
    public function login() {
        return view('backend.auth.login');
    }

    public function storeLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all())->withInput();
        }

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {

            return redirect()->route('admin.dashboard');

        }

        return redirect()->route('admin.auth.login')->withToastError('Invalid Credentitials!!');

    }

    public function adminList() {
        $admins = Admin::all();

        return view('backend.auth.admin-list', compact('admins'));
    }

    public function customerList() {
        $customers = User::where('id', '=', DB::raw('user_id'))->orderBy('id', 'desc')->paginate(500);

        return view('backend.auth.customer-list', compact('customers'));
    }

    public function updateCustomSubscription(Request $request) {
        $user = User::find($request->user_id);

        $package        = Package::where('price', 0)->first();
        $user->validity = date("Y-m-d", strtotime('+' . $request->date . ' days'));
        $user->save();

        if ($package) {

            if ($package->discount > 0) {
                $price = $package->discount_price;
            } else {
                $price = $package->price;
            }

            $subscription                  = new SubscriptionHistory();
            $subscription->user_id         = $user->id;
            $subscription->package_id      = $package->id;
            $subscription->en_package_name = $package->en_name;
            $subscription->bn_package_name = $package->bn_name;
            $subscription->price           = $price;
            $subscription->duration        = $request->date;
            $subscription->user_limit      = $package->user_limit;
            $subscription->validity_from   = date("Y-m-d");
            $subscription->validity_to     = date("Y-m-d", strtotime('+' . $request->date . ' days'));
            $subscription->save();

        }

        return redirect()->route('admin.auth.customerList')->withToastSuccess('Custom subscription updated!');

    }

    public function createAdmin() {
        return view('backend.auth.create-admin');
    }

    public function storeAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'phone'    => 'required|numeric',
            'email'    => 'required|email|unique:admins',
            'password' => 'required',
            'address'  => 'required',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all())->withInput();
        }

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');

            if ($image_file) {

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/admin/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
            }

        }

        $admin           = new Admin();
        $admin->name     = $request->name;
        $admin->phone    = $request->phone;
        $admin->email    = $request->email;
        $admin->password = bcrypt($request->password);
        $admin->address  = $request->address;
        $admin->image    = $final_name1 ?? '';
        $admin->status   = 1;
        $admin->save();

        return redirect()->route('admin.auth.adminList')->withToastSuccess('New Amin Registered Successfully!!');
    }

    public function editAdmin(Admin $admin) {
        return view('backend.auth.edit-admin', compact('admin'));
    }

    public function updateAdmin(Request $request, Admin $admin) {
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
            'phone'   => 'required',
            'email'   => 'required|email|unique:admins,email,' . $admin->id,
            'address' => 'required',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all())->withInput();
        }

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');

            if ($image_file) {

                $image_path = public_path($admin->image);

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/admin/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
                $admin->image = $final_name1;
                $admin->save();

            }

        }

        $admin->name    = $request->name;
        $admin->phone   = $request->phone;
        $admin->email   = $request->email;
        $admin->address = $request->address;
        $admin->update();

        return redirect()->route('admin.auth.adminList')->withToastSuccess('The admin updated successfully!!');
    }

    public function activeAdmin(Request $request, Admin $admin) {
        $admin->status = 1;
        $admin->save();

        return redirect()->back()->withToastSuccess('The admin activated successfully!!');
    }

    public function inactiveAdmin(Request $request, Admin $admin) {
        $admin->status = 0;
        $admin->save();

        return redirect()->back()->withToastSuccess('The admin inactivated successfully!!');
    }

    public function deleteAdmin(Request $request, Admin $admin) {
        $image_path = public_path($admin->image);

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        $admin->delete();

        return redirect()->back()->withToastSuccess('The admin deleted successfully!!');
    }

    public function logout(Request $request) {
        Auth::guard('admin')->logout();

        return redirect()
            ->route('admin.auth.login')
            ->withToastSuccess('Logout Successful!!');
    }

}
