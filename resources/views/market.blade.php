@extends('layouts.master')

@section('title', 'Markets')

@section('stylesheet')
    <link rel="stylesheet" media="screen, print" href="/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
    <link rel="stylesheet" media="screen, print" href="/css/notifications/sweetalert2/sweetalert2.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">Markets</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
						<div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col">
                                        <div id="panel-market" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Markets <span class="fw-300"><i>Calendar</i></span>
                                                </h2>
												<div class="panel-toolbar">
													<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
													<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
													<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
												</div>
                                            </div>
                                            <div class="panel-container show">
                                                <div class="panel-content d-flex justify-content-center">
                                                    <div id="datepicker-market"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</main>
@endsection

@section('javascript')
    <script src="/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="/moment/min/moment.min.js"></script>
    <script src="/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>

    <script type="text/javascript">
        $(document).ready( function() {
            initApp.destroyNavigation(myapp_config.navHooks);
            $('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
            initApp.buildNavigation(myapp_config.navHooks);

            $('#datepicker-market').datepicker({
                todayHighlight: true,
                multidate: true,
                templates: controls,
                format: "yyyy-mm-dd",
                daysOfWeekDisabled: [0,6]
            })
            .datepicker('setDates', @json(
                $markets->map( function($item) {
                    return $item->closing_at->toDateString();
                })
                ->toArray()
            ))
            .on('changeDate', function(e) {
                Swal.showLoading();

                $.ajax({
                    method: 'POST',
                    url: @json(route('api.markets.store')),
                    data: {
                        api_token: $(document).find('meta[name="api-token"]').attr('content'),
                        dates: e.dates.map(date => moment(date).format())
                    }

                })
                .done( function(response) {
                    Swal.close();

                })
                .fail( function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Oops...', jqXHR.responseJSON.message, 'error')
                    .then( function(result) {
                        window.location.reload();
                    });
                });

            });

        })

    </script>
@endsection
