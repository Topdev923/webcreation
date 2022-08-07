@extends($extend_blade)

@section('content')
    @include($activeTemplate.'partials.user-breadcrumb')



    <!-- dashboard-section start -->
    <section class="dashboard-section pt-150 pb-150">
        <div class="container">
            
            <div class="row mb-none-50">
                <div class="col-md-12">
                    <div class="text-right mb-5">
                        <a href="{{ route('user.interest.log') }}" class="btn btn-primary">@lang('My Investments')</a>
                    </div>
                </div>
                @foreach($plans as $k => $data)
            @php
            $time_name = \App\Models\TimeSetting::where('time', $data->times)->first();
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="pricing-item mb-50">
                    <div class="pricing-item__header">
                        <span class="package__price text-shadow">{{@$data->name}}</span>

                        <div class="package__offer text-shadow">
                            @if($data->fixed_amount == 0)
                            {{__($general->cur_sym)}}{{__($data->minimum)}}  - {{__($general->cur_sym)}}{{__($data->maximum)}}
                            @else
                            {{__($general->cur_sym)}}{{__($data->maximum)}}
                            @endif
                        </div>





                    </div>
                    <div class="pricing-item__content">
                        <ul class="package__feature-list">





                            <li class="text-shadow"> @lang('Return') {{__($data->interest)}}{{($data->interest_status == 1) ? '%': __($general->cur_text)}} </li>



                            <li class="text-shadow">@lang('Every') {{__($time_name->name)}}</li>


                            <li class="text-shadow"> @lang('For')  @if($data->lifetime_status == 0) {{__($data->repeat_time)}} {{__($time_name->name)}} @else @lang('Lifetime') @endif </li>
                            <li class="text-shadow">@if($data->lifetime_status == 0) 
                                @lang('Total')   {{__($data->interest*$data->repeat_time)}}{{($data->interest_status == 1) ? '%': __($general->cur_text)}}

                                @if($data->capital_back_status == 1)
                                +<span class="badge badge-success">@lang('Capital')</span> 
                                @endif
                                @else
                                @lang('Lifetime Earning') 
                            @endif </li>




                        </ul>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#depoModal" data-resource="{{$data}}"
                        class="btn btn-primary btn-small mt-30 investButton">@lang('Invest now')</a>
                    </div>
                </div><!-- pricing-item end -->
            </div>
            @endforeach


            </div>
        </div>
    </section>
    <!-- pricing-section end -->
@endsection

@push('renderModal')


    <!-- Modal -->
    <div class="modal fade" id="depoModal" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-bg">
                <div class="modal-header">
                    <strong class="modal-title text-white" id="ModalLabel">
                        @guest
                            @lang('At first sign in your account')
                        @else
                            @lang('Confirm to invest on') <span class="planName"></span>
                        @endguest
                    </strong>
                    <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <form action="{{route('user.buy.plan')}}" method="post" class="register">
                    @csrf
                    @auth
                        <div class="modal-body">

                            <div class="form-group">
                                <h6 class="text-center investAmountRenge"></h6>

                                <p class="text-center mt-1 interestDetails"></p>
                                <p class="text-center interestValidaty"></p>

                                <div class="form-group ">
                                    <strong class="text-white mb-2 d-block">@lang('Select wallet')</strong>
                                    <select class="form-control" name="wallet_type">
                                        <option value="deposit_wallet">@lang('Deposit Wallet - '.$general->cur_sym.getAmount(auth()->user()->deposit_wallet))</option>
                                        <option value="interest_wallet">@lang('Interest Wallet -'.$general->cur_sym.getAmount(auth()->user()->interest_wallet))</option>
                                        <option value="checkout">@lang('Checkout')</option>
                                    </select>
                                </div>
                                <input type="hidden" name="plan_id" class="plan_id">

                                <div class="form-group">
                                    <strong class="text-white mb-2 d-block">@lang('Invest Amount')</strong>
                                    <input type="text" class="form-control fixedAmount" id="fixedAmount" name="amount"
                                           value="{{old('amount')}}"
                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                           autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger action-btn"
                                    data-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn-primary action-btn">@lang('Yes')</button>
                        </div>
                    @endauth

                    @guest
                        <div class="modal-footer">
                            <a href="{{route('user.login')}}" type="button"
                               class="btn btn-success custom-success w-100">@lang('At first sign in your account')</a>
                        </div>
                    @endguest
                </form>
            </div>
        </div>
    </div>
@endpush




@push('script')
    <script>
        (function ($) {
            "use strict";
            $(document).on('click','.investButton',function () {
                var data = $(this).data('resource');
                var symbol = "{{__($general->cur_sym)}}";
                var currency = "{{__($general->cur_text)}}";

                $('#mySelect').empty();

                if (data.fixed_amount == '0') {
                    $('.investAmountRenge').text(`@lang('invest'): ${symbol}${data.minimum} - ${symbol}${data.maximum}`);
                    $('.fixedAmount').val('');
                    $('#fixedAmount').attr('readonly', false);

                } else {
                    $('.investAmountRenge').text(`@lang('invest'): ${symbol}${data.fixed_amount}`);
                    $('.fixedAmount').val(data.fixed_amount);
                    $('#fixedAmount').attr('readonly', true);
                }

                if (data.interest_status == '1') {
                    $('.interestDetails').html(`<strong> @lang('Interest'): ${data.interest} % </strong>`);
                } else {
                    $('.interestDetails').html(`<strong> @lang('Interest'): ${data.interest} ${currency}  </strong>`);
                }
                if (data.lifetime_status == '0') {
                    $('.interestValidaty').html(`<strong>  @lang('per') ${data.times} @lang('hours') ,  ${data.repeat_time} @lang('times')</strong>`);
                } else {
                    $('.interestValidaty').html(`<strong>  @lang('per') ${data.times} @lang('hours'),  @lang('life time') </strong>`);
                }

                $('.planName').text(data.name);
                $('.plan_id').val(data.id);
            });



        })(jQuery);

    </script>
@endpush