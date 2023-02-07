@extends('layouts.master')

@section('title', 'Closing Rates - Sales')

@section('stylesheet')
        <link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
@endsection

@section('content')
                    <!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">Closing Rates</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 d-flex justify-content-start">
                                <div class="subheader">
                                    <h1 class="subheader-title">
                                        <i class='subheader-icon fal fa-table'></i> Closing Rates
                                    </h1>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 d-flex justify-content-end">
                                <div id="panel-sales-blotter-export" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form>
                                                <div class="form-group">
                                                    <label class="form-label" for="next-market-at">
                                                        Next Market At
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" name="next_market_at" class="form-control" value="{{
                                                            $market->closing_at->toFormattedDateString()
                                                        }}" readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text fs-xl">
                                                                <i class="fal fa-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
@endif
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-closing-rate-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>Closing Rate <span class="fw-300"><i>Table</i></span></h2>
                                        <div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <!-- datatable start -->
                                            <table id="dt-advance" class="table table-bordered table-hover table-striped w-100"></table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                    <!-- Modal default-->
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('closing-rates.store') }}" method="post">
                                    @csrf
                                    
                                    <input type="hidden" name="next-market-id" value="{{ $market->id }}">
                                    <div class="modal-header">
                                        <h4 class="modal-title">
                                            Closing Rate
                                            <small class="m-0 text-muted">Input Closing Rate</small>
                                        </h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="form-label" for="currency">Currency</label>
                                            <input type="text" name="currency-code" class="form-control" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="bid">Bid</label>
                                            <input type="hidden" name="buying-rate" required>
                                            <input type="text" class="form-control" autocomplete="off" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="ask">Ask</label>
                                            <input type="hidden" name="selling-rate" required>
                                            <input type="text" class="form-control" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->
@endsection

@section('javascript')
        <script src="/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="/moment/min/moment.min.js"></script>
        <script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>

        <script type="text/javascript">
            $(document).ready( function() {
                initApp.destroyNavigation(myapp_config.navHooks);
                $('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
                $('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
                initApp.buildNavigation(myapp_config.navHooks);

                $.fn.dataTable.ext.errMode = 'throw';

                dtAdvance = $('#dt-advance').DataTable({
                    responsive: true,
                    fixedHeader: {
                        headerOffset: $(document.body)
                            .hasClass('header-function-fixed') ? $('header.page-header').outerHeight() : 0
                    },
                    paging: false,
                    lengthChange: false,
                    bInfo: false,
                    order: [],
@if(auth()->user()->can('create', 'App\ClosingRate') && $market->closing_at->startOfDay()->isFuture() && $now->isAfter($close))
                    select: {
                        style: 'single'
                    },
@endif
                    ajax: {
                        url: @json(route('api.closing-rates.index')),
                        type: 'GET',
                        dataType: 'json',
                        data: function(data) {
                            return ({
                                api_token: $(document).find('meta[name="api-token"]').attr('content'),
                                next_market_at: moment($(document).find('input[name="next_market_at"]').val(), 'll').format('YYYY-M-D')
                            });
                        }
                    },
                    columns: [{
                        title: 'Currency',
                        className: 'text-center',
                        data: 'primary_code'
                    },
                    {
                        title: 'Bid',
                        data: 'buying_rate',
                        className: 'text-right',
                        render: function(data, type, row, meta) {
                            if (data) {
                                data = parseFloat(data).toLocaleString('en-US');
                            }

                            return (data || null);
                        }
                    },
                    {
                        title: 'Ask',
                        data: 'selling_rate',
                        className: 'text-right',
                        render: function(data, type, row, meta) {
                            if (data) {
                                data = parseFloat(data).toLocaleString('en-US');
                            }

                            return (data || null);
                        }
                    },
                    {
                        title: 'Mid',
                        data: 'mid_rate',
                        className: 'text-right',
                        render: function(data, type, row, meta) {
                            if (data) {
                                data = parseFloat(data).toLocaleString('en-US', {
                                    maximumFractionDigits: 2
                                });
                            }

                            return (data || null);
                        }
                    },
                    {
                        title: 'Threshold',
                        data: 'threshold',
                        className: 'text-right',
                        render: function(data, type, row, meta) {
                            if (data) {
                                data = parseFloat(data).toLocaleString('en-US', {
                                    maximumFractionDigits: 2
                                });
                            }

                            return (data || null);
                        }
                    },
                    {
                        title: 'Created At',
                        data: 'created_at',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data) {
                                data = moment(data).format('ll');
                            }

                            return (data || null);
                        }
                    },
                    {
                        title: 'Status',
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            data = {
                                value: data,
                                element: document.createElement('i')
                            };

                            if (data.value) {
                                data.element.classList.add('fal', 'fa-check-circle', 'fa-2x');
                            } else {
                                data.element.classList.add('fal', 'fa-times-circle', 'fa-2x');
                            }

                            return data.element.outerHTML;
                        }
                    }],
                    language: {
                        infoFiltered: ''
                    },
                    createdRow: function(row, data, dataIndex) {
@if(auth()->user()->can('create', 'App\ClosingRate') && $market->closing_at->startOfDay()->isFuture() && $now->isAfter($close))
                        $(row).addClass('pointer');

@endif
                        if ($(row).children().last().children().hasClass('fa-check-circle')) {
                            $(row).children().last().addClass('text-success');

                        } else {
                            $(row).children().last().addClass('text-danger');
                        }
                    },
                    drawCallback: function(settings) {
                        if (
                            settings.oInstance.fnSettings().oInit.select && (
                                moment($(document).find('input[name="next_market_at"]').val(), 'll').startOf('day').isAfter()
                            )
                        ) {
                            settings.oInstance.api().select.style('single');
                            settings.oInstance.api().rows().every( function(rowIdx, tableLoop, rowLoop) {
                                this.rows(rowIdx).nodes().to$().addClass('pointer');
                            })
                        }
                    },
                    initComplete: function(settings, json) {
                        settings.oInstance.api().columns().header().to$().addClass('text-center');
                        settings.oInstance.api().table().header().classList.add('thead-dark');

                        window.setInterval( function () {
                            settings.oInstance.api().ajax.reload();
                        }, 15000 );
                    }
                })

            })

            $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('show.bs.modal', function(e) {
                e.data = dtAdvance.row({
                    selected: true
                });

                e.data = e.data.data();

                $(e.currentTarget).find('[name="currency-code"]').val(e.data.primary_code);
                $(e.currentTarget).find('[name="buying-rate"]').next().val(e.data.buying_rate);
                $(e.currentTarget).find('[name="buying-rate"]').next().trigger('input');
                $(e.currentTarget).find('[name="selling-rate"]').next().val(e.data.selling_rate);
                $(e.currentTarget).find('[name="selling-rate"]').next().trigger('input');
            })

            $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('hidden.bs.modal', function(e) {
                dtAdvance.rows().deselect();
            })

        </script>
@endsection
