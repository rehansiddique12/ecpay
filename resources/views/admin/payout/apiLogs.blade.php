<x-admin-layout :title="$pageTitle">
<style>
 table td div code {
        color: #ccc;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .table td, .table th {
        vertical-align: top;
    }
</style>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <table class=" table table-sm  table-hover table-striped table-bordered text-white" style="table-layout: fixed; width: 100%;">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 100px;">@lang('No.')</th>
                            <th style="width: 200px;">@lang('Request URL')</th>
                            <th style="width: 100px;">@lang('Request Method')</th>
                            <th style="width: 600px;">@lang('Request Payload')</th>
                            <th style="width: 400px;">@lang('Request Header')</th>
                            <th style="width: 100px;">@lang('Response Code')</th>
                            <th style="width: 300px;">@lang('Response Payload')</th>
                            <th style="width: 200px;">@lang('Response Header')</th>
                            <th style="width: 160px;">@lang('Created At')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $transaction)
                            <tr>
                                <td data-label="@lang('No.')">{{ $transaction->id }}</td>

                                <td data-label="@lang('Request URL')" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $transaction->request_url }}">
                                    {{ $transaction->request_url }}
                                </td>

                                <td data-label="@lang('Request Method')">{{ $transaction->request_method }}</td>

                                <td data-label="@lang('Request Payload')">
                                    <div style="max-height: 200px; overflow: auto; background-color: #1e1e2f; padding: 5px; border-radius: 5px; font-size: 16px;">
                                        <code>{{ $transaction->request_payload }}</code>
                                    </div>
                                </td>

                                <td data-label="@lang('Request Header')">
                                        {{ $transaction->request_headers }}
                                </td>

                                <td data-label="@lang('Response Code')">{{ $transaction->response_code }}</td>

                                <td data-label="@lang('Response Payload')">
                                        {{ $transaction->response_payload }}
                                </td>

                                <td data-label="@lang('Response Header')">
                                        {{ $transaction->response_headers }}
                                </td>

                                <td data-label="@lang('Created At')">{{ $transaction->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-danger" colspan="9">@lang('No Record Found')</td>
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

</x-admin-layout>
