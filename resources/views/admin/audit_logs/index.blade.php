<x-admin-layout :title="$pageTitle">
<style>
    #pagination{
        margin-top: 1rem;
    }
</style>
<div class="container">
    <h2 class="mb-4">Audit Logs</h2>
    <form method="GET" class="mb-5 row g-2">
        <div class="col-md-3">
            <select name="user_id" class="form-select select2" data-allow-clear="true"
            data-placeholder="{{ __('transaction.select_user') }}">
            <option></option>
                <option value="">Filter by User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="text" name="module" value="{{ request('module') }}" placeholder="Module"
                   class="form-control" />
        </div>

        <div class="col-md-3">
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" />
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>
    <table class="table table-bordered mt-5">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Module</th>
                {{-- <th>Module ID</th> --}}
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->user->name ?? 'N/A' }}</td>
                <td>{{ $log->module }}</td>
                {{-- <td>{{ $log->module_id }}</td> --}}
                <td>{{ $log->description }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $logs->appends($_GET)->links('partials.pagination') }}
</div>
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
      $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('transaction.processing') }}");

                    // Allow form to proceed
                    return true;
                });
                let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
                $select.on('select2:unselecting', function(e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function(e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });
</script>
@endpush
</x-admin-layout>
