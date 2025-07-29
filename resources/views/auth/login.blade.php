<x-auth-layout>
    <x-slot name="title">Halaman Login</x-slot>
    <form method="POST" action="{{ route("login") }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __("Email") }}</label>

            <input id="email" type="email" class="form-control @error("email") is-invalid @enderror" name="email"
                value="{{ old("email") }}" required autocomplete="email" autofocus>

            @error("email")
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __("Kata Sandi") }}</label>

            <input id="password" type="password" class="form-control @error("password") is-invalid @enderror"
                name="password" required autocomplete="current-password">

            @error("password")
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old("remember") ? "checked" : "" }}>

                <label class="form-check-label" for="remember">
                    {{ __("Remember Me") }}
                </label>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary w-100">
                {{ __("Login") }}
            </button>

            <div class="mt-3 text-center justify-content-between">
                @if (Route::has("password.request"))
                    <a class="btn btn-link" href="{{ route("password.request") }}">
                        {{ __("Lupa Sandi Anda?") }}
                    </a>
                @endif

                {{-- <a class="btn btn-link" href="{{ route('register') }}">
                    Register
                </a> --}}
            </div>
        </div>
    </form>
</x-auth-layout>
