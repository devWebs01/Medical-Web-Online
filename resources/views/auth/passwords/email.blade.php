<x-auth-layout>
    <x-slot name="title">Halaman Lupa Kata Sandi</x-slot>
    @if (session("status"))
        <div class="alert alert-primary" role="alert">
            {{ session("status") }}
        </div>
    @endif

    <form method="POST" action="{{ route("password.email") }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label text-md-end">{{ __("Email") }}</label>

            <input id="email" type="email" class="form-control @error("email") is-invalid @enderror" name="email"
                value="{{ old("email") }}" required autocomplete="email" autofocus>

            @error("email")
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="d-grid mb-0">
            <button type="submit" class="btn btn-primary">
                {{ __("Kirim Link Reset Kata Sandi") }}
            </button>
        </div>
    </form>
</x-auth-layout>
