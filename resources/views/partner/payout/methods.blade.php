<x-partner-layout >

<center>
    <div class="w-full h-full" style="margin-top:10%; margin-bottom:10%">
        <div class="row ">
            <div class="col-md-6">
                <a href="deposit">
                    <div>
                        <!-- <i class="fas fa-hand-holding-usd" style="font-size:200px;color:red"></i> -->
                        <p style="font-size:80px;"><span class="badge badge-success" style="background-color:rgba(25, 192, 25, 0.781);">Deposit</span></p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="withdrawal">
                    <div>
                        <!-- <i class="fas fa-credit-card" style="font-size:200px;color:blue"></i> -->
                        <p style="font-size:80px;"><span class="badge badge-primary">Withdrawal</span></p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</center>


@push('script')
@endpush
</x-partner-layout>
