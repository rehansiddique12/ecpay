<x-admin-layout :title="$pageTitle">
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="mb-3 text-right">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                    id="newCategoryButton">
                    Add Commission Category
                </button>
            </div>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Created At')</th>
                            {{-- <th>@lang('Status')</th> --}}
                            <th>@lang('Actions')</th>
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
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteCategoryModal" data-id="{{ $item->id }}">
                                    Delete
                                </button>

                                <!-- Commission Button -->
                                <a href="{{ route('admin.apis.commission', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">
    Commission
</a>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">@lang('No Data Found')</td>
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
                    <h5 class="modal-title">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    {{-- <div class="form-group mt-3">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Save')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('Close')</button>
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
                    <h5 class="modal-title">@lang('Edit Category')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required />
                    </div>
                    {{-- <div class="form-group mt-3">
                        <label>Status</label>
                        <select class="form-control" name="status" id="editCategoryStatus" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Update')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('Close')</button>
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
                    <h5 class="modal-title">@lang('Delete Category')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure you want to delete this category?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-danger">@lang('Delete')</button>
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