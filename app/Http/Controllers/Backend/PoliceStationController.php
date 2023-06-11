<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PoliceStation;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PoliceStationController extends Controller {
    public function index() {
        $police_station = PoliceStation::orderBy('country_id', 'ASC')->with('country', 'state')->get();

        return view('backend.police_station.index', compact('police_station'));
    }

    public function create() {
        $countries = Country::where('status', 1)->get();
        $states    = State::where('status', 1)->get();

        return view('backend.police_station.create', compact('countries', 'states'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        PoliceStation::create([
            'country_id' => $request->country_id,
            'state_id'   => $request->state_id,
            'en_name'    => $request->en_name,
            'bn_name'    => $request->bn_name,
            'status'     => 1,
        ]);

        return to_route('admin.p_s.index')->withToastSuccess('New police station added successfully');
    }

    public function edit(PoliceStation $p_s) {
        $countries = Country::where('status', 1)->get();
        $states    = State::where('status', 1)->where('country_id', $p_s->country_id)->get();

        return view('backend.police_station.edit', compact('countries', 'states', 'p_s'));
    }

    public function update(Request $request, PoliceStation $p_s) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $p_s->country_id = $request->country_id;
        $p_s->state_id   = $request->state_id;
        $p_s->en_name    = $request->en_name;
        $p_s->bn_name    = $request->bn_name;
        $p_s->save();

        return to_route('admin.p_s.index')->withToastSuccess('Police Station updated successfully!!');
    }

    public function active(Request $request, PoliceStation $p_s) {
        $p_s->status = 1;
        $p_s->save();

        return to_route('admin.p_s.index')->withToastSuccess('Police Station activated successfully!!');
    }

    public function inactive(Request $request, PoliceStation $p_s) {
        $p_s->status = 0;
        $p_s->save();

        return to_route('admin.p_s.index')->withToastSuccess('Police Station inactivated successfully!!');
    }

}
