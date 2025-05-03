<x-admin-layout :title="$pageTitle">
    @push('styles')
    <script src="{{ asset('public/assets/css/select2.min.css')}}"></script>
    <style>
        tr th {
            color: white !important
        }
    </style>
    @endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h1>Create Role</h1>

                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <div>
                            <label>Name:</label>
                            <input type="text" name="name" value="{{ old('name') }}">
                        </div>

                        <div>
                            <label>Permissions:</label><br>
                            @foreach($permissions as $permission)
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                {{ $permission->name }}
                            </label><br>
                            @endforeach
                        </div>

                        <button type="submit">Create</button>
                    </form>
                </div>
            </div>
        </div>

    </div>


    @push('js')


    @endpush
</x-admin-layout>
