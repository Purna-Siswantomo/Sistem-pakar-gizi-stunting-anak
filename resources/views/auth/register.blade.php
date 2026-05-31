<x-guest-layout>
    <h2 class="h5 fw-semibold mb-3">Register</h2>

    <form method="POST" action="{{ route('register') }}" class="vstack gap-3">
        @csrf

        <div>
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control" required autofocus autocomplete="name">
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" required autocomplete="username">
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" class="form-control" required autocomplete="new-password">
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-success w-100">Register</button>

        <div class="small text-center">
            <a href="{{ route('login') }}" class="text-success">Already registered?</a>
        </div>
    </form>
</x-guest-layout>
