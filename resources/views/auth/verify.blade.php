<!-- resources/views/auth/verify.blade.php -->

<form method="POST" action="{{ route('otp.verify.submit') }}">
    @csrf
    <div>
        <label for="otp">OTP</label>
        <input id="otp" type="text" name="otp" required>
        @error('otp')
        <span>{{ $message }}</span>
        @enderror
    </div>
    <input type="hidden" name="phone_number" value="{{ session('phone_number') }}">
    <button type="submit">Verify OTP</button>
</form>
