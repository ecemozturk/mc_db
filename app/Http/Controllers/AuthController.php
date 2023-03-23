<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserAbility;
use PragmaRX\Google2FALaravel\Google2FA;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;

    class AuthController extends Controller
{

    //QR
        public function generateQRCode(Request $request)
        {
            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->google2fa_secret) {
                return response()->json([
                    'message' => 'User not found or 2FA secret not set.',
                ], 404);
            }

            $google2fa = new Google2FA();
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'), // Your app name
                $user->email,
                $user->google2fa_secret
            );

            $renderer = new Png();
            $renderer->setWidth(256);
            $renderer->setHeight(256);

            $writer = new Writer($renderer);
            $qrCodeImage = $writer->writeString($qrCodeUrl);

            // Encode the QR code image as a base64 string
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImage);

            return response()->json([
                'message' => 'QR code generated.',
                'qrCode' => $qrCodeBase64,
            ]);
        }

        //QR Finish


    //two factor

        // ...

        public function enableTwoFactor(Request $request)
        {
            $user = Auth::user();
            $google2fa = new Google2FA();

            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();

            $QRImage = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );

            return response()->json([
                'message' => 'Two-factor authentication enabled.',
                'QRImage' => $QRImage,
                'secret' => $user->google2fa_secret
            ]);
        }

        public function disableTwoFactor(Request $request)
        {
            $user = Auth::user();
            $user->google2fa_secret = null;
            $user->save();

            return response()->json([
                'message' => 'Two-factor authentication disabled.'
            ]);
        }

        public function verifyTwoFactor(Request $request)
        {
            $request->validate([
                'otp' => 'required',
            ]);

            $user = Auth::user();
            $google2fa = new Google2FA();

            $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

            if ($valid) {
                return response()->json([
                    'message' => 'Two-factor authentication verified.',
                ]);
            } else {
                return response()->json([
                    'message' => 'Invalid OTP.',
                ], 400);
            }
        }


        //Two factor finished





    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
//            'role' => 'required|string',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
          //  'role' => $request->role,

        ]);





        if ($user->save()) {
            // Assign the submitted role
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->attach($role);
            } else {
                // If the submitted role is not in the database, you can either create a new role or assign a default role
            }

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'message' => 'Successfully created user!',
                'accessToken' => $token,
            ], 201);
        } else {
            return response()->json(['error' => 'Provide proper details']);
        }
    }

//LOGIN

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
            'role' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->google2fa_secret) {
            return response()->json([
                'message' => 'Two-factor authentication required.',
                'twoFactorRequired' => true,
            ]);
        }

        $credentials = request(['email','password']);
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }



        // Retrieve the authenticated user with their role and permissions
        $user = User::with('roles.permissions')
            ->find(Auth::id());

        // Format the userAbilities array
        $userAbilities = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $userAbilities[] = [
                    'action' => $permission->action,
                    'subject' => $permission->subject,
                ];
            }
        }

        $user = User::with('roles.permissions')
            ->find(Auth::id());

        // Remove password from userData
        $user->makeHidden('password');

        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'userData' => $user,
        ]);


    }

    //LOGIN FNSH

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);

    }

    // Import the necessary classes at the top of your controller

// Add this method to your AuthController
    public function getUserData()
    {
        // Retrieve the authenticated user with their roles and permissions
        $user = User::with('roles.permissions')
            ->find(Auth::id());

        // Format the userAbilities array
        $userAbilities = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $userAbilities[] = [
                    'action' => $permission->action,
                    'subject' => $permission->subject,
                ];
            }
        }

        // Debugging the userAbilities
        error_log('User Abilities: ' . json_encode($userAbilities));

        return response()->json([
            'userData' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')
            ],
            'userAbilities' => $userAbilities
        ]);
    }

    public function getAllAbilitiesAttribute()
    {
        $abilities = [];
        $roles = $this->roles()->with('permissions')->get();

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $abilities[$permission->subject] = $permission->action;
            }
        }

        return $abilities;
    }




//Password Change

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Incorrect current password'], 401);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }




}
