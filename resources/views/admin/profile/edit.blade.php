@extends('layouts.admin')

@section('title', 'Profile settings')
@section('page-title', 'Profile')
@section('page-subtitle', 'Manage your account and security preferences')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">Profile details</h2>
            </div>
            <div class="card-body">
                <form id="profileForm" method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update profile</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">Change password</h2>
            </div>
            <div class="card-body">
                <form id="passwordForm" method="POST" action="{{ route('admin.profile.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Current password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Update password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#profileForm').on('submit', function (event) {
            event.preventDefault();
            Devgenfour.submitForm(this).then(() => Devgenfour.flash('Profile updated'));
        });

        $('#passwordForm').on('submit', function (event) {
            event.preventDefault();
            Devgenfour.submitForm(this).then(() => {
                Devgenfour.flash('Password updated');
                this.reset();
            });
        });
    });
</script>
@endpush
