<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller 
{
    public function index() {
        $countries = Country::orderBy('en_name', 'ASC')->get();

        return view('backend.country.index', compact('countries'));
    }

    public function create() {
        return view('backend.country.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique:countries',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        Country::create([
            'en_name' => $request->en_name,
            'bn_name' => $request->bn_name,
            'status'  => 1,
        ]);

        return to_route('admin.country.index')->withToastSuccess('New country added successfully');
    }

    public function edit(Country $country) {
        return view('backend.country.edit', compact('country'));
    }

    public function update(Request $request, Country $country) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required|unique:countries,en_name,' . $country->id,
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $country->en_name = $request->en_name;
        $country->bn_name = $request->bn_name;
        $country->save();

        return to_route('admin.country.index')->withToastSuccess('Country updated successfully!!');
    }

    public function active(Request $request, Country $country) {
        $country->status = 1;
        $country->save();

        return to_route('admin.country.index')->withToastSuccess('Country activated successfully!!');
    }

    public function inactive(Request $request, Country $country) {
        $country->status = 0;
        $country->save();

        return to_route('admin.country.index')->withToastSuccess('Country inactivated successfully!!');
    }

}
