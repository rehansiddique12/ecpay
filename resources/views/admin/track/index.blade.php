<x-admin-layout :title="$pageTitle">
    
    <!-- Add these lines to your HTML header section -->
    <link rel="stylesheet" href="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
    <script src="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">partner_transection_id</th>
                            <th scope="col">txn_id</th>
                            <th scope="col">e_wallet_phone_number</th>
                            <th scope="col">amount</th>
                            <th scope="col">ocr_text</th>
                            <th scope="col">Image</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $key => $record)
                            <tr>

                                <td>{{ $record->partner_transection_id }}</td>
                                <td>{{ $record->txn_id }}</td>
                                <td>{{ $record->e_wallet_phone_number }}</td>
                                <td>{{ $record->amount }}</td>
                                <td>{{ $record->ocr_text }}</td>
                                <td>
                                    @if (!empty($record->image_path))
                                        <a data-fancybox="images"
                                            href="{{ env('ASSET_URL').str_replace('/home/ecpayadmin/public_html', '', $record->image_path) }}">
                                            <h2><i class="fa fa-file"></i></h2>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $record->created_at }}</td>
                                <td>{{ $record->updated_at }}</td>






                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">{{ __('transaction.no_data_found') }}</p>
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
                <div class="mt-5">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>




</x-admin-layout>
