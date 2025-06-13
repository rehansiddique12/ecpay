<x-partner-layout :title="$pageTitle">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <h3 style="color: #7376f0">{{ $pageTitle }}</h3>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3" style="margin-bottom: 60px;">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ Auth::guard('partner')->user()->balance }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.my_balance_page_title')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['withdrawal_able_amount'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Withdrawalable_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_payment_count'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Total_Deposit_Transactions_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payment_sum'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.total_deposit_amount')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            {{-- me --}}
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="me-3" style="font-size: 2rem; color: #65658b;">
                                <i class="fa fa-hand-holding-usd"></i>
                            </span>
                            <h2 class="text-dark mb-0 font-weight-medium">
                                <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payment_charge'] }}
                            </h2>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                            @lang('partner_basic.Deposit_Charges_en')
                        </h6>
                    </div>
                </div>
            </div>

            {{-- me --}}
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_payout_count'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Total_Withdrawal_Transactions_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payout_sum'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Total_Withdrawal_Amount_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payout_charge'] }}
                                    </h2>
                                </div>

                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Withdrawal_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <h6 class="text-dark font-weight-bold">@lang('Today Statistics')</h6>
            </div>


            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2" style="margin-bottom: 60px;">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_payment_count_today'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Deposit_ransactions_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payment_sum_today'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Deposit_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payment_charge_today'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Deposit_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_payout_count_today'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Withdrawal_Transactions_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payout_sum_today'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Withdrawal_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payout_charge_today'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Withdrawal_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <div class="col-md-12">
                <h6 class="text-dark font-weight-bold">@lang('This Month Statistics')</h6>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_payment_count_current_month'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.This_Month_Deposit_Transactions_en')</
                                        </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payment_sum_current_month'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.This_Month_Deposit_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-muted" style="font-size: 2rem;">
                                <i class="fa fa-hand-holding-usd"></i>
                            </div>
                            <h2 class="text-dark mb-0 font-weight-medium">
                                <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payment_charge_current_month'] }}
                            </h2>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 mt-2 w-100 text-truncate">
                            @lang('partner_basic.This_Month_Deposit_Charges_en')
                        </h6>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_payout_count_current_month'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.This_Month_Withdrawal_Transactions_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payout_sum_current_month'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.This_Month_Withdrawal_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_payout_charge_current_month'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                                    @lang('partner_basic.This_Month_Withdrawal_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <h6 class="text-dark font-weight-bold">@lang('Completed Settlements')</h6>
            </div>

        </div>

        <div class="row justify-content-center">

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3" style="margin-bottom: 60px;">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_settlement_count'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Total_Settlements_Count_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_settlement_sum'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Total_Settlements_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_settlement_charge'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Total_Settlements_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_settlement_count_daily'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Settlements_Count_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_settlement_sum_daily'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Settlements_Amount_en')
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_settlement_charge_daily'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Today_Settlements_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        {{ $transection_data['total_settlement_count_current_month'] }}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Month_Settlements_Count_en')</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_settlement_sum_current_month'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Month_Settlements_Amount_en')</h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup>{{ trans($basic->currency_symbol) }}</sup>{{ $transection_data['total_settlement_charge_current_month'] }}
                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.Month_Settlements_Charges_en')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title">@lang("partner_basic.This_Months_Summary_en")</h4>
                                <div>
                                    <canvas id="line-chart" height="150"></canvas>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h4 class="card-title">@lang('partner_basic.Gateway_Uses_en')</h4>
                                <div>
                                    <canvas id="pie-chart" height="280"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>











    </div>
    @push('js')
        <script src="{{ asset('assets/admin/js/Chart.min.js') }}"></script>

        <script>
            "use strict";

            $(document).on('click', '.user-login', function() {
                var id = $(this).data('id');
                $('.userId').val(id);
            });

            new Chart(document.getElementById("line-chart"), {
                type: 'line',
                data: {
                    labels: @json($statistics['schedule']->keys()),
                    datasets: [{
                        data: @json($statistics['deposit']->values()),
                        label: "Deposits",
                        borderColor: "#9b18cb",
                        fill: false
                    }, {
                        data: @json($statistics['payout']->values()),
                        label: "Payout",
                        borderColor: "#0dd2bb",
                        fill: false
                    }]
                }
            });


            new Chart(document.getElementById("pie-chart"), {
                type: 'pie',
                data: {
                    labels: @json($pieLog->pluck('level')),
                    datasets: [{
                        backgroundColor: ["#6fbbff", "#ff6f62", "#05ffe4", "#98df8a", "#8b6ef3", "#f9dd7e",
                            "#f34da3"
                        ],
                        data: @json($pieLog->pluck('value')),
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return data.labels[tooltipItems.index] + ': ' + data.datasets[0].data[tooltipItems
                                    .index] + '%';
                            }
                        }

                    }
                }
            });


            $(document).on('click', '#details', function() {
                var title = $(this).data('servicetitle');
                var description = $(this).data('description');
                $('#title').text(title);
                $('#servicedescription').text(description);
            });

            $(document).ready(function() {
                let isActiveCronNotification = '{{ $basic->is_active_cron_notification }}';
                if (isActiveCronNotification == 1)
                    $('#cron-info').modal('show');
                $(document).on('click', '.copy-btn', function() {
                    var _this = $(this)[0];
                    var copyText = $(this).parents('.input-group-append').siblings('input');
                    $(copyText).prop('disabled', false);
                    copyText.select();
                    document.execCommand("copy");
                    $(copyText).prop('disabled', true);
                    $(this).text('Coppied');
                    setTimeout(function() {
                        $(_this).text('');
                        $(_this).html('<i class="fas fa-copy"></i>');
                    }, 500)
                });
            })
        </script>
    @endpush
</x-partner-layout>
