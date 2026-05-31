<x-guest-layout>
    <h2 class="h5 fw-semibold mb-3">Login</h2>

    <form method="POST" action="{{ route('login') }}" class="vstack gap-3">
        @csrf

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
        </div>

        <div class="form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">Remember me</label>
        </div>

        <button type="submit" class="btn btn-success w-100">Login</button>

        <div class="d-flex justify-content-between small">
            <a href="{{ route('register') }}" class="text-success">Register</a>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-secondary">Forgot password?</a>
            @endif
        </div>
    </form>
</x-guest-layout>
