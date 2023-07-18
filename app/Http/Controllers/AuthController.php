<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorEmail;
use App\Models\AlternativeEmail;
use App\Models\AlternativeEmails;
use App\Models\TwoFactorAuthCodes;
use App\Models\User;
use App\Models\VerificationCode;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    //

    /**
     * @throws Exception
     */
    public function phoneVerification(Request $request): JsonResponse|RedirectResponse
    {
        $verification_code = random_int(100000, 999999);
        $Auth_ID = Auth::user()->id;
        $receiverNumber = $request->Phone_Number;
        $message = "Your Phone Verification Code is: " . $verification_code;
        if (!empty($receiverNumber)) {
            try {
                $account_sid = env("TWILIO_SID");
                $auth_token = env("TWILIO_TOKEN");
                $twilio_number = env("TWILIO_FROM");

                $client = new Client($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number,
                    'body' => $message
                ]);
                VerificationCode::create([
                    'user_id' => $Auth_ID,
                    'two_factor_code' => $verification_code
                ]);
                return redirect()->route('Code.Verify', ['User_ID' => $Auth_ID])->with('Success!', 'Code Sent!. Check Your Phone Verification Code is Sent!');

            } catch (Exception $e) {
                return response()->json(['Error!' => $e->getMessage()]);
            }
        }
        return response()->json(['Error!' => 'Phone Required!']);
    }

    public function codeVerifyView(Request $request): Factory|View|Application
    {
        $User_ID = $request->User_ID;
        return view('auth.phone-verify', compact('User_ID'));
    }

    public function codeVerification(Request $request): RedirectResponse
    {
        $code_verify = VerificationCode::where('user_id', $request->User_ID)->latest('id')->first();
        if (($code_verify->two_factor_code === (int)$request->two_factor_code) && $code_verify->delete()) {
            User::where('id', $request->User_ID)
                ->update([
                    'is_phone_verified' => 1
                ]);
            return redirect()->route('home')->with('success', 'Your Phone Verified Successfully!');
        }
        return back()->with('error', 'Invalid Code!!');
    }

    public function emailVerification(Request $request): JsonResponse|RedirectResponse
    {
        $verification_code = random_int(100000, 999999);
        $Auth_ID = Auth::user()->id;
        $mailData = [
            'title' => 'Verification Email Code',
            'body' => 'This is for verification email for 2factor authentication.',
            'code' => $verification_code
        ];
        if (!empty($request->Email_Address)) {
            VerificationCode::create([
                'user_id' => $Auth_ID,
                'two_factor_code' => $verification_code
            ]);
            AlternativeEmail::create([
                'user_id' => $Auth_ID,
                'email' => $request->Email_Address,
            ]);
            Mail::to($request->Email_Address)->send(new TwoFactorEmail($mailData));
            return redirect()->route('Email.Code.Verify', ['User_ID' => $Auth_ID])->with('Success!', 'Email Sent!. Check Your Email Verification Code is Sent!');
        }
        return response()->json(['Error!' => 'Email Required!']);
    }

    public function codeEmailVerifyView(Request $request): Factory|View|Application
    {
        $User_ID = $request->User_ID;
        return view('auth.email-verify', compact('User_ID'));
    }

    public function alternateEmailVerification(Request $request): RedirectResponse
    {
        $code_verify = VerificationCode::where('user_id', $request->User_ID)->latest('id')->first();
        if (($code_verify->two_factor_code === (int)$request->two_factor_code) && $code_verify->delete()) {
            AlternativeEmail::where('user_id', $request->User_ID)
                ->where('email', $request->email)
                ->update([
                    'is_verified' => 1
                ]);
            return redirect()->route('home')->with('success', 'Your Alternative Email Verified Successfully!');
        }
        return back()->with('error', 'Invalid Code!!');
    }
}
