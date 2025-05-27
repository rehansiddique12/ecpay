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
    <?php $__env->stopPush(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    
                    
                    

                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Partner'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Group Name'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Group ID'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($item['id']); ?></td>
                                    <td><?php echo e($partners[$item['api_id']] ?? ''); ?></td>
                                    <td><?php echo e($item['group_name']); ?></td>
                                    <td><?php echo e($item['group_username']); ?></td>

                                    <td class="text-lg-center text-right">
                                        <form class="toggle-status-form d-inline" data-id="<?php echo e($item->id); ?>" data-url="<?php echo e(route('admin.groups.toggleStatus', $item->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="button" class="btn btn-sm toggle-status-btn <?php echo e($item->status ? 'btn-success' : 'btn-danger'); ?>">
                                                <i class="fa fa-circle me-1"></i> <?php echo e($item->status ? __('Active') : __('Inactive')); ?>

                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="icon-base ti tabler-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <?php if(adminAccessRoute(config('role.partners.access.delete'))): ?>
                                                <form action="<?php echo e(route('admin.groups.delete', $item['id'])); ?>"
                                                    method="POST"
                                                    class="delete-form"
                                                    data-id="<?php echo e($item['id']); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-icon edit_button">
                                                        <i class="icon-base ti tabler-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                


                                                
                                                <?php if(adminAccessRoute(config('role.partners.access.edit'))): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon edit_button"
                                                    data-id="<?php echo e($item['id']); ?>"
                                                    data-api_id="<?php echo e($item['api_id']); ?>"
                                                    data-group_name="<?php echo e($item['group_name']); ?>"
                                                    data-group_username="<?php echo e($item['group_username']); ?>"
                                                    data-status="<?php echo e($item['status']); ?>"
                                                    data-route="<?php echo e(route('admin.groups.update', $item['id'])); ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal">
                                                    <i class="icon-base ti tabler-user me-1"></i> Edit
                                                </button>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                    </td>
                                </tr>

                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Single Edit Modal -->
    <div id="editModal" class="modal modal-top fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title"><?php echo app('translator')->get('Edit Record'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" id="edit_id">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Partner</label>
                                    <select class="form-control" name="api_id" id="edit_api_id" required>
                                        <option value="">Select Partner</option>
                                        <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Group Name</label>
                                    <input type="text" class="form-control" name="group_name" id="edit_group_name" required />
                                </div>

                                <div class="form-group">
                                    <label>Group ID</label>
                                    <input type="text" class="form-control" name="group_username" id="edit_group_username" required />
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="1"><?php echo app('translator')->get('Active'); ?></option>
                                        <option value="0"><?php echo app('translator')->get('Inactive'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Update'); ?></button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>


<?php $__env->startPush('js'); ?>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // stop form

                const itemId = form.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `This will permanently delete item ID: ${itemId}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // proceed to submit
                    }
                });
            });
        });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit_button');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const api_id = this.getAttribute('data-api_id');
                    const group_name = this.getAttribute('data-group_name');
                    const group_username = this.getAttribute('data-group_username');
                    const status = this.getAttribute('data-status');
                    const route = this.getAttribute('data-route');

                    // Set the form action
                    document.getElementById('editForm').setAttribute('action', route);

                    // Fill form values
                    document.getElementById('edit_api_id').value = api_id;
                    document.getElementById('edit_group_name').value = group_name;
                    document.getElementById('edit_group_username').value = group_username;
                    document.getElementById('edit_status').value = status;
                });
            });
        });

      document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.toggle-status-form');

        forms.forEach(form => {
            const button = form.querySelector('.toggle-status-btn');
            button.addEventListener('click', function () {
                const url = form.getAttribute('data-url');
                const token = form.querySelector('input[name="_token"]').value;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Refresh page
                    } else {
                        alert('Failed to update status.');
                    }
                })
                .catch(() => alert('Something went wrong.'));
            });
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/group/api.blade.php ENDPATH**/ ?>