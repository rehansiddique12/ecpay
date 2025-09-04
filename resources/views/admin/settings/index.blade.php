<x-admin-layout :title="$pageTitle">
    <div class="container">
        <h2 class="mb-4">{{ __('transaction.settings') }}</h2>
        @if (adminAccessRoute(config('role.ip_whitelist.access.add')))
        <button class="btn btn-primary mb-3" onclick="openAddModal()">{{ __('transaction.add_setting') }}</button>
        @endif
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered" id="settingsTable">
                <thead>
                    <tr>
                        <th>{{ __('transaction.name') }}</th>
                        <th>{{ __('transaction.value') }}</th>
                        <th>{{ __('transaction.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $setting)
                    <tr id="row-{{ $setting->id }}">
                        <td class="name">{{ $setting->name }}</td>
                        <td class="value">{{ $setting->value }}</td>
                        <td>
                            @if (adminAccessRoute(config('role.ip_whitelist.access.edit')))
                            <button class="btn btn-warning btn-sm"
                                onclick="openEditModal({{ $setting->id }}, '{{ $setting->name }}', '{{ $setting->value }}')">{{ __('transaction.edit') }}</button>
                            @endif
                            @if (adminAccessRoute(config('role.ip_whitelist.access.delete')))
                            <a href="{{ route('admin.settings.delete', $setting->id) }}" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">{{ __('transaction.delete') }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="settingForm">
                    @csrf
                    <input type="hidden" id="setting_id" name="setting_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="settingModalLabel">{{ __('transaction.add_setting') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label>{{ __('transaction.name') }}</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('transaction.value') }}</label>
                                <input type="text" name="value" id="value" class="form-control" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="saveBtn">{{ __('transaction.save') }}</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('transaction.close') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>
    </div>

    @push('js')
    <script>
        function openAddModal() {
        $('#settingForm')[0].reset();
        $('#setting_id').val('');
        $('#settingModalLabel').text("{{ __('transaction.add_setting') }}");
        $('#saveBtn').text("{{ __('transaction.add') }}");
        $('#settingModal').modal('show');
    }

    function openEditModal(id, name, value) {
        $('#setting_id').val(id);
        $('#name').val(name);
        $('#value').val(value);
        $('#settingModalLabel').text("{{ __('transaction.edit_setting') }}");
        $('#saveBtn').text("{{ __('transaction.update') }}");
        $('#settingModal').modal('show');
    }

    $('#settingForm').submit(function(e) {
        e.preventDefault();

        let id = $('#setting_id').val();
        let url = id ? "{{ url('admin/settings/update') }}/" + id : "{{ route('admin.settings.store') }}";

        $.ajax({
            url: url,
            method: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    if(id) {
                        // Update existing row
                        $('#row-' + id + ' .name').text(res.data.name);
                        $('#row-' + id + ' .value').text(res.data.value);
                    } else {
                        // Add new row
                        $('#settingsTable tbody').prepend(`
                            <tr id="row-${res.data.id}">
                                <td class="name">${res.data.name}</td>
                                <td class="value">${res.data.value}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="openEditModal(${res.data.id}, '${res.data.name}', '${res.data.value}')">Edit</button>
                                    <a href="/admin/settings/delete/${res.data.id}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        `);
                    }

                    $('#settingModal').modal('hide');
                }
            },
            error: function(err) {
                alert('An error occurred. Please check your input.');
            }
        });
    });
    </script>
    @endpush
</x-admin-layout>
