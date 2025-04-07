<x-admin-layout :title="$pageTitle">

        <style>
             h3{
          color: #7367f0 !important
        }

        .dropzone-container {
    width: 100%;
}

.dropzone {
    border: 1px dashed #ccc;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

.dropzone:hover {
    border-color: #999;
    background-color: #f9f9f9;
}

.upload-icon {
    background-color: #f0f0f0;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.upload-svg {
    color: #666;
}

.dropzone-title {
    font-size: 1.125rem;
    color: #333;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.dropzone-description {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
}

.hidden-input {
    position: absolute;
    width: 0;
    height: 0;
    opacity: 0;
}

.preview-image {
    max-width: 100%;
    margin-top: 1rem;
    border-radius: 4px;
    display: none;
}

#image_preview_container:not([src="/placeholder.svg"]) {
    display: block;
}


        </style>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title mb-3"><i class="icon-user"></i> @lang('Profile Setting')</h3>
                    <form action="" method="post" class="form-body file-upload" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="form-row justify-content-between flex">


                        <div class="flex">
                            <div class="col-sm-6 col-md-4">
                                <div class="image-input dropzone-container">
                                    <div class="dropzone" id="image-dropzone" onclick="document.getElementById('image').click()">
                                        <div class="upload-icon" id="upload-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="upload-svg">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>
                                        <h3 class="dropzone-title" id="dropzone-title">Drop files here or click to upload</h3>
                                        <p class="dropzone-description" id="dropzone-description">
                                            (This is just a demo dropzone. Selected files are not actually uploaded.)
                                        </p>

                                        <input type="file" name="image" id="image" class="hidden-input" accept="image/*" style="display:none;" onchange="handleImageSelection(event)">

                                        <!-- Preview Image -->
                                        <img id="image_preview_container" class="preview-image"
                                            src="" alt="Preview Image"
                                            style="display: none; max-width: 100%; height: auto; margin-top: 10px;">
                                    </div>
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <script>
                                    function handleImageSelection(event) {
                                        const file = event.target.files[0]; // Get the selected file

                                        if (file) {
                                            const reader = new FileReader();

                                            reader.onload = function(e) {
                                                // Set the image source to the selected file's data URL
                                                const imagePreview = document.getElementById('image_preview_container');
                                                imagePreview.src = e.target.result;
                                                imagePreview.style.display = 'block'; // Show the preview image

                                                // Hide the dropzone elements
                                                document.getElementById('upload-icon').style.display = 'none';
                                                document.getElementById('dropzone-title').style.display = 'none';
                                                document.getElementById('dropzone-description').style.display = 'none';
                                            };

                                            reader.readAsDataURL(file); // Read the file as a data URL
                                        }
                                    }
                                </script>

                            </div>

                            <div class="col-sm-6 col-md-7">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Name') <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control form-control-lg" value="{{$admin->name}}" placeholder="@lang('Enter Name')">

                                            @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Username') <span class="text-danger">*</span></label>
                                            <input type="text" name="username" class="form-control" value="{{$admin->username}}" placeholder="@lang('Enter Username')">

                                            @error('username')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email Address') <span class="text-danger">*</span></label>
                                            <input type="text" name="email" class="form-control" value="{{$admin->email}}" placeholder="@lang('Enter Email Address')">


                                            @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Phone Number') <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" class="form-control" value="{{$admin->phone}}" placeholder="@lang('Enter Phone Number')">

                                            @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Address') <span class="text-muted text-sm">{{trans('(optional)')}}</span></label>
                                            <textarea name="address" class="form-control" rows="3" placeholder="@lang('Your Address')">{{$admin->address}}</textarea>

                                            @error('address')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="text-right">
                                            <button type="submit" class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">{{trans('Submit')}}</button>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>



                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>









@push('js')

@endpush
</x-admin-layout>
