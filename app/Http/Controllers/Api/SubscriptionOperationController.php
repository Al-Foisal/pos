<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionReminder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionOperationController extends Controller {
    public function packageList(Request $request) {
        $data = Package::where('status', 1)->where('price', '>', 0)->with('packageFeatures')->get();

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function packageFeature(Request $request) {
        $data = PackageFeature::where('status', 1)->get();

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function packageDetails(Request $request) {
        $data = Package::where('id', $request->package_id)->with('packageFeatures')->first();

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function packageSubscription(Request $request) {
        DB::beginTransaction();

        try {
            $package = Package::findOrFail($request->package_id);

            if (!$package || auth()->user()->id != auth()->user()->user_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Something went wrong!!',
                ]);
            }

            if ($package->discount > 0) {
                $price = $package->discount_price;
            } else {
                $price = $package->price;
            }

            $subscription                  = new SubscriptionHistory();
            $subscription->user_id         = auth()->user()->user_id;
            $subscription->package_id      = $package->id;
            $subscription->en_package_name = $package->en_name;
            $subscription->bn_package_name = $package->bn_name;
            $subscription->price           = $price;
            $subscription->duration        = $package->duration;
            $subscription->user_limit      = $package->user_limit;
            $subscription->validity_from   = date("Y-m-d");
            $subscription->validity_to     = date("Y-m-d", strtotime('+' . $package->duration . ' days'));
            $subscription->save();

// $notification          = new Notification();

// $notification->user_id = auth()->user()->user_id;

// $notification->name    = 'Store subscription migrated to <b>' . $subscription->en_name . '</b>';
            // $notification->save();

            $users = User::where('user_id', auth()->user()->user_id)->get();

            foreach ($users as $user) {
                $user->update([
                    'validity' => date("Y-m-d", strtotime('+' . $package->duration . ' days')),
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Your subscription successfull!!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function subscriptionHistory(Request $request) {
        $data = SubscriptionHistory::where('user_id', auth()->user()->user_id)->orderBy('id', 'DESC')->with(['user' => function ($query) {
            return $query->select(['id', 'business_name']);
        },
            'subscriptionReminder',
        ])->get();

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function presentSubscription(Request $request) {
        $data = SubscriptionHistory::where('user_id', auth()->user()->user_id)->orderBy('id', 'DESC')->with([
            'subscriptionReminder',
            'user' => function ($query) {
                return $query->select(['id', 'business_name']);
            },
        ])->first();

        if ($data) {
            return response()->json([
                'status'  => true,
                'message' => 'Data found successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'No data found',
                'data'    => $data,
            ]);
        }

    }

    public function setReminder(Request $request) {

        $subscription = SubscriptionHistory::find($request->id);

        $now         = time(); // or your date as well
        $validity_to = strtotime($subscription->validity_to);
        $datediff    = $validity_to - $now;

        $remain_days = round($datediff / (60 * 60 * 24));

        if ($remain_days < $request->duration) {
            return response()->json([
                'status'  => false,
                'message' => 'Your reminder days is less than package expiration days!!',
            ]);
        } else {
            DB::beginTransaction();

            try {
                SubscriptionReminder::updateOrCreate(
                    [
                        'subscription_history_id' => $request->id,
                    ],
                    [
                        'subscription_history_id' => $request->id,
                        'duration'                => $request->duration,
                        'reminder'                => $request->reminder,
                    ]
                );

                $notification          = new Notification();
                $notification->user_id = auth()->user()->user_id;
                $notification->name    = 'Reminder <b>' . $request->reminder . '</b> times brfore <b>' . $request->duration . '</b> days of expiration';
                $notification->save();

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Your subscription reminder created successfully!!',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status'  => false,
                    'message' => $th,
                ]);
            }

        }

    }

}
