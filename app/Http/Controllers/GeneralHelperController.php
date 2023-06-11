<?php

namespace App\Http\Controllers;

use App\Models\AppFeature;
use App\Models\Page;
use App\Models\Slider;
use App\Models\State;

class GeneralHelperController extends Controller {
    public function getState($id) {
        $state = State::where('country_id', $id)->where('status', 1)->get();

        return json_encode($state);
    }

    public function home() {
        $data            = [];
        $data['slider']  = Slider::orderBy('id', 'desc')->get();
        $data['feature'] = AppFeature::all();

        return view('frontend.home', $data);
    }

    public function pageDetails($slug) {
        $page = Page::where('slug', $slug)->first();

        return view('frontend.page-details', compact('page'));
    }
}
