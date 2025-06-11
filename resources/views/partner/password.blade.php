<x-partner-layout :title="$pageTitle">
    <style>
        h4 {
            color: #7367f0 !important
        }
    </style>

    <div class="container-fluid p-4">
        <h4 class="card-title mb-5"><i class="icon-key"></i> @lang('partner_basic.password_settings')</h4>
        <form action="" method="post" class="form-body file-upload">
            @csrf
            @method('put')
            <div class="form-body">

                <div class="form-group">
                    <div class="row">
                        <label class="col-lg-2">@lang('partner_basic.current_password_label')</label>
                        <div class="col-lg-6">
                            <input type="password" class="form-control" name="current_password"
                                placeholder="@lang('partner_basic.current_password_placeholder')">

                            @error('current_password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="col-lg-2">@lang('partner_basic.new_password_label')</label>
                        <div class="col-lg-6">
                            <input type="password" name="password" class="form-control"
                                placeholder="@lang('partner_basic.new_password_placeholder')">
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="col-lg-2">@lang('partner_basic.confirm_password_label')</label>
                        <div class="col-lg-6">
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="@lang('partner_basic.confirm_password_placeholder')">
                        </div>
                    </div>

                </div>


                <div class="form-group">
                    <div class="row ">
                        <div class="col-md-6 offset-md-2">
                            <button type="submit"
                                class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">@lang('partner_basic.change_password')</button>
                        </div>
                    </div>
                </div>
            </div>


        </form>
    </div>


    @push('js')

    @endpush
</x-partner-layout>
