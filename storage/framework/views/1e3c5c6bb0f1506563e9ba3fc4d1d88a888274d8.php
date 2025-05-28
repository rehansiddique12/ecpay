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
        /* Fix for Select2 inside Bootstrap modal */
        .select2-container {
            z-index: 99999 !important;
        }
    </style>
    <?php $__env->stopPush(); ?>
    <?php
    $currentRoute = Route::currentRouteName();
    ?>

     <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <?php if(adminAccessRoute(config('role.account_management.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.ewallet.accounts.details')); ?>" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_management.access.add'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.e_wallet_accounts.access.edit'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.on_off_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.on_off_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">On/Off Accounts</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_group.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.account_group')); ?>" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.gateways.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.gateway')); ?>" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.categories.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_category')); ?>" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>


                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    <?php if(adminAccessRoute(config('role.account_group.access.add'))): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#groupModal"
                        id="newCategoryButton">
                        Add Account Group
                    </button>
                    <?php endif; ?>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Group Name</th>
                                <th scope="col">Accounts</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($group->name); ?></td>
                                <td>
                                    <?php if($group->accounts->isNotEmpty()): ?>
                                    <?php $__currentLoopData = $group->accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-primary me-1"><?php echo e($account->account_no); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                    <span class="text-muted">No accounts</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(adminAccessRoute(config('role.account_group.access.edit'))): ?>
                                    <button class="btn btn-sm btn-warning editGroupBtn" data-id="<?php echo e($group->id); ?>"
                                        data-name="<?php echo e($group->name); ?>"
                                        data-accounts="<?php echo e($group->accounts->pluck('id')->implode(',')); ?>">
                                        Edit

                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>



                </div>
            </div>


        </div>
    </div>


    <!-- Group Modal -->
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupModalLabel">Add Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="groupForm" action="<?php echo e(route('admin.accounts.addpairs')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="groupName" class="form-label">Group Name</label>
                            <input type="text" name="group_name" class="form-control" id="groupName"
                                placeholder="Enter group name">
                        </div>

                        <div class="mb-3">
                            <label for="paris" class="form-label">Select Pairs</label>
                            <select id="paris" name="pairs[]" class="form-select select2" z-index="99999" multiple>
                                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($accounts->id); ?>"> <?php echo e($accounts->account_no); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="submitGroupBtn">Save Group</button>
                        </div>
                    </form>

                </div>


            </div>
        </div>
    </div>

    <!-- Edit Group Modal -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGroupForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" id="editGroupId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGroupModalLabel">Edit Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editGroupName" class="form-label">Group Name</label>
                            <input type="text" name="edit_group_name" class="form-control" id="editGroupName"
                                placeholder="Enter group name">
                            <small class="text-danger error-text group_name_error"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Pairs</label>
                            <select name="edit_pairs[]" id="editGroupAccounts" class="form-select select2" multiple>
                                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($accounts->id); ?>"><?php echo e($accounts->account_no); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger error-text pairs_error"></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateGroupBtn">Update Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('groupForm');
            const submitBtn = document.getElementById('submitGroupBtn');
            const modal = new bootstrap.Modal(document.getElementById('groupModal'));

            form.addEventListener('submit', function (e) {
                // Disable the button to prevent multiple clicks
                submitBtn.disabled = true;
                submitBtn.innerText = 'Saving...';

                // Optionally hide the modal immediately
                modal.hide();
            });
        });

$(document).ready(function () {
     // Initialize Add Group Modal select2
                 $('#groupModal').on('shown.bs.modal', function () {
                    let $select = $(this).find('.select2');

                    // Prevent re-initialization
                    if (!$select.hasClass('select2-hidden-accessible')) {
                        $select.select2({
                            dropdownParent: $('#groupModal'),
                            allowClear: true
                        });

                        $select.on('select2:unselecting', function (e) {
                            $(this).data('unselecting', true);
                        });

                        $select.on('select2:opening', function (e) {
                            if ($(this).data('unselecting')) {
                                $(this).removeData('unselecting');
                                e.preventDefault();
                            }
                        });
                    }
                });

                // Initialize Edit Group Modal select2
                $('#editGroupModal').on('shown.bs.modal', function () {
                    let $editSelect = $(this).find('.select2');
                    if (!$editSelect.hasClass('select2-hidden-accessible')) {
                        $editSelect.select2({
                            dropdownParent: $('#editGroupModal'),
                            allowClear: true
                        });

                        $editSelect.on('select2:unselecting', function (e) {
                            $(this).data('unselecting', true);
                        });

                        $editSelect.on('select2:opening', function (e) {
                            if ($(this).data('unselecting')) {
                                $(this).removeData('unselecting');
                                e.preventDefault();
                            }
                        });
                    }
                });

    // Open edit modal
       $(document).on('click', '.editGroupBtn', function () {
        const groupId = $(this).data('id');
        const groupName = $(this).data('name');
        const accounts = $(this).data('accounts')?.toString().split(',') || [];

        // Set form values
        $('#editGroupId').val(groupId);
        $('#editGroupName').val(groupName);

        const $select = $('#editGroupAccounts');

        // Reset and set selected options
        $select.val(null).trigger('change'); // Clear previous selections
        $select.val(accounts).trigger('change');

        // Set form action dynamically
        $('#editGroupForm').attr('action', '/admin/accounts/update-group/' + groupId);

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById('editGroupModal'));
        editModal.show();
    });

    // Submit form via AJAX
    $('#editGroupForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        clearErrors();

        $.ajax({
            type: "POST",
            url: "<?php echo e(route('admin.accounts.updateGroup')); ?>",
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $('#editGroupModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $('.' + key + '_error').text(value[0]);
                });
            }
        });
    });

    function clearErrors() {
        $('.error-text').text('');
    }
});


    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/groups.blade.php ENDPATH**/ ?>