<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller {
    public function index() {
        $pages = Page::orderBy('status', 'DESC')->get();

        return view('backend.page.index', compact('pages'));
    }

    public function create() {
        return view('backend.page.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'en_name'    => 'required|unique:pages',
            'bn_name'    => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        Page::create([
            'en_name'    => $request->en_name,
            'bn_name'    => $request->bn_name,
            'en_details' => $request->en_details,
            'bn_details' => $request->bn_details,
            'status'  => 1,
        ]);

        return to_route('admin.page.index')->withToastSuccess('New page added successfully');
    }

    public function edit(Page $page) {
        return view('backend.page.edit', compact('page'));
    }

    public function update(Request $request, Page $page) {
        $validator = Validator::make($request->all(), [
            'en_name'    => 'required|unique:pages,en_name,'.$page->id,
            'en_details' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $page->en_name    = $request->en_name;
        $page->en_details = $request->en_details;
        $page->bn_name    = $request->bn_name;
        $page->bn_details = $request->bn_details;
        $page->save();

        return to_route('admin.page.index')->withToastSuccess('Page updated successfully!!');
    }

    public function active(Request $request, Page $page) {
        $page->status = 1;
        $page->save();

        return to_route('admin.page.index')->withToastSuccess('Page activated successfully!!');
    }

    public function inactive(Request $request, Page $page) {
        $page->status = 0;
        $page->save();

        return to_route('admin.page.index')->withToastSuccess('Page inactivated successfully!!');
    }

    public function delete(Request $request, Page $page) {
        $page->delete();

        return to_route('admin.page.index')->withToastSuccess('Page deleted successfully!!');
    }

}
