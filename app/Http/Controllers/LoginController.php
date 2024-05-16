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


    private function generateRandomPKey() {
        // Generate a random 16-digit number as p_key
        $p_key = str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
        return $p_key;
    }

    private function generateAKey($userId, $p_key, $encrKey) {
        // Generate a_key based on userId, p_key, and encrKey
        $a_key = md5(($userId + $p_key) . $encrKey);
        return $a_key;
    }

    function sendSms($mobile, $sms) {
        $url = 'http://bulksms1.teletalk.com.bd:8091/jlinktbls.php';

        // Hash 'pass' value using md5
        $pass = md5('@7654321@');

        // Generate a random p_key
        $p_key = $this->generateRandomPKey();

        // Set userId
        $userId = '122'; // Assuming this is the userId

        // Set encrKey
        $encrKey = '***!'; // Your encryption key

        // Generate a_key based on userId, p_key, and encrKey
        $a_key = $this->generateAKey($userId, $p_key, $encrKey);

        $data = array(
            'op' => 'SMS',
            'chunk' => 'V',
            'user' => 'Parliament',
            'pass' => $pass,
            'servername' => 'bulksms1.teletalk.com.bd',
            'smsclass' => 'GENERAL',
            'sms' => $sms,
            'sms_id' => '122',
            'mobile' => $mobile,
            'charset' => 'ASCII|UTF-8',
            'validity' => '1440',
            'a_key' => $a_key,
            'p_key' => $p_key,
            'cid' => '1234567890'
        );

        $payload = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
        );

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}
