@extends('layouts.admin')

@section('title', 'Change password')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Account</p>
            <h1>Change password</h1>
            <p class="sub">Signed in as {{ $user->email }}.</p>
        </div>
    </div>

    <div class="form-layout">
        <div class="form-main">
            <section class="card">
                <div class="card-head"><h2>Password</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.account.password.update') }}" data-lock-submit>
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="field field--full">
                                <label class="label" for="current_password">Current password</label>
                                <input class="input @error('current_password') is-invalid @enderror" id="current_password"
                                       name="current_password" type="password" required autocomplete="current-password">
                                @error('current_password')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="password">New password</label>
                                <input class="input @error('password') is-invalid @enderror" id="password"
                                       name="password" type="password" required minlength="12" autocomplete="new-password">
                                <span class="help">At least 12 characters.</span>
                                @error('password')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="password_confirmation">Confirm new password</label>
                                <input class="input" id="password_confirmation" name="password_confirmation"
                                       type="password" required minlength="12" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="card-actions" style="margin-top:16px">
                            <button class="abtn abtn-primary" type="submit">Update password</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <aside class="form-side">
            <section class="card">
                <div class="card-head"><h2>What happens</h2></div>
                <div class="card-body">
                    <p class="help">Updating your password signs out every other session and device. Your current session stays active, and your saved content is not touched.</p>
                </div>
            </section>
        </aside>
    </div>
@endsection
