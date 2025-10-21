<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function list(Request $request): string
    {
        $users = User::all();

        return $users->toJson();
    }

    public function create(Request $request): string
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:user,admin',
        ]);

        $password = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
        ]);

        // Notification utilisateur
        $user->notify(new \App\Notifications\Core\CreateUserNotification($password));


        return $user->toJson();
    }

    public function update(Request $request, int $user_id): string
    {
        $user = User::findOrFail($user_id);

        if($request->query('blocked')) {
            try {
                $user->update([
                    'blocked' => $request->query('blocked'),
                ]);
            } catch (Exception $ex) {
                return $ex->getMessage();
            }
        } else {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                    'role' => 'required|string|max:255',
                ]);

                $user->update($validated);
            } catch (Exception $ex) {
                return $ex->getMessage();
            }
        }

        return $user->toJson();
    }

    public function delete(Request $request, int $user_id): string
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        return $user->toJson();
    }

    public function passwordReset(Request $request, int $user_id): string
    {
        $user = User::findOrFail($user_id);
        $password = Str::random(12);

        Auth::logout();

        $user->update([
            'password' => Hash::make($password),
        ]);

        // Notification utilisateur
        $user->notify(new \App\Notifications\Core\PasswordResetNotification($password));

        return $user->toJson();
    }
}
