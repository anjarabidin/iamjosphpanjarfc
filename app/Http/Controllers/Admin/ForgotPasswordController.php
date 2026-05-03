<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Models\Admin;
use App\Models\PersonalTrainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return view('admins.auth.forgot-password');
    }
    public function post(Request $request)
    {

        if ($request->type == 'ADMIN') {
            $admin = Admin::whereEmail($request->email)->first();
            $admin?->update([
                'token' => Str::random(50)
            ]);
            $admin?->refresh();
            $user = $admin;
        } else {
            $personalTrainer = PersonalTrainer::whereEmail($request->email)->first();
            $personalTrainer?->update([
                'token' => Str::random(50)
            ]);
            $personalTrainer?->refresh();
            $user = $personalTrainer;
        }

        if ($user) {
            Mail::send('emails.forgot_password', [
                'admin' => $user
            ], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Lupa Password');
            });
        }
        return view('admins.auth.success');
    }
    public function changePassword()
    {
        $tokenCheck = Admin::whereToken(request()->token)->whereNotNull('token')->exists();
        $type = 'ADMIN';
        if (!$tokenCheck) {
            $tokenCheck = PersonalTrainer::whereToken(request()->token)->whereNotNull('token')->exists();
            $type = 'PT';
        }
        if (!$tokenCheck) {
            return redirect(route('forgot-password'))->with('error', 'Tautan tidak valid atau token expired');
        }
        return view('admins.auth.change-password', compact('type'));
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        if ($request->type == 'ADMIN') {
            $admin = Admin::whereToken($request->token)->first();
            $admin->update([
                'password' => Hash::make($request->password),
                'token' => null
            ]);
            return back()->with(['success', 'Password berhasil direset, silakan login menggunakan password baru']);
        } else {
            $personalTrainer = PersonalTrainer::whereToken($request->token)->first();
            $personalTrainer->update([
                'password' => Hash::make($request->password),
                'token' => null
            ]);
            return back()->with(['success', 'Password berhasil direset, silakan login menggunakan password baru']);
        }
    }
}
