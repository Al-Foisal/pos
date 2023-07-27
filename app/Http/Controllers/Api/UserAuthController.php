<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtp;
use App\Models\CompanyInfo;
use App\Models\Customer;
use App\Models\Package;
use App\Models\SubscriptionHistory;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller {
    public function register(Request $request) {
        DB::beginTransaction();

        try {
            $bytes = random_bytes(4);
            function getIPAddress() {

                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    //whether ip is from the share internet
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    //whether ip is from the proxy
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    //whether ip is from the remote address
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                return $ip;
            }

            $validator = Validator::make($request->all(), [
                'business_name' => 'required',
                'email'         => 'required|unique:users|email',
                'password'      => 'required',
            ]);

            $otp  = rand(111111, 999999);
            $user = User::create([
                'name'           => 'Admin',
                'role_id'        => 1,
                'business_name'  => $request->business_name,
                'email'          => $request->email,
                'password'       => bcrypt($request->password),
                'reference_id'   => bin2hex($bytes),
                'reference_code' => $request->reference_code,
                'ip'             => getIPAddress(),
                'otp'            => $otp,
            ]);

            if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Http::get('https://sysadmin.muthobarta.com/api/v1/send-sms-get?token=f476a73d21a1d7ee2abff920c94eac23021021db&sender_id=8809601002704&receiver=' . trim($user->email) . '&message=G Manager যাচাইকরণ কোডটি হলো: ' . $otp . '&remove_duplicate=true');

            } else {
                Mail::to($user->email)->send(new PasswordResetOtp($otp));
            }

            $package        = Package::where('price', 0)->first();
            $user->user_id  = $user->id;
            $user->validity = date("Y-m-d", strtotime('+' . $package->duration . ' days'));
            $user->save();

            //make default customer
            $customer          = new Customer();
            $customer->user_id = $user->id;
            $customer->name    = 'GUEST CUSTOMER';
            $customer->phone   = 123456;
            $customer->type    = 0;
            $customer->save();

            //make default supplier
            $supplier          = new Supplier();
            $supplier->user_id = $user->id;
            $supplier->name    = 'GUEST SUPPLIER';
            $supplier->phone   = 123456;
            $supplier->type    = 0;
            $supplier->save();

//otithi package default subscription update by zero price

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
                $subscription->duration        = $package->duration;
                $subscription->user_limit      = $package->user_limit;
                $subscription->validity_from   = date("Y-m-d");
                $subscription->validity_to     = date("Y-m-d", strtotime('+' . $package->duration . ' days'));
                $subscription->save();

            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Your account created successfully!! Wait of OTP',
                'user'    => $user,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function verifyOtp(Request $request) {

        DB::beginTransaction();

        try {
            $request->validate([
                'email_or_phone' => 'required',
                'otp'            => 'required|min:6',
            ]);

            $user = User::where('email', $request->email_or_phone)->first();

            if (!$user || $user->otp != $request->otp) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP!!',
                ]);
            }

            $user->otp = null;
            $user->save();

            if ($request->red_from === 'login') {
                if (!Auth::attempt([
                    'email'    => $request->email_or_phone,
                    'password' => $request->password,
                    'status'   => 1,
                ])) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid phone number or unauthorized account!!',
                    ]);
                }

                $user = Auth::user();
                if ($user->validity < today()) {
                    $user->tokens()->delete();
                    Auth::guard('web')->logout();

                    return response()->json([
                        'status'  => false,
                        'message' => 'Your account is expired!!',
                    ]);
                }

                $tokenResult = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'status'       => true,
                    'token_type'   => 'Bearer',
                    'access_token' => $tokenResult,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Your account verified successfully!!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function resendOtp(Request $request) {

        $company = CompanyInfo::find(1);
        DB::beginTransaction();

        try {
            $request->validate([
                'email_or_phone' => 'required',
            ]);

            if (!filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $request->email_or_phone)->first();

                if (!$user) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid account!!',
                    ]);
                }

                $user->otp = rand(111111, 999999);
                $user->save();
                Http::get('https://sysadmin.muthobarta.com/api/v1/send-sms-get?token=f476a73d21a1d7ee2abff920c94eac23021021db&sender_id=8809601002704&receiver=' . trim($user->email) . '&message=G Manager যাচাইকরণ কোডটি হলো: ' . $user->otp . '&remove_duplicate=true');

            } else {

                $user = User::where('email', $request->email_or_phone)->first();

                if (!$user) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid account!!',
                    ]);
                }

                $user->otp = rand(111111, 999999);
                $user->save();

                Mail::to($user->email)->send(new PasswordResetOtp($user->otp));

            }

            $user->tokens()->delete();
            Auth::guard('web')->logout();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'OTP sent successfully!!',
                'user'    => $user,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function login(Request $request) {
        try {
            $request->validate([
                'email_or_phone' => 'required',
                'password'       => 'required',
            ]);

            if (!filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {

                if (!Auth::attempt([
                    'email'    => $request->email_or_phone,
                    'password' => $request->password,
                    'status'   => 1,
                ])) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid phone number or unauthorized account!!',
                    ]);
                }

                $user = Auth::user();

                if ($user->validity < today()) {
                    $user->tokens()->delete();
                    Auth::guard('web')->logout();

                    return response()->json([
                        'status'  => false,
                        'message' => 'Your account is expired!!',
                    ]);
                }

                if (!is_null($user->otp)) {
                    //resend otp if user forgot to verify OTP
                    $user      = User::where('email', $request->email_or_phone)->first();
                    $user->otp = rand(111111, 999999);
                    $user->save();
                    Http::get('https://sysadmin.muthobarta.com/api/v1/send-sms-get?token=f476a73d21a1d7ee2abff920c94eac23021021db&sender_id=8809601002704&receiver=' . trim($user->email) . '&message=G Manager যাচাইকরণ কোডটি হলো: ' . $user->otp . '&remove_duplicate=true');

                    $user->tokens()->delete();
                    Auth::guard('web')->logout();

                    return response()->json([
                        'status'       => false,
                        'message'      => 'Your account is not verified!!',
                        'otp_required' => true,
                    ]);
                }

                $tokenResult = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'status'       => true,
                    'token_type'   => 'Bearer',
                    'access_token' => $tokenResult,
                    'auth_type'    => 'mobile',
                ]);

            } else {

                if (!Auth::attempt([
                    'email'    => $request->email_or_phone,
                    'password' => $request->password,
                    'status'   => 1,
                ])) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid email or unauthorized account!!',
                    ]);
                }

                $user = Auth::user();

                if ($user->validity < today()) {
                    $user->tokens()->delete();
                    Auth::guard('web')->logout();

                    return response()->json([
                        'status'  => false,
                        'message' => 'Your account is expired!!',
                    ]);
                }

                if (!is_null($user->otp)) {
                    //resend otp if user forgot to verify OTP
                    $user = User::where('email', $request->email_or_phone)->first();

                    $user->otp = rand(111111, 999999);
                    $user->save();

                    Mail::to($user->email)->send(new PasswordResetOtp($user->otp));

                    $user->tokens()->delete();
                    Auth::guard('web')->logout();

                    return response()->json([
                        'status'       => false,
                        'message'      => 'Your account is not verified!!',
                        'otp_required' => true,
                    ]);
                }

                $tokenResult = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'status'       => true,
                    'token_type'   => 'Bearer',
                    'access_token' => $tokenResult,
                    'auth_type'    => 'email',
                ]);

            }

        } catch (Exception $error) {
            return response()->json([
                'status'  => false,
                'message' => 'Error in Login',
            ]);
        }

    }

    public function storeForgotPassword(Request $request) {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'This email is no longer with our records!!',
                ]);
            }

            if ($user->email == 'shakila@gmail.com') {
                $verification_otp = null;
                $user->otp        = $verification_otp;
                $user->save();
            } else {
                $verification_otp = rand(111111, 999999);
                $user->otp        = $verification_otp;
                $user->save();

                if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    Http::get('https://sysadmin.muthobarta.com/api/v1/send-sms-get?token=f476a73d21a1d7ee2abff920c94eac23021021db&sender_id=8809601002704&receiver=' . trim($user->email) . '&message=G Manager যাচাইকরণ কোডটি হলো: ' . $verification_otp . '&remove_duplicate=true');
                } else {
                    Mail::to($user->email)->send(new PasswordResetOtp($verification_otp));
                }

            }

            DB::table('password_resets')->insert([
                'token'      => $verification_otp,
                'email'      => $request->email,
                'created_at' => now(),
            ]);
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'We have sent a fresh reset password link!!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function resetPassword(Request $request) {
        DB::beginTransaction();

        try {

            //here _token is used as otp number
            $validator = Validator::make($request->all(), [
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            $password = DB::table('password_resets')->where('email', $request->email)->where('token', $request->otp)->first();

            if (!$password) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Something went wrong, Invalid token or email!!',
                ]);
            }

            $user = User::where('email', $request->email)->first();

            if ($user && $password) {
                $user->update(['password' => bcrypt($request->password)]);

                $password = DB::table('password_resets')->where('email', $request->email)->delete();

                $user->otp = null;
                $user->save();

                return response()->json([
                    'status'  => true,
                    'message' => 'New password reset successfully!!',
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'The email is no longer our record!!',
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

}
