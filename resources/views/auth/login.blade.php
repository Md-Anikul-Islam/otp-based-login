<!-- resources/views/auth/login.blade.php -->

<form method="POST" action="{{ route('login.sendOtp') }}">
    @csrf
    <div>
        <label for="phone_number">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        <label for="phone_number">Phone Number</label>
        <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" required>
        @error('phone_number')
        <span>{{ $message }}</span>
        @enderror
    </div>
    <button type="submit">Send OTP</button>
</form>
