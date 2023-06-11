<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller {
    public function index() {
        $states = State::orderBy('country_id', 'ASC')->with('country')->get();

        return view('backend.state.index', compact('states'));
    }

    public function create() {
        $countries = Country::where('status', 1)->get();

        return view('backend.state.create', compact('countries'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        State::create([
            'country_id' => $request->country_id,
            'en_name'    => $request->en_name,
            'bn_name'    => $request->bn_name,
            'status'     => 1,
        ]);

        return to_route('admin.state.index')->withToastSuccess('New country added successfully');
    }

    public function edit(State $state) {
        $countries = Country::where('status', 1)->get();

        return view('backend.state.edit', compact('countries', 'state'));
    }

    public function update(Request $request, State $state) {
        $validator = Validator::make($request->all(), [
            'en_name' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $state->country_id = $request->country_id;
        $state->en_name       = $request->en_name;
        $state->bn_name       = $request->bn_name;
        $state->save();

        return to_route('admin.state.index')->withToastSuccess('State updated successfully!!');
    }

    public function active(Request $request, State $state) {
        $state->status = 1;
        $state->save();

        return to_route('admin.state.index')->withToastSuccess('State activated successfully!!');
    }

    public function inactive(Request $request, State $state) {
        $state->status = 0;
        $state->save();

        return to_route('admin.state.index')->withToastSuccess('State inactivated successfully!!');
    }

}
