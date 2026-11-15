@extends('admin-panel::layouts.auth')
@section('content')
<form method="post" action="{{ route('login.store') }}" class="space-y-4">
    @csrf
    <div>
        <label class="text-sm">Email</label>
        <input name="email" type="email" value="{{ old('email', 'super@rongdhonu.test') }}" required class="admin-input w-full">
        @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="text-sm">Password</label>
        <input name="password" type="password" value="password" required class="admin-input w-full">
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1"> Remember this desk</label>
    <button class="admin-btn w-full" style="background:var(--admin-primary);color:white">Enter factory</button>
    <p class="text-xs" style="color:var(--admin-text-muted)">Demo accounts use the password <strong>password</strong>. Start with super@rongdhonu.test.</p>
</form>
@endsection
