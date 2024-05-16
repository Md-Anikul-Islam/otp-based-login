<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'numeric']
        ]);
        $user = User::where('phone_number', $request->phone_number)->first();
        if (!$user) {
            $user = User::create([
                'phone_number' => $request->phone_number,
                'name' => $request->name
            ]);
        }
        $otp = mt_rand(100000, 999999);
        $user->update(['otp' => $otp, 'otp_expiry' => now()->addMinutes(5)]);
        // Send OTP via SMS here
        $this->sendSms($request->phone_number, 'Your OTP is: ' . $otp);


        return redirect()->route('otp.verify');
    }

    public function showOtpVerificationForm()
    {
        return view('auth.verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric']
        ]);
        $user = User::where('otp', $request->otp)
            ->where('otp_expiry', '>=', now())
            ->first();
        if (!$user) {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.']);
        }
        Auth::login($user);
        return redirect()->intended('/dashboard');
    }

    private function sendSms($mobile, $sms)
    {
        $url = 'http://bulksms.teletalk.com.bd/link_sms_send.php?' . http_build_query([
                'op'      => 'SMS',
                'user'    => env('SMS_API_USERNAME', 'Parliament'),
                'pass'    => env('SMS_API_PASSWORD', ''),
                'mobile'  => $mobile,
                'charset' => 'UTF-8',
                'sms'     => $sms
            ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
