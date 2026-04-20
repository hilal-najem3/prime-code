<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Auth;
// use Modules\Auth\Models\User;
use Modules\Auth\Models\TokenAuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthService
{
    /*
    |--------------------------------------------------------------------------
    | Authenticate User
    |--------------------------------------------------------------------------
    */

    public function login(array $credentials, string $ip, string $device)
    {
        try {
            Log::info("Authenticating user with email: {$credentials['email']}");

            if (!Auth::attempt($credentials)) {
                Log::warning("Failed login attempt for email: {$credentials['email']} from IP: {$ip}");
                throw new Exception('Invalid credentials.');
            }

            Log::info("User authenticated: {$credentials['email']}");
            /** @var \Modules\Auth\Models\User $user */
            $user = Auth::user();
            Log::info("User authenticated: {$user->email} (ID: {$user->id})");

            if (!$user->enabled) {
                throw new Exception('User account is disabled.');
            }

            DB::beginTransaction();

            $permissions = $user->getCachedPermissions();

            /*
            |--------------------------------------------------------------------------
            | Device Limit
            |--------------------------------------------------------------------------
            */

            if (env('APP_ENV') === 'production') {
                $deviceLimit = 5;

                if ($user->tokens()->count() >= ($deviceLimit * 2)) {
                    throw new Exception('Device limit reached.');
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Access Token
            |--------------------------------------------------------------------------
            */

            $accessToken = $user->createToken(
                'access-token',
                ['api'],
                now()->addMinutes(30)
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh Token
            |--------------------------------------------------------------------------
            */

            $refreshToken = $user->createToken(
                'refresh-token',
                ['refresh'],
                now()->addDays(7)
            );

            /*
            |--------------------------------------------------------------------------
            | Token Audit Log
            |--------------------------------------------------------------------------
            */

            TokenAuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => $ip,
                'device' => $device
            ]);

            DB::commit();

            return [
                'user' => $user,
                'role' => optional($user->roles()->first())->slug,
                'permissions' => $permissions,
                'token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'token_type' => 'Bearer'
            ];
        } catch (Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public function getAuthenticatedUser(\Modules\Auth\Models\User $user): array
    {
        return [
            'user' => $user->load('roles'),
            'role' => optional($user->roles()->first())->slug,
            'permissions' => $user->getCachedPermissions(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Update Profile
    |--------------------------------------------------------------------------
    */

    public function updateProfile($user, array $data)
    {
        $user->update($data);

        return $user->load('roles');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Password
    |--------------------------------------------------------------------------
    */

    public function updatePassword($user, array $data)
    {
        $user->update([
            'password' => $data['password'], // auto hashed
        ]);

        /*
    |--------------------------------------------------------------------------
    | Security: logout all devices
    |--------------------------------------------------------------------------
    */

        $user->tokens()->delete();
    }
}
