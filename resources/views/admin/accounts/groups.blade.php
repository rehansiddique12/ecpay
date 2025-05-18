<style>

.select2-container {
    z-index: 999999 !important;
}
</style>
<!-- In <head> -->



<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal"
                            data-bs-target="#groupModal">
                            Add Group
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Group Name</th>
                                <th scope="col">Accounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $group)
                            <tr>
                                <td>{{$group->group_name}}</td>
                                <td>{{$group->pairs}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Group Modal -->
        <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="groupModalLabel">Add Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('admin.accounts.addpairs') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="groupName" class="form-label">Group Name</label>
                                <input type="text" name="group_name" class="form-control" id="groupName"
                                    placeholder="Enter group name">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label" >Select Pairs</label>
                                <select id="" name="pairs[]" class="form-select select2" multiple z-index="1000">
                                    @foreach($records as $accounts)
                                    <option value="{{$accounts->account_no}}"> {{$accounts->account_no}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Group</button>
                            </div>
                        </form>

                    </div>


                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function () {
    // let initialized = false;

    // $('#groupModal').on('shown.bs.modal', function () {
    //     if (!initialized) {
    //         $('#groupPairs').select2({
    //             dropdownParent: $('#groupModal'),
    //             placeholder: "Search & select pairs",
    //             width: '100%'
    //         });
    //         initialized = true;
    //     }
    // });


});
</script>
