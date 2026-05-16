<?php

namespace Modules\Auth\Controllers;

use Modules\Auth\Services\AuthService;
use Modules\Auth\Requests\LoginRequest;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\ApiController;
use Modules\Auth\Models\TokenAuditLog;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Requests\UpdatePasswordRequest;
use Modules\Auth\Requests\UpdateProfileRequest;
use Throwable;

class AuthenticationController extends ApiController
{
    public function __construct(
        protected AuthService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function authenticate(LoginRequest $request)
    {
        try {

            DB::beginTransaction();

            $data = $this->service->login(
                $request->validated(),
                $request->ip(),
                $request->header('User-Agent')
            );

            DB::commit();

            return $this->success(
                $data,
                'Login successful.'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
                $e->getMessage(),
                401
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        try {

            DB::beginTransaction();

            $user = $request->user();

            $user->tokens()->delete();

            DB::commit();

            return $this->success(
                null,
                'Logged out successfully.'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
                $e->getMessage()
            );
        }
    }

    public function refresh(Request $request)
    {
        try {

            DB::beginTransaction();

            $request->validate([
                'refresh_token' => ['required', 'string']
            ]);

            $token = PersonalAccessToken::findToken(
                $request->refresh_token
            );

            if (!$token || !$token->can('refresh')) {
                throw new \Exception('Invalid refresh token.');
            }

            if ($token->expires_at && $token->expires_at->isPast()) {
                $token->delete();
                throw new \Exception('Refresh token expired.');
            }

            $user = $token->tokenable;

            /*
            |--------------------------------------------------------------------------
            | Token Rotation
            |--------------------------------------------------------------------------
            */

            $token->delete();

            $user->tokens()
                ->where('name', 'access-token')
                ->delete();

            $newAccessToken = $user->createToken(
                'access-token',
                ['api'],
                now()->addMinutes(30)
            );

            $newRefreshToken = $user->createToken(
                'refresh-token',
                ['refresh'],
                now()->addDays(7)
            );

            TokenAuditLog::create([
                'user_id' => $user->id,
                'action' => 'refresh',
                'ip_address' => $request->ip(),
                'device' => $request->header('User-Agent')
            ]);

            DB::commit();

            return $this->success([
                'token' => $newAccessToken->plainTextToken,
                'access_token' => $newAccessToken->plainTextToken,
                'refresh_token' => $newRefreshToken->plainTextToken,
                'token_type' => 'Bearer'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error($e->getMessage(), 401);
        }
    }

    public function me()
    {
        $user = auth_user();

        return $this->success(
            $this->service->getAuthenticatedUser($user)
        );
    }

    /*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

    public function profile()
    {
        $user = auth_user();

        return $this->success(
            $this->service->getAuthenticatedUser($user)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Profile
    |--------------------------------------------------------------------------
    */

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = auth_user();

            $user = $this->service->updateProfile(
                $user,
                $request->validated()
            );

            DB::commit();

            return $this->success(
                $this->service->getAuthenticatedUser($user),
                'Profile updated successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Password
    |--------------------------------------------------------------------------
    */

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = auth_user();

            $this->service->updatePassword(
                $user,
                $request->validated()
            );

            DB::commit();

            return $this->success(
                null,
                'Password updated successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
