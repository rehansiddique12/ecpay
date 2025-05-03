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
                    <h1>Roles</h1>

                    <a href="{{ route('admin.roles.create') }}">Create New Role</a>

                    @if(session('success'))
                        <p>{{ session('success') }}</p>
                    @endif

                    <table border="1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ implode(', ', $role->permissions->pluck('name')->toArray()) }}</td>
                                <td>
                                    <a href="{{ route('admin.roles.edit', $role->id) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>


    @push('js')


    @endpush
</x-admin-layout>
