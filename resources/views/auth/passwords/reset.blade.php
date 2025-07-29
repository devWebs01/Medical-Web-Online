<x-auth-layout>
    <x-slot name="title">Halaman Reset Sandi</x-slot>
    @if (session("status"))
        <div class="alert alert-primary" role="alert">
            {{ session("status") }}
        </div>
    @endif
    <p>
        <a href="{{ route("login") }}" class="small">
            Kembali ke Halaman Login
        </a>
    </p>
    <form method="POST" action="{{ route("password.update") }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}" readonly>

        <div class="mb-3">
            <label for="email" class="form-label text-md-end">{{ __("Email Address") }}</label>

            <input id="email" type="email" class="form-control @error("email") is-invalid @enderror"
                name="email" value="{{ $email ?? old("email") }}" required autocomplete="email" readonly>

            @error("email")
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="password" class="form-label text-md-end">{{ __("Password") }}</label>

            <input id="password" type="password" class="form-control @error("password") is-invalid @enderror"
                name="password" required autocomplete="new-password">

            @error("password")
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="password-confirm" class="form-label text-md-end">{{ __("Confirm Password") }}</label>

            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                autocomplete="new-password">

        </div>

        <div class="mb-0">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    {{ __("Reset Password") }}
                </button>
            </div>
        </div>
    </form>
</x-auth-layout>
