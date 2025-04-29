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
                    <h1>Edit Permission: {{ $permission->name }}</h1>

                    <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label>Name:</label>
                            <input type="text" name="name" value="{{ old('name', $permission->name) }}">
                        </div>

                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>

    </div>


    @push('js')


    @endpush
</x-admin-layout>
