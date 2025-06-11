<x-admin-layout :title="$pageTitle">
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="mb-3 text-right">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                    id="newCategoryButton">
                    {{ __('partner.add_commission_category') }}
                </button>
            </div>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>{{ __('partner.name') }}</th>
                            <th>{{ __('partner.created_at') }}</th>
                            {{-- <th>{{ __('partner.status') }}</th> --}}
                            <th>{{ __('partner.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ dateTime($item->created_at, 'd M, Y H:i') }}</td>
                                {{-- <td>
                                @if ($item->status == 1)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td> --}}
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal" data-id="{{ $item->id }}"
                                        data-name="{{ $item->title }}" data-status="{{ $item->status }}">
                                        {{ __('partner.edit') }}
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteCategoryModal" data-id="{{ $item->id }}">
                                        {{ __('partner.delete') }}
                                    </button>

                                    <!-- Commission Button -->
                                    <a href="{{ route('admin.apis.commission', ['id' => $item->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        {{ __('partner.commission') }}
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">{{ __('partner.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $records->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.commission.categories.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('partner.add_new') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('partner.category_name') }}</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    {{-- <div class="form-group mt-3">
                        <label>{{ __('partner.status') }}</label>
                        <select class="form-control" name="status" required>
                            <option value="1">{{ __('partner.active') }}</option>
                            <option value="0">{{ __('partner.inactive') }}</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('partner.save') }}</button>
                    <button type="button" class="btn btn-dark"
                        data-bs-dismiss="modal">{{ __('partner.close') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.commission.categories.update') }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editCategoryId">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('partner.edit_category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('partner.category_name') }}</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required />
                    </div>
                    {{-- <div class="form-group mt-3">
                        <label>{{ __('partner.status') }}</label>
                        <select class="form-control" name="status" id="editCategoryStatus" required>
                            <option value="1">{{ __('partner.active') }}</option>
                            <option value="0">{{ __('partner.inactive') }}</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('partner.update') }}</button>
                    <button type="button" class="btn btn-dark"
                        data-bs-dismiss="modal">{{ __('partner.close') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.commission.categories.destroy') }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" id="deleteCategoryId">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('partner.delete_category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('partner.delete_confirmation') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark"
                        data-bs-dismiss="modal">{{ __('partner.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('partner.delete') }}</button>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                $('#editCategoryModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    var name = button.data('name');
                    // var status = button.data('status').toString();
                    $('#editCategoryId').val(id);
                    $('#editCategoryName').val(name);
                    // $('#editCategoryStatus').val(status == 1 ? '1' : '0');
                });

                $('#deleteCategoryModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    $('#deleteCategoryId').val(button.data('id'));
                });
            });
        </script>
    @endpush
</x-admin-layout>
