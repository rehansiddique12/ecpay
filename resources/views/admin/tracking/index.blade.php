<x-admin-layout :title="$pageTitle">
    <style>
        #pagination {
            margin-top: 1rem;
        }
    </style>
    <div class="container">
        <h2 class="mb-4">{{ __('transaction.audit_logs') }}</h2>
        <form action="{{ route('admin.tracking.filter') }}" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <select name="user_id" id="user_id" class="form-select select2" data-allow-clear="true"
                        data-placeholder="{{ __('transaction.select_user') }}">
                        <option></option>
                        <option value="">{{ __('transaction.filter_by_user') }}</option>
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="action">Action</label>
                        <input type="text" name="action" id="action" class="form-control"
                               value="{{ request('action') }}" placeholder="Search action...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control"
                               value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control"
                               value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right my-3">
                    <button type="submit" class="btn btn-primary">Filter</button>

                </div>
            </div>
        </form>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Duration</th>
                    <th>Recorded At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trackers as $tracker)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tracker->user->name ?? 'N/A' }}</td>
                    <td>{{ $tracker->action }}</td>
                    <td>{{ $tracker->from->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $tracker->to ? $tracker->to->format('Y-m-d H:i:s') : 'In Progress' }}</td>
                    <td>{{ $tracker->from->diffForHumans($tracker->to, true) }}</td>
                    <td>{{ $tracker->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

            {{ $trackers->appends($_GET)->links('partials.pagination') }}
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
