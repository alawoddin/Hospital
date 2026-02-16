<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function UserDashboard() {
        return view('backend.user.index');
    }

    //Logout function

    public function UserLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // End Logout

    // User Profile
    public function UserProfile(){
        $user = Auth::user();
        return view('backend.user.profile.user_profile', compact('user'));
    }
    // End User Profile
  
    // Update Profile
    public function UpdateUserProfile(Request $request) {

        $user = Auth::user();

       if ($request->file('photo')) {

            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/user/profile/'.$name_gen));
            $save_url = 'upload/user/profile/'.$name_gen;

            $user->photo = $save_url;
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->role = $request->role;

        $user->save();
        return redirect()->route('user.profile');
    }
    // End Update Profile

}
