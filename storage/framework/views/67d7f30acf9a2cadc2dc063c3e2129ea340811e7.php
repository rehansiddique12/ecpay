<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <style>
        tr th {
            color: white !important;
        }
    </style>
    <?php $__env->stopPush(); ?>


    <?php
    $key = 0;
    $selectedGateways = '';
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>

                <form action="<?php echo e(route('admin.apis.commission.add')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" value="<?php echo e($id); ?>" name="category_id" />

                    <?php if(count($commissions) > 0): ?>
                    <?php $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div id="row-p<?php echo e($key); ?>">
                        <br>
                        <div style="border:1px solid;padding:20px">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>From Amount</label>
                                    <input type="hidden" name="id[]" value="<?php echo e($commission->id); ?>">
                                    <input type="number" class="form-control" name="from_amount[]"
                                        value="<?php echo e($commission->from_amount); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label>To Amount</label>
                                    <input type="number" class="form-control" name="to_amount[]"
                                        value="<?php echo e($commission->to_amount); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Deposit %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            name="deposit_percentage[]" value="<?php echo e($commission->deposit_percentage); ?>"
                                            required>
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Withdrawal %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            name="withdrawal_percentage[]"
                                            value="<?php echo e($commission->withdrawal_percentage); ?>" required>
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Settlement %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            name="settlement_percentage[]"
                                            value="<?php echo e($commission->settlement_percentage); ?>" required>
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <?php
                                $selectedGateways = json_decode($commission->gateway_id ?? '[]', true);
                                $selectedtypes = json_decode($commission->type ?? '');
                                ?>
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select select2" multiple name="type[<?php echo e($key); ?>][]" required>
                                        
                                        <option value="Agent" <?php echo e(in_array('Agent', $selectedtypes)? 'selected' : ''); ?>>Agent
                                        </option>
                                        <option value="Personal" <?php echo e(in_array('Personal', $selectedtypes)? 'selected' : ''); ?>>Personal</option>
                                        <option value="Merchant" <?php echo e(in_array('Merchant', $selectedtypes)? 'selected' : ''); ?>>Merchant</option>    
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label>Category</label>
                                    <select class="form-select" id="category<?php echo e($key); ?>" name="category[]">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->name); ?>" <?php echo e($category->name == $commission->category
                                            ? 'selected' : ''); ?>>
                                            <?php echo e($category->name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-5">
                                    <label>Gateway</label>
                                    <select id="settlement_gateway<?php echo e($key); ?>" class="form-select select2" multiple name="settlement_gateway[<?php echo e($key); ?>][]" data-selected='<?php echo json_encode($selectedGateways, 15, 512) ?>'>
                                        
                                    </select>
                                </div>
                                <?php if($key > 0): ?>
                                <div class="col-md-1 mt-4">
                                    <button type="button" class="btn btn-danger cancel-row"
                                        data-row="p<?php echo e($key); ?>">Cancel</button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $key++;
                    ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <?php
                    $key = 1;
                    ?>
                    <div id="row-p0">
                        <br>
                        <div style='border:1px solid;padding:20px'>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>From Amount</label>
                                    <input type="hidden" name="id[]" />
                                    <input type="number" readonly value="0" class="form-control" name="from_amount[]"
                                        required />
                                </div>
                                <div class="col-md-2">
                                    <label>To Amount</label>
                                    <input type="number" class="form-control" name="to_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>Deposit %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            name="deposit_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Withdrawal %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            name="withdrawal_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Settlement %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            name="settlement_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select select2" multiple name="type[0][]" required>
                                        
                                        <option value="Agent">Agent</option>
                                        <option value="Personal">Personal</option>
                                        <option value="Merchant">Merchant</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Category</label>
                                    <select class="form-select" id="category0" name="category[]">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->name); ?>">
                                            <?php echo e($category->name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>


                                <div class="col-md-3">
                                    <label>Gateway</label>
                                    <select id="settlement_gateway0" class="form-select select2" name="settlement_gateway[0][]" multiple
                                        required>
                                       
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div id="add-row"></div>

                    <div class="col-md-12 mb-4 mt-2">
                        <button type="button" class="duplicate-row btn btn-success">Add More</button>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

                <?php if(count($cron_commissions) > 0): ?>
                <hr>
                <h3 style="color: #7367f0">Pending to Update</h3>

                <?php $__currentLoopData = $cron_commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="border:1px solid; padding:20px;" class="mb-3">
                    <div class="row">
                        <?php
                        $selectedGateways = json_decode($commission->gateway_id ?? '');
                        $selectedtypes = json_decode($commission->type ?? '');
                        ?>
                        <div class="col-md-1"><label>From</label><input type="number" class="form-control"
                                value="<?php echo e($commission->from_amount); ?>" readonly /></div>
                        <div class="col-md-1"><label>To</label><input type="number" class="form-control"
                                value="<?php echo e($commission->to_amount); ?>" readonly /></div>
                        <div class="col-md-1"><label>Deposit %</label><input type="number" class="form-control"
                                value="<?php echo e($commission->deposit_percentage); ?>" readonly /></div>
                        <div class="col-md-1"><label>Withdrawal %</label><input type="number" class="form-control"
                                value="<?php echo e($commission->withdrawal_percentage); ?>" readonly /></div>
                        <div class="col-md-1"><label>Settlement %</label><input type="number" class="form-control"
                                value="<?php echo e($commission->settlement_percentage); ?>" readonly /></div>
                        <div class="col-md-1"><label>Category</label><input type="text" class="form-control"
                            value="<?php echo e($commission->category); ?>" readonly /></div>
                        <div class="col-md-2">
                            <label>Type</label>
                            <select class="form-select select2" multiple readonly>
                                <option value="Agent" <?php echo e(in_array('Agent', $selectedtypes)? 'selected' : ''); ?>>Agent
                                </option>
                                <option value="Personal" <?php echo e(in_array('Personal', $selectedtypes)? 'selected' : ''); ?>>Personal</option>
                                <option value="Merchant" <?php echo e(in_array('Merchant', $selectedtypes)? 'selected' : ''); ?>>Merchant</option>    
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label>Gateway</label>
                            <select class="form-select select2" multiple readonly>
                                <?php $__currentLoopData = $allgateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gateway->name); ?>" <?php echo e(in_array($gateway->name, $selectedGateways)
                                    ? 'selected' : ''); ?>>
                                    <?php echo e($gateway->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
        var key = '<?php echo $key?>';
        let $select = $('.select2').select2({
            // placeholder: "Select Partner",
            // allowClear: true,
            selectOnClose: true,
        });

        // Prevent dropdown from opening on clear
        $select.on('select2:unselecting', function (e) {
            $(this).data('unselecting', true);
        });

        $select.on('select2:opening', function (e) {
            if ($(this).data('unselecting')) {
                $(this).removeData('unselecting');
                e.preventDefault();
            }
        });

        $(document).on('click', '.duplicate-row', function() {
            let html = `
                    <div id="row-p${key}">
                        <br>
                        <div style='border:1px solid;padding:20px'>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>From Amount</label>
                                    <input type="hidden" name="id[]" />
                                    <input type="number" class="form-control" name="from_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>To Amount</label>
                                    <input type="number" class="form-control" name="to_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>Deposit %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="deposit_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Withdrawal %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="withdrawal_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Settlement %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="settlement_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select select2" multiple name="type[${key}][]" required>
                                        
                                        <option value="Agent">Agent</option>
                                        <option value="Personal">Personal</option>
                                        <option value="Merchant">Merchant</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Category</label>
                                    <select class="form-select" id="category${key}" name="category[]">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->name); ?>">
                                            <?php echo e($category->name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label>Gateway</label>
                                    <select id="settlement_gateway${key}" class="form-select select2" name="settlement_gateway[${key}][]" multiple required>
                                       
                                    </select>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <button type="button" class="btn btn-danger cancel-row" data-row="p${key}">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
        $('#add-row').append(html);
        $('.select2').select2();
        $('#category'+key).trigger('change');
        key++;
    });

    $(document).on('click', '.cancel-row', function() {
        const rowId = $(this).data('row');
        $(`#row-${rowId}`).remove();
    });


    let gateways = <?php echo json_encode($gateways, 15, 512) ?>;

    // Load gateway options
    function loadGateways(category, gatewaySelect, preselected = []) {
        gatewaySelect.empty();

        if (gateways[category]) {
            $.each(gateways[category], function (index, gateway) {
                let selected = preselected.includes(gateway) ? 'selected' : '';
                gatewaySelect.append('<option value="' + gateway + '" ' + selected + '>' + gateway + '</option>');
            });
        }

        gatewaySelect.trigger('change'); // Refresh Select2
    }

    // On change of any category select field
    $(document).on('change', 'select[id^="category"]', function () {
        let categorySelect = $(this);
        let key = categorySelect.attr('id').replace('category', '');
        let selectedCategory = categorySelect.val();
        let gatewaySelect = $('#settlement_gateway' + key);

        // Read selected gateways from data-selected attribute
        let preselected = gatewaySelect.data('selected') || [];

        loadGateways(selectedCategory, gatewaySelect, preselected);
    });

    // Trigger change on load for initial values
    $(document).ready(function () {
        $('select[id^="category"]').each(function () {
            $(this).trigger('change');
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



<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/commission.blade.php ENDPATH**/ ?>