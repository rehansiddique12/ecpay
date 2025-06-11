<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>


    <style>
        h3 {
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
            margin-top: 8% !important;
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

<div class="ps-4 pt-2">
        <h3 class="card-title mb-3"><i class="icon-user"></i> <?php echo app('translator')->get('Profile Setting'); ?></h3>
        <form action="" method="post" class="form-body file-upload" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('put'); ?>
            <div class="row">
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
                            <h3 class="dropzone-title" id="dropzone-title">Drop files here or click
                                to upload</h3>
                            <p class="dropzone-description" id="dropzone-description">
                                (This is just a demo dropzone. Selected files are not actually
                                uploaded.)
                            </p>

                            <input type="file" name="image" id="image" class="hidden-input" accept="image/*"
                                style="display:none;" onchange="handleImageSelection(event)">

                            <!-- Preview Image -->
                            <img id="image_preview_container" class="preview-image" src="" alt="Preview Image"
                                style="display: none; max-width: 100%; height: auto; margin-top: 10px;">
                        </div>
                        <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>



                </div>

                <div class="col-sm-6 col-md-7">

                    <?php if($partner->type=="Admin"): ?>
                    <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo app('translator')->get('API Key'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="api_key" class="form-control" value="<?php echo e($partner->api_key); ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo app('translator')->get('Website'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="website" class="form-control" value="<?php echo e($partner->website); ?>" readonly />
                        </div>
                    </div>
                    </div>

                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo app('translator')->get('Name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg"
                                    value="<?php echo e($partner->name); ?>" placeholder="<?php echo app('translator')->get('Enter Name'); ?>">

                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo app('translator')->get('Username'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" value="<?php echo e($partner->username); ?>"
                                    placeholder="<?php echo app('translator')->get('Enter Username'); ?>">

                                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="timezone">Select TimeZone</label>
                                <select class="form-control select2" id="timezone" name="timezone">
                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($timezone['timezone']); ?>" <?php echo e($usertimezone == $timezone['timezone'] ? 'selected' : ''); ?>>
                                            [<?php echo e($timezone['offset']); ?>] <?php echo e($timezone['timezone']); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo app('translator')->get('Email Address'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="email" class="form-control" value="<?php echo e($partner->email); ?>"
                                    placeholder="<?php echo app('translator')->get('Enter Email Address'); ?>">


                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo app('translator')->get('Phone Number'); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="<?php echo e($partner->phone); ?>"
                                    placeholder="<?php echo app('translator')->get('Enter Phone Number'); ?>">

                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo app('translator')->get('Address'); ?> <span
                                        class="text-muted text-sm"><?php echo e(trans('(optional)')); ?></span></label>
                                <textarea name="address" class="form-control" rows="3"
                                    placeholder="<?php echo app('translator')->get('Your Address'); ?>"><?php echo e($partner->address); ?></textarea>

                                <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-12 mb-5">
                            <div class="text-right">
                                <button type="submit"
                                    class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3"><?php echo e(trans('Submit')); ?></button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </form>
    </div>


    <?php $__env->startPush('js'); ?>
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
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/partner/profile.blade.php ENDPATH**/ ?>