@extends('admin.layouts.master')
@section('title')
    Settings Management
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 45px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--primary-color);
            color: white;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Fix for modal positioning */
        .select2-container--open .select2-dropdown--below {
            z-index: 1060;
        }

        :root {
            --primary-color: #249722;
            --primary-light: #e8f5e8;
            --primary-dark: #1e7a1c;
        }

        .settings-nav .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .settings-nav .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .settings-nav .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .settings-tab-pane {
            display: none;
        }

        .settings-tab-pane.active {
            display: block;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .api-key-display {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-family: monospace;
            font-size: 0.875rem;
        }

        .permission-group {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .permission-group-header {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }

        .permission-items {
            padding: 1rem;
        }

        .logo-preview {
            max-width: 150px;
            max-height: 50px;
            object-fit: contain;
        }

        .swal2-toast {
            font-size: 12px !important;
            padding: 6px 10px !important;
            min-width: auto !important;
            width: 220px !important;
            line-height: 1.3em !important;
        }

        .swal2-toast .swal2-icon {
            width: 24px !important;
            height: 24px !important;
            margin-right: 6px !important;
        }

        .swal2-toast .swal2-title {
            font-size: 13px !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800 page-title">
                <i class="fas fa-cog me-2"></i>Settings Management
            </h1>
        </div>

        <div class="row">
            <!-- Settings Navigation -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="settings-nav p-3">
                            <div class="nav flex-column">
                                <a class="nav-link active" href="#" data-tab="general">
                                    <i class="fas fa-sliders-h me-2"></i>General Settings
                                </a>
                                <a class="nav-link" href="#" data-tab="api-keys">
                                    <i class="fas fa-key me-2"></i>API Keys
                                </a>
                                <a class="nav-link" href="#" data-tab="roles-permissions">
                                    <i class="fas fa-user-shield me-2"></i>Roles & Permissions
                                </a>
                                <a class="nav-link" href="#" data-tab="taxes">
                                    <i class="fas fa-receipt me-2"></i>Taxes Management
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-lg-9">
                <!-- General Settings Tab -->
                <div class="settings-tab-pane active" id="general-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-sliders-h me-2"></i>General Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.general.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="app_name" class="form-label fw-semibold">Application Name</label>
                                        <input type="text" class="form-control" id="app_name" name="app_name"
                                            value="{{ old('app_name', $settings->app_name ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact_email" class="form-label fw-semibold">Contact Email</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email"
                                            value="{{ old('contact_email', $settings->contact_email ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="logo" class="form-label fw-semibold">Application Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo"
                                            accept="image/*">
                                        @if ($settings->logo ?? false)
                                            <div class="mt-2">
                                                <label class="form-label">Current Logo:</label>
                                                <div>
                                                    <img src="{{ Storage::url($settings->logo) }}" alt="Logo"
                                                        class="logo-preview">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gst_percentage" class="form-label fw-semibold">GST Percentage
                                            (%)</label>
                                        <input type="number" step="0.01" class="form-control" id="gst_percentage"
                                            name="gst_percentage"
                                            value="{{ old('gst_percentage', $settings->gst_percentage ?? 0) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="rounding_rules" class="form-label fw-semibold">Rounding Rules</label>
                                        <select class="form-select" id="rounding_rules" name="rounding_rules" required>
                                            <option value="nearest"
                                                {{ ($settings->rounding_rules ?? '') == 'nearest' ? 'selected' : '' }}>Round
                                                to Nearest</option>
                                            <option value="up"
                                                {{ ($settings->rounding_rules ?? '') == 'up' ? 'selected' : '' }}>Always
                                                Round Up</option>
                                            <option value="down"
                                                {{ ($settings->rounding_rules ?? '') == 'down' ? 'selected' : '' }}>Always
                                                Round Down</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h6 class="fw-semibold mb-3">Surcharge Configuration</h6>
                                    <div class="row g-3">
                                        @php
                                            $surchargeConfig = $settings->surcharge_configuration ?? [
                                                'peak_hours' => 1.2,
                                                'late_night' => 1.5,
                                                'weekend' => 1.1,
                                            ];
                                        @endphp
                                        <div class="col-md-4">
                                            <label for="peak_hours" class="form-label">Peak Hours Multiplier</label>
                                            <input type="number" step="0.1" class="form-control" id="peak_hours"
                                                name="surcharge_configuration[peak_hours]"
                                                value="{{ $surchargeConfig['peak_hours'] ?? 1.2 }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="late_night" class="form-label">Late Night Multiplier</label>
                                            <input type="number" step="0.1" class="form-control" id="late_night"
                                                name="surcharge_configuration[late_night]"
                                                value="{{ $surchargeConfig['late_night'] ?? 1.5 }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="weekend" class="form-label">Weekend Multiplier</label>
                                            <input type="number" step="0.1" class="form-control" id="weekend"
                                                name="surcharge_configuration[weekend]"
                                                value="{{ $surchargeConfig['weekend'] ?? 1.1 }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- API Keys Tab -->
                <div class="settings-tab-pane" id="api-keys-tab">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-key me-2"></i>API Keys Management</h5>
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#apiKeyModal">
                                <i class="fas fa-plus me-1"></i>Add API Key
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>API Key</th>
                                            <th>Secret Key</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($apiKeys as $apiKey)
                                            <tr>
                                                <td class="fw-semibold">{{ $apiKey->name }}</td>
                                                <td>
                                                    <div class="api-key-display">
                                                        {{ $apiKey->key }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="api-key-display">
                                                        {{ $apiKey->secret }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary edit-api-key"
                                                        data-id="{{ $apiKey->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#apiKeyModal" title="Edit API Key">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form
                                                        action="{{ route('admin.settings.api-keys.delete', $apiKey->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this API key?')"
                                                            title="Delete API Key">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-key fa-3x mb-3"></i>
                                                        <h5>No API Keys Found</h5>
                                                        <p>Click "Add API Key" to create your first API key.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles & Permissions Tab -->
                <div class="settings-tab-pane" id="roles-permissions-tab">
                    <div class="row">
                        <!-- Roles Management -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Roles Management</h5>
                                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#roleModal">
                                        <i class="fas fa-plus me-1"></i>Add Role
                                    </button>
                                </div>
                                <div class="card-body">
                                    @foreach ($roles as $role)
                                        <div class="role-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="fw-semibold mb-1">{{ $role->name }}
                                                        @if ($role->is_default)
                                                            <span class="badge bg-primary ms-2">Default</span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-2 small">{{ $role->description }}</p>
                                                    <div class="permissions-list">
                                                        @foreach ($role->permissions->take(3) as $permission)
                                                            <span
                                                                class="badge bg-light text-dark small me-1">{{ $permission->name }}</span>
                                                        @endforeach
                                                        @if ($role->permissions->count() > 3)
                                                            <span
                                                                class="badge bg-secondary small">+{{ $role->permissions->count() - 3 }}
                                                                more</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary edit-role"
                                                        data-id="{{ $role->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#roleModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if (!$role->is_default)
                                                        <form
                                                            action="{{ route('admin.settings.roles.delete', $role->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Admin Role Assignment -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-user-shield me-2"></i>Admin Role
                                        Assignment</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.settings.assign-admin-role') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="admin_id" class="form-label fw-semibold">Select Admin</label>
                                            <select class="form-select" id="admin_id" name="admin_id" required>
                                                <option value="">Choose Admin...</option>
                                                @foreach ($admins as $admin)
                                                    <option value="{{ $admin->id }}">
                                                        {{ $admin->name }} ({{ $admin->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="role_id" class="form-label fw-semibold">Assign Role</label>
                                            <select class="form-select" id="role_id" name="role_id" required>
                                                <option value="">Choose Role...</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i>Assign Role
                                        </button>
                                    </form>

                                    <div class="mt-4">
                                        <h6 class="fw-semibold mb-3">Current Assignments</h6>
                                        @foreach ($admins as $admin)
                                            <div class="admin-role-item mb-2 p-2 border rounded">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $admin->name }}</strong>
                                                        <small class="text-muted d-block">{{ $admin->email }}</small>
                                                    </div>
                                                    <div>
                                                        @foreach ($admin->roles as $role)
                                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-list-check me-2"></i>Available Permissions</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $permissionsByModule = $permissions->groupBy('module');
                            @endphp
                            @foreach ($permissionsByModule as $module => $modulePermissions)
                                <div class="permission-group">
                                    <div class="permission-group-header">
                                        {{ $module }}
                                    </div>
                                    <div class="permission-items">
                                        <div class="row">
                                            @foreach ($modulePermissions as $permission)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox"
                                                            type="checkbox" value="{{ $permission->id }}"
                                                            id="perm_{{ $permission->id }}" disabled>
                                                        <label class="form-check-label small"
                                                            for="perm_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Taxes Management Tab -->
                <div class="settings-tab-pane" id="taxes-tab">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-receipt me-2"></i>Taxes Management</h5>
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#taxModal">
                                <i class="fas fa-plus me-1"></i>Add Tax
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tax Name</th>
                                            <th>Rate</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Description</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($taxes as $tax)
                                            <tr>
                                                <td class="fw-semibold">{{ $tax->name }}</td>
                                                <td>
                                                    {{ $tax->type == 'percentage' ? $tax->rate . '%' : '$' . $tax->rate }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $tax->type == 'percentage' ? 'bg-info' : 'bg-warning' }}">
                                                        {{ ucfirst($tax->type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $tax->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="small">{{ $tax->description ?: 'No description' }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary edit-tax"
                                                        data-id="{{ $tax->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#taxModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('admin.settings.taxes.delete', $tax->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this tax?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-receipt fa-3x mb-3"></i>
                                                        <h5>No Taxes Found</h5>
                                                        <p>Click "Add Tax" to create your first tax.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Key Modal -->
    <div class="modal fade" id="apiKeyModal" tabindex="-1" aria-labelledby="apiKeyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title text-white" id="apiKeyModalTitle">Add New API Key</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="apiKeyForm" method="POST">
                    @csrf
                    <input type="hidden" id="apiKeyId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="api_key_name" class="form-label fw-semibold">API Key Name</label>
                            <input type="text" class="form-control" id="api_key_name" name="name" required
                                placeholder="Enter API key name">
                        </div>

                        <div class="mb-3">
                            <label for="api_key_key" class="form-label fw-semibold">API Key</label>
                            <input type="text" class="form-control" id="api_key_key" name="key" required
                                placeholder="Enter API key">
                        </div>

                        <div class="mb-3">
                            <label for="api_key_secret" class="form-label fw-semibold">API Secret</label>
                            <input type="text" class="form-control" id="api_key_secret" name="secret" required
                                placeholder="Enter API secret">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="apiKeySubmitBtn">Save API Key</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="roleModalTitle">Add New Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="roleForm" action="{{ route('admin.settings.roles.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="roleId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_name" class="form-label fw-semibold">Role Name</label>
                                    <input type="text" class="form-control" id="role_name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_description" class="form-label fw-semibold">Description</label>
                                    <input type="text" class="form-control" id="role_description" name="description">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Permissions</label>
                            @foreach ($permissions->groupBy('module') as $module => $modulePermissions)
                                <div class="permission-group mb-3">
                                    <div class="permission-group-header">
                                        {{ $module }}
                                    </div>
                                    <div class="permission-items">
                                        <div class="row">
                                            @foreach ($modulePermissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input role-permission-checkbox"
                                                            type="checkbox" name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            id="role_perm_{{ $permission->id }}">
                                                        <label class="form-check-label"
                                                            for="role_perm_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tax Modal -->
    <div class="modal fade" id="taxModal" tabindex="-1" aria-labelledby="taxModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="taxModalTitle">Add New Tax</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="taxForm" action="{{ route('admin.settings.taxes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="taxId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tax_name" class="form-label fw-semibold">Tax Name</label>
                            <input type="text" class="form-control" id="tax_name" name="name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label fw-semibold">Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="tax_rate"
                                        name="rate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_type" class="form-label fw-semibold">Type</label>
                                    <select class="form-select" id="tax_type" name="type" required>
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount ($)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tax_description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="tax_description" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="tax_is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label fw-semibold" for="tax_is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Tax</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for permissions dropdown
            function initializeSelect2() {
                $('.select2-permissions').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Select permissions...',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $('#apiKeyModal')
                });
            }

            // Initialize Select2 when modal is shown
            $('#apiKeyModal').on('shown.bs.modal', function() {
                initializeSelect2();
            });

            // Tab Navigation
            $('.settings-nav .nav-link').click(function(e) {
                e.preventDefault();
                const tab = $(this).data('tab');

                // Update active nav link
                $('.settings-nav .nav-link').removeClass('active');
                $(this).addClass('active');

                // Show corresponding tab
                $('.settings-tab-pane').removeClass('active');
                $(`#${tab}-tab`).addClass('active');
            });

            // API Key Management - Fixed
            $('.edit-api-key').click(function() {
                const apiKeyId = $(this).data('id');

                fetch(`/admin/settings/api-keys/${apiKeyId}/edit`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        $('#apiKeyModalTitle').text('Edit API Key');
                        $('#apiKeyId').val(data.id);
                        $('#api_key_name').val(data.name);
                        $('#api_key_key').val(data.key);
                        $('#api_key_secret').val(data.secret);

                        // Update form action for editing
                        $('#apiKeyForm').attr('action', `/admin/settings/api-keys/${data.id}`);
                        $('#apiKeyForm').append('<input type="hidden" name="_method" value="PUT">');
                        $('#apiKeySubmitBtn').text('Update API Key');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to load API key data', 'error');
                    });
            });

            // Reset API Key modal when closed
            $('#apiKeyModal').on('hidden.bs.modal', function() {
                $('#apiKeyForm')[0].reset();
                $('#apiKeyId').val('');
                $('#apiKeyModalTitle').text('Add New API Key');
                $('#apiKeyForm').attr('action', '{{ route('admin.settings.api-keys.store') }}');
                $('#apiKeyForm').find('input[name="_method"]').remove();
                $('#apiKeySubmitBtn').text('Save API Key');
            });

            // Role Management
            $('.edit-role').click(function() {
                const roleId = $(this).data('id');

                fetch(`/admin/settings/roles/${roleId}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        $('#roleModalTitle').text('Edit Role');
                        $('#roleId').val(data.id);
                        $('#role_name').val(data.name);
                        $('#role_description').val(data.description);

                        // Set permissions
                        $('.role-permission-checkbox').prop('checked', false);
                        if (data.permissions) {
                            data.permissions.forEach(permission => {
                                $(`#role_perm_${permission.id}`).prop('checked', true);
                            });
                        }

                        // Update form action for editing
                        $('#roleForm').attr('action', `/admin/settings/roles/${data.id}`);
                        $('#roleForm').append('<input type="hidden" name="_method" value="PUT">');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to load role data', 'error');
                    });
            });

            // Tax Management
            $('.edit-tax').click(function() {
                const taxId = $(this).data('id');

                fetch(`/admin/settings/taxes/${taxId}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        $('#taxModalTitle').text('Edit Tax');
                        $('#taxId').val(data.id);
                        $('#tax_name').val(data.name);
                        $('#tax_rate').val(data.rate);
                        $('#tax_type').val(data.type);
                        $('#tax_description').val(data.description);
                        $('#tax_is_active').prop('checked', data.is_active);

                        // Update form action for editing
                        $('#taxForm').attr('action', `/admin/settings/taxes/${data.id}`);
                        $('#taxForm').append('<input type="hidden" name="_method" value="PUT">');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to load tax data', 'error');
                    });
            });

            // Reset modals when closed
            $('.modal').on('hidden.bs.modal', function() {
                const $form = $(this).find('form');
                $form[0].reset();
                $form.find('input[type="hidden"]').val('');

                // Reset titles
                $(this).find('.modal-title').text(function() {
                    return $(this).text().replace('Edit', 'Add New');
                });

                // Reset form actions to create
                if ($form.attr('id') === 'roleForm') {
                    $form.attr('action', '{{ route('admin.settings.roles.store') }}');
                    $form.find('input[name="_method"]').remove();
                }
                if ($form.attr('id') === 'taxForm') {
                    $form.attr('action', '{{ route('admin.settings.taxes.store') }}');
                    $form.find('input[name="_method"]').remove();
                }
            });

            // Set minimum date for API key expiry
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            $('#api_key_expires_at').attr('min', tomorrow.toISOString().split('T')[0]);

            // Form validation
            $('#taxForm').submit(function(e) {
                const rate = parseFloat($('#tax_rate').val());
                const type = $('#tax_type').val();

                if (type === 'percentage' && (rate < 0 || rate > 100)) {
                    e.preventDefault();
                    Swal.fire('Invalid Rate', 'Percentage rate must be between 0 and 100', 'error');
                }
            });

            // SweetAlert for success messages
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@endsection
