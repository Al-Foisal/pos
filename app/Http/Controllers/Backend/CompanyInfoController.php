<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyInfoController extends Controller {
    public function showCompanyInfo() {
        $info = CompanyInfo::where('id', 1)->first();

        return view('backend.company-info', compact('info'));
    }

    public function storeCompanyInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'about'   => 'required',
            'address' => 'required',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all())->withInput();
        }

        CompanyInfo::updateOrCreate(
            ['id' => 1],
            [
                'name'       => $request->name,
                'about'       => $request->about,
                'address'     => $request->address,
                'phone_one'   => $request->phone_one,
                'phone_two'   => $request->phone_two,
                'phone_three' => $request->phone_three,
                'app_link'    => $request->app_link,
                'email'       => $request->email,
                'facebook'    => $request->facebook,
                'twitter'     => $request->twitter,
                'instagram'   => $request->instagram,
                'youtube'     => $request->youtube,
                'linkedin'    => $request->linkedin,
                'pinterest'   => $request->pinterest,
            ]
        );

        if ($request->hasFile('logo')) {

            $image_file = $request->file('logo');

            if ($image_file) {

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/logo/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
                CompanyInfo::updateOrCreate(
                    ['id' => 1],
                    [
                        'logo' => $final_name1,
                    ]
                );
            }

        }

        if ($request->hasFile('favicon')) {

            $image_file = $request->file('favicon');

            if ($image_file) {

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/logo/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
                CompanyInfo::updateOrCreate(
                    ['id' => 1],
                    [
                        'favicon' => $final_name1,
                    ]
                );
            }

        }

        if ($request->hasFile('app_logo')) {

            $image_file = $request->file('app_logo');

            if ($image_file) {

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/logo/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
                CompanyInfo::updateOrCreate(
                    ['id' => 1],
                    [
                        'app_logo' => $final_name1,
                        'app_link' => $request->app_link,
                    ]
                );
            }

        }

        return redirect()->back()->withToastSuccess('Company Info Added Successfully!!');
    }

    public function getCompanyNotification() {
        $data = Notification::whereNull('user_id')->orderBy('id', 'desc')->get();

        return view('backend.notification', compact('data'));
    }

    public function storeCompanyNotification(Request $request) {
        // dd($request->all());
        Notification::create([
            'name' => $request->name,
        ]);

        return redirect()->back();
    }

}
