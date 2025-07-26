<x-admin-layout :title="$pageTitle">
    <style>
        #pagination {
            margin-top: 1rem;
        }

    </style>
    <div class="container">
        <h2 class="mb-4">{{ __('transaction.blacklist') }}</h2>
        <form method="POST" action="{{ route('admin.blacklist.store') }}" class="mb-3">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <label>{{ __('transaction.select_merchant') }}</label>
                    <select name="api_id" class="form-control select2">
                        <option value="">{{ __('transaction.select_merchant') }}</option>
                        @foreach($merchants as $merchant)
                        <option value="{{ $merchant->id }}" {{ request('api_id') == $merchant->id ? 'selected' : '' }}>
                            {{ $merchant->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>{{ __('transaction.select_merchant') }}</label>
                    <select name="member_id" class="form-control select2">
                        <option value="">{{ __('transaction.select_merchant') }}</option>
                        @foreach($members as $member)
                        <option value="{{ $member }}" {{ request('api_id') == $merchant->id ? 'selected' : '' }}>
                            {{ $member }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>{{ __('transaction.select_merchant') }}</label>
                    <select name="type" class="form-control select2">
                        <option value="">{{ __('transaction.select_merchant') }}</option>
                        <option value="consecutive">3 Consecutive Missing</option>
                        <option value="total">7 Total Missing</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="">Reason</label>
                   <input type="text" name="reason" class="form-control">
                </div>
                <div class="col-md-1">
                    <label for=""></label>
                    <button class="btn btn-danger mt-5" type="submit">Submit</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{__('transaction.member_id')}}</th>
                        <th>{{__('transaction.merchant')}}</th>
                        <th>{{__('transaction.reason')}}</th>
                        <th>{{__('transaction.blacklisted_at')}}</th>
                        <th>Total Count</th>
                        <th>Consecutive Count</th>
                        <th>{{__('transaction.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blacklists as $blacklist)
                    <tr>
                        <td>{{ $blacklist->member_id }}</td>
                        <td>{{ optional($blacklist->api)->name }}</td>
                        <td>{{ $blacklist->reason }}</td>
                        <td>{{ $blacklist->created_at }}</td>
                       <td>{{ $blacklist->total_count }}</td>
                       <td>{{ $blacklist->consecutive_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.blacklist.destroy', $blacklist->id) }}"
                                onsubmit="return confirm('Remove this member from blacklist?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $blacklists->appends($_GET)->links('partials.pagination') }}


    </div>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @push('js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    @endpush
</x-admin-layout>
