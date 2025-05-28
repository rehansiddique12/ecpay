<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>


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

label{
    margin-bottom: 5px;
}

   </style>

    <div class="container-fluid">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e(trans($error)); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary shadow">
                    <div class="card-body">
                        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                        <form method="post" action=""
                              class="needs-validation base-form" novalidate="" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label><?php echo e(trans('Name')); ?></label>
                                    <input type="text" class="form-control "
                                           name="name"
                                           value="<?php echo e(old('name')); ?>" required="">
                                    <?php if($errors->has('name')): ?>
                                        <span class="invalid-text">
                                                <?php echo e(trans($errors->first('name'))); ?>

                                            </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-md-4">
                                    <label><?php echo e(trans('Currency')); ?></label>
                                    <input type="text" class="form-control "
                                           name="currency"
                                           value="<?php echo e(old('currency')); ?>" required="required">

                                    <?php if($errors->has('currency')): ?>
                                        <span class="invalid-text">
                                                <?php echo e(trans($errors->first('currency'))); ?>

                                            </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Type</label>
                                    <select class="form-control" name="type" required>
                                        <option value="Bank">Bank</option>
                                        <option value="E-Wallet">E-Wallet</option>
                                        <option value="Crypto">Crypto</option>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-4">
                                    <label><?php echo e(trans('Minimum Deposit Amount')); ?></label>
                                    <div class="input-group ">
                                        <input type="text" class="form-control "
                                               name="minimum_deposit_amount"
                                               value="<?php echo e(old('minimum_deposit_amount')); ?>"
                                               required="">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <?php echo e($basic->currency ?? trans('USD')); ?>

                                            </div>
                                        </div>
                                    </div>

                                    <?php if($errors->has('minimum_deposit_amount')): ?>
                                        <span class="invalid-text">
                                                <?php echo e(trans($errors->first('minimum_deposit_amount'))); ?>

                                            </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group col-md-4 col-4">
                                    <label><?php echo e(trans('Maximum Deposit Amount')); ?></label>
                                    <div class="input-group ">
                                        <input type="text" class="form-control "
                                               name="maximum_deposit_amount"
                                               value="<?php echo e(old('maximum_deposit_amount')); ?>"
                                               required="">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <?php echo e($basic->currency ?? trans('USD')); ?>

                                            </div>
                                        </div>
                                    </div>

                                    <?php if($errors->has('maximum_deposit_amount')): ?>
                                        <span class="invalid-text">
                                                <?php echo e(trans($errors->first('maximum_deposit_amount'))); ?>

                                            </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            

                            <div class="row justify-content-between">
                                <div class="col-sm-6 col-md-4">
                                    <div class="image-input dropzone-container mt-5">
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
                                
                            </div>
                            <div class="row mt-3 justify-content-between">
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get('Status'); ?></label>
                                        <div class="form-check form-switch d-flex align-items-center">
                                            <span id="disableText" class="me-12 text-primary"><?php echo app('translator')->get('No'); ?></span>
                                            <input class="form-check-input" type="checkbox" id="statusSwitch"
                                                name="status" value="1">
                                            <span id="enableText" class="ms-2 text-secondary"><?php echo app('translator')->get('Yes'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>



                          <div class="d-flex gap-5">
                            <button type="submit" class="btn btn-rounded btn-primary btn-block mt-6"><?php echo app('translator')->get('Save Changes'); ?></button>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <a href="javascript:void(0)" class="btn btn-success float-right mt-3 " id="generate"><i
                                            class="fa fa-plus-circle"></i> <?php echo e(trans('Add Field')); ?></a>
                                </div>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php $__env->startPush('js'); ?>
    <script>
        "use strict";

        $(document).ready(function () {
            setCurrency();
            $(document).on('change', 'input[name="currency"]', function (){
                setCurrency();
            });

            function setCurrency() {
                let currency = $('input[name="currency"]').val();
                $('.set-currency').text(currency);
            }

            $(document).on('click', '.copy-btn', function () {
                var _this = $(this)[0];
                var copyText = $(this).parents('.input-group-append').siblings('input');
                $(copyText).prop('disabled', false);
                copyText.select();
                document.execCommand("copy");
                $(copyText).prop('disabled', true);
                $(this).text('Coppied');
                setTimeout(function () {
                    $(_this).text('');
                    $(_this).html('<i class="fas fa-copy"></i>');
                }, 500)
            });
        })



        $(document).ready(function (e) {

            $("#generate").on('click', function () {
                var form = `<div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="field_name[]" class="form-control " type="text" value="" required placeholder="<?php echo e(trans('Field Name')); ?>">

                                        <select name="type[]"  class="form-control  ">
                                            <option value="text"><?php echo e(trans('Input Text')); ?></option>
                                            <option value="textarea"><?php echo e(trans('Textarea')); ?></option>
                                            <option value="file"><?php echo e(trans('File upload')); ?></option>
                                        </select>

                                        <select name="validation[]"  class="form-control  ">
                                            <option value="required"><?php echo e(trans('Required')); ?></option>
                                            <option value="nullable"><?php echo e(trans('Optional')); ?></option>
                                        </select>

                                        <span class="input-group-btn">
                                            <button class="btn btn-danger delete_desc" type="button">
                                                <i class="icon-base ti tabler-ban me-1"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div> `;

                $('.addedField').append(form)
            });


            $(document).on('click', '.delete_desc', function () {
                $(this).closest('.input-group').parent().remove();
            });


            $('#image').change(function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            $('.summernote').summernote({
                height: 250,
                callbacks: {
                    onBlurCodeview: function() {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });
        });



    </script>
    <script>
        $(document).ready(function (e) {
            "use strict";

            $('#image').change(function(){
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });


            $('select').select2({
                selectOnClose: true
            });

        });
    </script>
<?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment_methods/manual/create.blade.php ENDPATH**/ ?>