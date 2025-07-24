<x-admin-layout :title="$pageTitle">
    <style>
        #pagination {
            margin-top: 1rem;
        }
    </style>
    <div class="container">
        <h2 class="mb-4">{{ __('transaction.audit_logs') }}</h2>
         <form method="GET" action="{{ route('admin.blacklist.index') }}" class="mb-3">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" name="member_id" class="form-control" placeholder="Search by Member ID" value="{{ request('member_id') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

          <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Merchant</th>
                            <th>Reason</th>
                            <th>Blacklisted At</th>
                            <th>Times Blacklisted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blacklists as $blacklist)
                            <tr>
                                <td>{{ $blacklist->member_id }}</td>
                                <td>{{ optional($blacklist->api)->name }}</td>
                                <td>{{ $blacklist->reason }}</td>
                                <td>{{ $blacklist->created_at }}</td>
                                <td>{{ \App\Models\BlacklistRemoval::where('member_id', $blacklist->member_id)->count() }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.blacklist.destroy', $blacklist->id) }}" onsubmit="return confirm('Remove this member from blacklist?');">
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

              <div class="tab-pane fade" id="removal" role="tabpanel">
            <form method="GET" action="{{ route('admin.blacklist.index') }}#removal" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <input type="text" name="removal_member_id" class="form-control" placeholder="Search by Member ID" value="{{ request('removal_member_id') }}">
                    </div>
                    <div class="col-auto">
                        <input type="date" name="removal_date" class="form-control" value="{{ request('removal_date') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </div>
            </form>
            @php($removals = \App\Models\BlacklistRemoval::query())
            @if(request('removal_member_id'))
                @php($removals = $removals->where('member_id', 'like', '%' . request('removal_member_id') . '%'))
            @endif
            @if(request('removal_date'))
                @php($removals = $removals->whereDate('removed_at', request('removal_date')))
            @endif
            @php($removals = $removals->orderBy('removed_at', 'desc')->paginate(20))
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Merchant </th>
                            <th>Removed At</th>
                            <th>Admin ID</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($removals as $removal)
                            <tr>
                                <td>{{ $removal->member_id }}</td>
                                <td>{{ optional($removal->api)->name }}</td>
                                <td>{{ $removal->removed_at }}</td>
                                <td>{{ $removal->admin_id ?? '-' }}</td>
                                <td>{{ $removal->reason ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No removal history found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $removals->links() }}
            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    @endpush
</x-admin-layout>
