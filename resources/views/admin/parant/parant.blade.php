    <x-admin-layout :title="$pageTitle">
        @push('styles')
        <script src="{{ asset('public/assets/css/select2.min.css')}}"></script>
        <style>
            tr th{
            color: white !important
            }
        </style>
        @endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    {{-- @if(adminAccessRoute(config('role.partners.access.add'))) --}}
                    {{-- <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2" data-target="#newModal" data-toggle="modal">
                        <span><i class="fa fa-plus-circle"></i> @lang('Add New')</span>
                    </a> --}}
                    {{-- @endif --}}

                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">@lang('ID')</th>
                                    <th scope="col">@lang('Partner')</th>
                                    <th scope="col">@lang('Parent1')</th>
                                    <th scope="col">@lang('Parent2')</th>
                                    <th scope="col">@lang('Parent3')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ optional($item->parent)->username ?? ' ' }}</td> <!-- Display Parent Username -->
                                    <td>{{ optional(optional($item->parent)->parent)->username ?? ' ' }}</td> <!-- Parent2 Username -->
                                    <td>{{ optional(optional(optional($item->parent)->parent)->parent)->username ?? ' ' }}</td> <!-- Parent3 Username -->
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark">@lang('No Data Found')</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                    <div class="card-footer">
                        {{ $records->appends($_GET)->links('partials.pagination') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    </x-admin-layout>
