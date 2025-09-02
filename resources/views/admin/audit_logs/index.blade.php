    <x-admin-layout :title="$pageTitle">
    <style>
        #pagination {
            margin-top: 1rem;
        }
    </style>
    <div class="container">
        <h2 class="mb-4">{{ __('transaction.audit_logs') }}</h2>
            <form method="GET" class="mb-5 row g-2">
                <div class="col-md-2">
                    <select name="user_id" class="form-select select2" data-allow-clear="true"
                        data-placeholder="{{ __('transaction.select_user') }}">
                        <option></option>
                        <option value="">{{ __('transaction.filter_by_user') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="module" class="form-select">
                        <option value="">{{ __('transaction.module') }}</option>
                        <option value="Workboard" {{ request('module') == 'Workboard' ? 'selected' : '' }}>Workboard</option>
                        <option value="Deposit Log" {{ request('module') == 'Deposit Log' ? 'selected' : '' }}>Deposit Log</option>
                        <option value="Withdrawal Log" {{ request('module') == 'Withdrawal Log' ? 'selected' : '' }}>Withdrawal Log</option>
                        <option value="Account Management" {{ request('module') == 'Account Management' ? 'selected' : '' }}>Account Management</option>
                        <option value="API Balance Adjustment" {{ request('module') == 'API Balance Adjustment' ? 'selected' : '' }}>API Balance Adjustment</option>
                        <option value="Transfer Balance" {{ request('module') == 'Transfer Balance' ? 'selected' : '' }}>Transfer Balance</option>
                    </select>
                </div>


                <div class="col-md-2">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <input type="text" name="description" value="{{ request('description') }}" class="form-control" placeholder="{{ __('transaction.search_description') ?? 'Search Description' }}" />
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary">{{ __('transaction.search') }}</button>
                </div>


            </form>
            <table class="table table-hover table-striped table-bordered table-sm">
                <thead>
                    <tr>
                        <th>{{ __('transaction.id') }}</th>
                        <th>{{ __('transaction.user') }}</th>
                        <th>{{ __('transaction.action') }}</th>
                        <th>{{ __('transaction.module') }}</th>
                        <th>{{ __('transaction.description') }}</th>
                        <th>{{ __('transaction.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                            <td>{{ $log->module }}</td>
                            <td>
                                @php
                                    $module = $log->module;
                                    $label = 'N/A';

                                    if (str_contains($module, 'Workboard')) {
                                        $label = 'Workboard';
                                    } elseif (str_contains($module, 'Payment')) {
                                        $label = 'Deposit Log';
                                    } elseif (str_contains($module, 'Payout')) {
                                        $label = 'Withdrawal Log';
                                    } elseif (
                                        str_contains(strtolower($module), 'gateway') ||
                                        str_contains($module, 'EWalletAccount') ||
                                        str_contains($module, 'Account Management')
                                    ) {
                                        $label = 'Account Management';
                                    } elseif (str_contains($module, 'API Balance Adjustment')) {
                                        $label = 'API Balance Adjustment';
                                    } elseif (str_contains($module, 'Transfer Balance')) {
                                        $label = 'Transfer Balance';
                                    }
                                @endphp
                                {{ $label }}
                            </td>


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
