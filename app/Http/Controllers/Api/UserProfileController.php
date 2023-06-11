<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserRoleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller {
    public function user() {
        $user = User::where('id', auth()->user()->id)->with('role', 'country', 'state', 'policeStation', 'businessType', 'userRole.userRoleAccess')->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'user'   => $user,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Nothing found!!',
            ]);
        }

    }

    public function userList() {
        $user_list = User::where('user_id', auth()->user()->user_id)
            ->with('role', 'businessType', 'country', 'state', 'policeStation', 'userRole.userRoleAccess')
            ->get();

        return response()->json([
            'status'    => true,
            'user_list' => $user_list,
        ]);
    }

    public function userDetails(Request $request) {
        $user = User::where('id', $request->id)
            ->where('user_id', auth()->user()->user_id)
            ->with('role', 'businessType', 'country', 'state', 'policeStation')
            ->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'user'   => $user,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Nothing found!!',
            ]);
        }

    }

    //user acess
    public function rr(Request $request) {
        $user_role = UserRole::where('user_id', 1)->with('userRoleAccess')->get();

        foreach ($user_role as $role) {

            foreach ($role->userRoleAccess as $access) {
                $access->delete();
            }

            $role->delete();
        }

        foreach ($request->name as $key => $item) {
            $user_role          = new UserRole();
            $user_role->user_id = 1;
            $user_role->name    = $item;
            $user_role->save();

            UserRoleAccess::create([
                'user_role_id' => $user_role->id,
                'add'          => $request->access[$key]["add"],
                'view'         => $request->access[$key]["view"],
                'edit'         => $request->access[$key]["edit"],
                'delete'       => $request->access[$key]["delete"],
                'share'        => $request->access[$key]["share"],
            ]);

        }

        return UserRole::where('user_id', 1)->with('userRoleAccess')->get();
    }

    public function store(Request $request) {

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'phone'    => 'required',
                'email'    => 'required|unique:users',
                'password' => 'required|min:8',
                'image'    => 'nullable|image|mimes:jpeg,jpg,png,gif',
            ]);

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/user/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

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

            $user = new User();

            $user->name              = $request->name;
            $user->role_id           = $request->role_id;
            $user->phone             = $request->phone;
            $user->email             = $request->email;
            $user->password          = bcrypt($request->password);
            $user->business_name     = auth()->user()->business_name;
            $user->business_type_id  = auth()->user()->business_type_id;
            $user->business_address  = auth()->user()->business_address;
            $user->country_id        = auth()->user()->country_id;
            $user->state_id          = auth()->user()->state_id;
            $user->police_station_id = auth()->user()->police_station_id;
            $user->user_id           = auth()->user()->user_id;
            $user->validity          = auth()->user()->validity;
            $user->reference_id      = bin2hex($bytes);
            $user->ip                = getIPAddress();
            $user->image             = $final_name1 ?? '';
            $user->save();

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'New user <b>' . $user->name . ' added';
            $notification->save();

            if ($request->role_name) {
                //user access role
                $user_role = UserRole::where('user_id', $user->id)
                    ->with('userRoleAccess')
                    ->get();

                /**
                 * deleting previous role access
                 */

                if ($user_role) {

                    foreach ($user_role as $role) {

                        foreach ($role->userRoleAccess as $access) {
                            $access->delete();
                        }

                        $role->delete();
                    }

                }

                /**
                 * store new user role access
                 */

                foreach ($request->role_name as $key => $item) {
                    $user_role          = new UserRole();
                    $user_role->user_id = $user->id;
                    $user_role->name    = $item;
                    $user_role->save();

                    UserRoleAccess::create([
                        'user_role_id' => $user_role->id,
                        'add'          => $request->access[$key]["add"],
                        'view'         => $request->access[$key]["view"],
                        'edit'         => $request->access[$key]["edit"],
                        'delete'       => $request->access[$key]["delete"],
                        'share'        => $request->access[$key]["share"],
                    ]);

                }

            }

            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => 'Profile created successfully!!',
                'user'      => $user,
                'user_role' => UserRole::where('user_id', $user->id)
                    ->with('userRoleAccess')
                    ->get(),
            ]);
        } catch (\Throwable$th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function update(Request $request) {
        DB::beginTransaction();

        try {
            $user      = User::findOrFail($request["id"]);
            $validator = Validator::make($request->all(), [
                'name'          => 'required',
                'business_name' => 'required',
                'phone'         => 'required',
                'email'         => 'required|unique:users,email,' . $user->id,
                'address'       => 'required',
                'image'         => 'nullable|image|mimes:jpeg,jpg,png,gif',
            ]);

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $image_path = public_path($user->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/user/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $user->image = $final_name1;
                    $user->save();
                }

            }

            $user->name              = $request["name"];
            $user->role_id           = $request["role_id"];
            $user->business_name     = $request["business_name"];
            $user->business_type_id  = $request["business_type_id"];
            $user->phone             = $request["phone"];
            $user->email             = $request["email"];
            $user->business_address  = $request["business_address"];
            $user->country_id        = $request["country_id"];
            $user->state_id          = $request["state_id"];
            $user->police_station_id = $request["police_station_id"];
            $user->save();

            if ($request["password"]) {
                $user->password = bcrypt($request["password"]);
                $user->save();
            }

            if ($request["role_name"]) {
                //user access role
                $user_role = UserRole::where('user_id', $user->id)
                    ->with('userRoleAccess')
                    ->get();

                /**
                 * deleting previous role access
                 */

                if ($user_role) {

                    foreach ($user_role as $role) {

                        foreach ($role->userRoleAccess as $access) {
                            $access->delete();
                        }

                        $role->delete();
                    }

                }

                /**
                 * store new user role access
                 */

                foreach ($request->role_name as $key => $item) {
                    $user_role          = new UserRole();
                    $user_role->user_id = $user->id;
                    $user_role->name    = $item;
                    $user_role->save();

                    UserRoleAccess::create([
                        'user_role_id' => $user_role->id,
                        'add'          => $request->access[$key]["add"],
                        'view'         => $request->access[$key]["view"],
                        'edit'         => $request->access[$key]["edit"],
                        'delete'       => $request->access[$key]["delete"],
                        'share'        => $request->access[$key]["share"],
                    ]);

                }

            }

            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => 'Profile updated successfully!!',
                'user'      => $user,
                'user_role' => UserRole::where('user_id', $user->id)
                    ->with('userRoleAccess')
                    ->get(),
            ]);
        } catch (\Throwable$th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function delete($id) {

        if (auth()->user()->id != auth()->user()->user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denide to perform this operation!!',
            ]);
        }

        $user = User::where('id', $id)->where('user_id', auth()->user()->user_id)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);

        } else {
            $user_role = UserRole::where('user_id', $user->id)
                ->with('userRoleAccess')
                ->get();

            /**
             * deleting previous role access
             */

            if ($user_role) {

                foreach ($user_role as $role) {

                    foreach ($role->userRoleAccess as $access) {
                        $access->delete();
                    }

                    $role->delete();
                }

            }

            $notification          = new Notification();
            $notification->user_id = auth()->user()->user_id;
            $notification->name    = 'User <b>' . $user->name . ' deleted';
            $notification->save();
            $user->delete();

            return response()->json([
                'status'  => true,
                'message' => 'User deleted successfully!!',
            ]);
        }

    }

    public function active(Request $request) {

        DB::beginTransaction();

        if (auth()->user()->id != auth()->user()->user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        }

        $user = User::where('id', $request->id)->where('user_id', auth()->user()->user_id)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        } else {
            try {
                $user->status = 1;
                $user->save();
                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'User activated successfully!!',
                ]);

            } catch (\Throwable$th) {
                DB::rollBack();

                return response()->json([
                    'status'  => false,
                    'message' => $th,
                ]);
            }

        }

    }

    public function inactive(Request $request) {
        DB::beginTransaction();

        if (auth()->user()->id != auth()->user()->user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        }

        $user = User::where('id', $request->id)->where('user_id', auth()->user()->user_id)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!!',
            ]);
        } else {
            try {
                $user->status = 0;
                $user->save();
                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'User inactivated successfully!!',
                ]);

            } catch (\Throwable$th) {
                DB::rollBack();

                return response()->json([
                    'status'  => false,
                    'message' => $th,
                ]);
            }

        }

    }

}
