@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Dashboard'))

@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection

@section('content')
    {{-- Dashboard Analytics Start --}}
    <section>

        <div class="row">
            <div class="col-lg-8 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0"></div>
                    <div class="card-body">

                        <h3 class="text-primary">{{ \App\Helpers\Helper::greetingMessage()}}</h3>
                        <p class="font-medium-2 mt-2">{{ __('locale.description.dashboard', ['brandname' => config('app.name')]) }}</p>

                        <a href="{{ route('customer.view.charts') }}" class="btn btn-primary mt-3"><i class="feather icon-pie-chart"></i> {{ __('locale.menu.View Charts') }}</a>

                    </div>
                </div>
            </div>

            @can('developers')
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0"></div>
                        <div class="card-body">
                            <p class="font-medium-2">{{ __('locale.developers.your_api_token') }}</p>
                            <span class="font-medium-2 text-primary">{{ Auth::user()->api_token }} </span>
                            <p class="mt-1">{{ __('locale.description.api_token', ['brandname' => config('app.name')]) }}</p>

                            <a href="{{ route('customer.developer.settings') }}" class="btn btn-primary mt-2"><i class="feather icon-terminal"></i>
                                {{ __('locale.developers.manage_your_api_token') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endcan


        </div>

        <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
                @can('view_numbers')
                    <a href="{{ route('customer.numbers.index') }}">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h5>{{ __('locale.labels.get_started_with_numbers') }}</h5>
                                    <p>{{ __('locale.labels.numbers_message') }}</p>
                                </div>

                                <div class="avatar bg-rgba-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-phone text-primary font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                @can('view_sender_id')
                    <a href="{{ route('customer.senderid.index') }}">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h5>{{ __('locale.labels.get_started_with_senderid') }}</h5>
                                    <p>{{ __('locale.labels.sender_id_message') }}</p>
                                </div>

                                <div class="avatar bg-rgba-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-inbox text-primary font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endcan

                <a href="{{ route('customer.sms.campaign_builder') }}">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h5>{{ __('locale.labels.get_started_with_message') }}</h5>
                                <p>{{ __('locale.labels.send_message_to_your_user') }}</p>
                            </div>

                            <div class="avatar bg-rgba-primary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-message-square text-primary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ __('locale.labels.current_plan')  }}</h3>
                        @if(Auth::user()->customer->activeSubscription() == null)
                            <h3 class="mt-1 text-danger">{{ __('locale.subscription.no_active_subscription') }}</h3>
                        @else
                            <p class="mb-2 mt-1 font-medium-2">{!! __('locale.subscription.you_are_currently_subscribed_to_plan',
                                        [
                                                'plan' => auth()->user()->customer->subscription->plan->name,
                                                'price' => \App\Library\Tool::format_price(auth()->user()->customer->subscription->plan->price, auth()->user()->customer->subscription->plan->currency->format),
                                                'remain' => \App\Library\Tool::formatHumanTime(auth()->user()->customer->subscription->current_period_ends_at),
                                                'end_at' => \App\Library\Tool::customerDateTime(auth()->user()->customer->subscription->current_period_ends_at)
                                        ]) !!}</p>
                        @endif
                        <a href="{{ route('customer.subscriptions.index') }}" class="btn btn-primary mt-3"><i class="feather icon-info"></i> {{ __('locale.labels.more_info') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="mb-0">{{ __('locale.plans.sms_sending_credits') }}</h4>
                    </div>
                    <div class="card-content">
                        @if(Auth::user()->customer->activeSubscription() == null)
                            <div class="card-body px-0 pb-0">
                                <h3 class="mt-1 text-danger px-2 mb-4"> {{ __('locale.subscription.no_active_subscription') }}</h3>
                            </div>
                        @else

                            <div class="card-body px-0 pb-0">
                                <div id="sms-sending-credit-chart" class="mt-75"></div>
                                <div class="row text-center mx-0">
                                    <div class="col-6 border-top border-right d-flex align-items-between flex-column py-1">
                                        <p class="mb-50">{{ __('locale.labels.total') }}</p>
                                        <p class="font-large-1 text-bold-700 mb-50">
                                            {{ (Auth::user()->customer->getSendingQuota() == -1) ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->getSendingQuota()) }}
                                        </p>
                                    </div>
                                    <div class="col-6 border-top d-flex align-items-between flex-column py-1">
                                        <p class="mb-50">{{ __('locale.labels.used') }}</p>
                                        <p class="font-large-1 text-bold-700 mb-50">{{ \App\Library\Tool::format_number(Auth::user()->customer->getSendingQuotaUsage()) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </section>
    <!-- Dashboard Analytics end -->
@endsection
@if(Auth::user()->customer->activeSubscription() != null)
@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection


@section('page-script')
    <script>

        let CustomerSendingQuota = "{{ Auth::user()->customer->getSendingQuota() }}";

        if (CustomerSendingQuota === '-1') {
            CustomerSendingQuota = '0'
        } else {
            CustomerSendingQuota = "{{ Auth::user()->customer->getSendingQuotaUsage() / Auth::user()->customer->getSendingQuota() *100 }}"
        }


        $(window).on("load", function () {

            let $success = '#00db89';
            let $strok_color = '#b9c3cd';


            // sms sending credit  Chart
            // -----------------------------

            let smsCreditChartoptions = {
                chart: {
                    height: 250,
                    type: 'radialBar',
                    sparkline: {
                        enabled: true,
                    },
                    dropShadow: {
                        enabled: true,
                        blur: 3,
                        left: 1,
                        top: 1,
                        opacity: 0.1
                    },
                },
                colors: [$success],
                plotOptions: {
                    radialBar: {
                        size: 110,
                        startAngle: -150,
                        endAngle: 150,
                        hollow: {
                            size: '77%',
                        },
                        track: {
                            background: $strok_color,
                            strokeWidth: '50%',
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                offsetY: 18,
                                color: $strok_color,
                                fontSize: '4rem'
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#00b5b5'],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    },
                },
                series: [parseFloat(CustomerSendingQuota).toFixed(1)],
                stroke: {
                    lineCap: 'round'
                },

            }

            let smsCreditChart = new ApexCharts(
                document.querySelector("#sms-sending-credit-chart"),
                smsCreditChartoptions
            );

            smsCreditChart.render();


        });

    </script>

@endsection
@endif
