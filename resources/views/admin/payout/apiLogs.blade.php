<x-admin-layout :title="$pageTitle">
<style>
    .categories-show-table {
        width: 100% !important;
    }
</style>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <table class="categories-show-table table table-hover table-striped table-bordered" class="full-width-table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">@lang('No.')</th>
                    <th scope="col">@lang('Request URL')</th>
                    <th scope="col">@lang('Request Method')</th>
                    <th scope="col">@lang('Request Payload')</th>
                    <th scope="col">@lang('Request Header')</th>
                    <th scope="col">@lang('Response Code')</th>
                    <th scope="col">@lang('Response Payload')</th>
                    <th scope="col">@lang('Response Header')</th>
                    <th scope="col">@lang('Created At')</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $transaction)
                    <tr>
                        <td data-label="@lang('No.')">{{ $transaction->id }}</td>
                        <td data-label="@lang('Request URL')">{{ $transaction->request_url }}</td>
                        <td data-label="@lang('Request URL')">{{ $transaction->request_method }}</td>
                        <td data-label="@lang('Request URL')">{{ $transaction->request_payload }}</td>
                         <td data-label="@lang('Request URL')">{{ $transaction->request_headers }}</td>
                        <td data-label="@lang('Response Code')">{{ $transaction->response_code }}</td>
                        <td data-label="@lang('Response Payload')">{{ $transaction->response_payload }}</td>
                        <td data-label="@lang('Response Payload')">{{ $transaction->response_headers }}</td>
                        <td data-label="@lang('Created At')">{{ $transaction->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-danger" colspan="5">@lang('No Record Found')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $data->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="pagination float-right mr-4">
</div>

@push('js')
@endpush
</x-admin-layout>