<x-partner-layout :title="$pageTitle">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center card-title mb-3"><i class="icon-key"></i> @lang('Two Step Verification')</h4>
                <form action="" method="post" class="form-body file-upload" enctype="multipart/form-data">
                    @csrf

                    @if($status == "No")
                        {{-- <div class="form-group"> --}}
                            <!-- QR Code Display -->
                            <div class="text-center">
                                <div class="qr-code-container">
                                    {!! $qrCodeUrl !!}
                                </div>
                            {{-- </div> --}}
                        </div>

                        <div class="form-group">
                            <label>@lang('OTP') <span class="text-danger">*</span></label>
                            <input type="text" name="otp" class="form-control" required />
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">{{ trans('Enable') }}</button>
                        </div>
                    @else
                        <br>
                        <h1 class="text-center">Two Step Verification Successfully Enabled</h1>
                        <br>
                    @endif

                </form>
            </div>
        </div>
    </div>
<style>
.qr-code-container svg {
    width: 20% !important; /* Adjust this as needed */
    height: auto;
}
</style>
</x-partner-layout>
