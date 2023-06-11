<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Package;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnOrder;
use App\Models\ReturnPurchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function dashboard() {
        $data                 = [];
        $data['total_client'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->count();

        $data['active_client'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->whereDate('validity', '>=', today())
            ->where('status', 1)
            ->count();

        $data['inactive_client'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->whereDate('validity', '>=', today())
            ->where('status', 0)
            ->count();

        $data['expired_client'] = DB::table('users')
            ->whereRaw('users.id = users.user_id')
            ->whereDate('validity', '<=', today())
            ->count();

        $data['total_customer']    = Customer::count();
        $data['total_supplier']    = Supplier::count();
        $data['total_products']    = Product::count();
        $data['inactive_products'] = Product::where('quantity', '<=', 0)->count();

        $data['todays_order']           = Order::whereDate('created_at', '=', date("Y-m-d"))->count();
        $data['todays_return_order']    = ReturnOrder::whereDate('created_at', '=', date("Y-m-d"))->count();
        $data['todays_purchase']        = Purchase::whereDate('created_at', '=', date("Y-m-d"))->count();
        $data['todays_return_purchase'] = ReturnPurchase::whereDate('created_at', '=', date("Y-m-d"))->count();

        $data['countries'] = Country::orderBy('en_name', 'asc')
            ->with([
                'users' => function ($query) {
                    return $query->select(['id', 'country_id']);
                },
            ])->get();

        $data['packages'] = Package::with('subscriptions')->orderBy('en_name', 'asc')->get();

        return view('backend.dashboard', $data);
    }

}
