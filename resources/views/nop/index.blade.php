@extends('layouts.master')

@section('title', 'NOP - Interbank')

@section('stylesheet')
    <link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
    <link rel="stylesheet" media="screen, print" href="/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
@endsection

@section('content')
            <!-- BEGIN Page Content -->
            <!-- the #js-page-content id is needed for some plugins to initialize -->
            <main id="js-page-content" role="main" class="page-content">
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                    <li class="breadcrumb-item"><a href="#interbank">Interbank</a></li>
                    <li class="breadcrumb-item active">NOP</li>
                    <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                </ol>
                <div class="subheader">
                    <h1 class="subheader-title"><i class='subheader-icon ni ni-wallet'></i> NOP</h1>
                </div>
@if (session('status'))
                <div id="alert-dismissible" class="panel-container show">
                    <div class="panel-content">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="fal fa-times"></i></span>
                            </button>
                            <strong>Well Done!</strong> {{ session('status') }}
                        </div>
                    </div>
                </div>
@endif
                <div class="row">
                    <div class="col-xl-12">
                        <div id="panel-nop-index" class="panel">
                            <div class="panel-hdr bg-faded">
                                <h2>NOP <span class="fw-300"><i>Table</i></span></h2>
                                <div class="panel-toolbar">
                                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                    <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                </div>
                            </div>
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <form action="{{ route('interbank-nop.excel') }}" method="post" target="_blank">
                                        @csrf
                                        
                                        <div class="form-row d-flex justify-content-between align-items-center mb-3">
                                            <div class="form-group col-md-6 mb-0">
                                                <label class="form-label" for="datepicker-from">Date from</label>
                                                <div class="input-group">
                                                    <input type="text" name="date_from" class="form-control datepicker" placeholder="Select date" data-date-end-date="{{ \Carbon\Carbon::today()->toDateString() }}" readonly>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text fs-xl">
                                                            <i class="fal fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 mb-0">
                                                <label class="form-label" for="datepicker-to">to</label>
                                                <div class="input-group">
                                                    <input type="text" name="date_to" class="form-control datepicker" placeholder="Select date" data-date-end-date="{{ \Carbon\Carbon::today()->toDateString() }}" readonly>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text fs-xl">
                                                            <i class="fal fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="d-none" data-toggle="modal" data-target="#modal-alert"></button>
                                    </form>
                                    <table id="dt-nop" class="table table-bordered table-hover table-striped w-100"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div id="panel-nop-adjustment-index" class="panel">
                            <div class="panel-hdr bg-faded">
                                <h2>NOP Adjustment <span class="fw-300"><i>Table</i></span></h2>
                                <div class="panel-toolbar">
                                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                    <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                </div>
                            </div>
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <table id="dt-advance" class="table table-bordered table-hover table-striped w-100"></table>
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
                        <form action="{{ route('interbank-nop.store') }}" method="post">
                            @csrf
                            
                            <div class="modal-header">
                                <h4 class="modal-title">Add NOP Adjustment</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label" for="currency-id">Currency Code</label>
                                    <select name="base-primary-code" class="form-control" required>
                                        <option value>Choose</option>
@foreach($currency as $value)
                                        <option value="{{ $value->currency_code }}">{{ $value->currency_code }}</option>
@endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="amount">Amount of Deviation</label>
                                    <input type="hidden" name="amount" id="amount-of-deviation" required>
                                    <input type="text" class="form-control" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="note">Note</label>
                                    <textarea name="note" class="form-control" rows="5" required></textarea>
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
            <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action method="post">
                            @csrf
                            
                            @method(strtoupper('patch'))
                            
                            <div class="modal-header">
                                <h4 class="modal-title">Edit NOP Adjustment</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label" for="currency-id-update">Currency Code</label>
                                    <input type="text" name="base-primary-code" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="amount-update">Amount of Deviation</label>
                                    <input type="hidden" name="amount" id="amount-of-deviation-update" required>
                                    <input type="text" class="form-control" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="note-update">Note</label>
                                    <textarea name="note" class="form-control" rows="5" required></textarea>
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
@endsection

@section('javascript')
					<script src="/js/datagrid/datatables/datatables.bundle.js"></script>
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script src="/moment/min/moment.min.js"></script>
					<script src="/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
					
					<script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);

							$.fn.dataTable.ext.errMode = 'throw';

							$.fn.dataTable.ext.type.order['currency-grade-pre'] = function ( data ) {
                                switch ( data ) {
                                    case 'USD': return 1;
                                    case 'SGD': return 2;
                                    case 'JPY': return 3;
                                    case 'HKD': return 4;
                                    case 'EUR': return 5;
                                    case 'AUD': return 6;
                                    case 'CNH': return 7;
                                    case 'GBP': return 8;
                                    case 'MYR': return 9;
                                }

                                return 10;
                            };

                            $('#dt-nop').DataTable({
								responsive: true,
								lengthChange: false,
								paging: false,
								order: [[ 0, 'asc' ]],
								ordering: true,
								bInfo: false,
								searching: false,
								searchable: false,
								select: false,
                                columnDefs: [
                                    {
                                        orderable: true,
                                        targets: 0
                                    },
                                    {
                                        orderable: false,
                                        targets: '_all'
                                    }
                                ],
								dom: "<'row mb-3'" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
									">" +
									"<'row'<'col-sm-12'tr>>" +
									"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										text: '<span class="fal fa-download mr-1"></span>Excel',
										titleAttr: 'Generate Excel',
										className: 'btn btn-outline-primary waves-effect waves-themed',
										action: function() {
											$(document).find('#panel-nop-index').find('[data-toggle="modal"][data-target="#modal-alert"]').click();
										}
									}
								],
								ajax: {
									method: 'GET',
									url: @json(route('api.nop.index')),
									data: function(params) {
										params.api_token = $(document).find('meta[name="api-token"]').attr('content');
										params.date_from = $(document).find('#panel-nop-index').find('input.datepicker[name="date_from"]').val();
										params.date_to = $(document).find('#panel-nop-index').find('input.datepicker[name="date_to"]').val();
									}
								},
								columns: [
									{
										title: 'Currency',
                                        type: 'currency-grade',
										data: 'currency_code',
										className: 'text-center fs-md fw-500'
									},
									{
										title: 'Opening NOP',
										data: 'opening_nop',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return (data).toLocaleString('en-US', {
												minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
														2
													) : (
														data.toString().split('.').slice(1).join().length
													),
												maximumFractionDigits: 2
											});
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(cellData) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									},
									{
										title: 'Opening Rate',
										data: 'opening_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											if (data) {
												data = parseFloat(data).toLocaleString('en-US', {
													minimumFractionDigits: (data.split('.').slice(1).join().length > 2) ? (
															2
														) : (
															data.split('.').slice(1).join().length
														),
													maximumFractionDigits: 2
												});
											}
											
											return data;
										}
									},
									{
										title: 'Average Rate',
										data: 'average_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											if (data) {
												data = (data).toLocaleString('en-US', {
													minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
															2
														) : (
															data.toString().split('.').slice(1).join().length
														),
													maximumFractionDigits: 2
												});
											}
											
											return (data);
										}
									},
									{
										title: 'Revaluation Rate',
										data: 'revaluation_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											if (data) {
												data = parseFloat(data).toLocaleString('en-US', {
													minimumFractionDigits: (data.split('.').slice(1).join().length > 2) ? (
															2
														) : (
															data.split('.').slice(1).join().length
														),
													maximumFractionDigits: 2
												});
											}
											
											return data;
										}
									},
									{
										title: 'Current NOP',
										data: 'current_nop',
										className: 'fs-lg fw-500 text-right',
										render: function(data, type, row, meta) {
											return (data).toLocaleString('en-US', {
												minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
														2
													) : (
														data.toString().split('.').slice(1).join().length
													),
												maximumFractionDigits: 2
											});
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(cellData) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									},
									{
										title: 'On Balance Sheet',
                                        defaultContent: '',
										className: 'text-right',
										render: function(data, type, row, meta) {
                                            data = row.current_nop;
                                            data -= row.off_balance_sheet;

											return (data).toLocaleString('en-US', {
												minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
														2
													) : (
														data.toString().split('.').slice(1).join().length
													),
												maximumFractionDigits: 2
											});
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(td.innerHTML.replace(/,/g, '')) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									},
									{
										title: 'Off Balance Sheet',
                                        data: 'off_balance_sheet',
										className: 'text-right',
										render: function(data, type, row, meta) {
                                            return (data).toLocaleString('en-US', {
												minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
														2
													) : (
														data.toString().split('.').slice(1).join().length
													),
												maximumFractionDigits: 2
											});
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(td.innerHTML.replace(/,/g, '')) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									},
									{
										title: 'USD NOP',
                                        defaultContent: '',
										className: 'text-right',
										render: function(data, type, row, meta) {
											if (row.revaluation_rate && meta.settings.json.data.find(data => data.is_world_currency)) {
												data = (Math.round(row.current_nop * 1000000000) / 1000000000);

												if (!row.is_world_currency) {
													data *= (Math.round(row.revaluation_rate * 1000000000) / 1000000000);
													data /= (
                                                        Math.round(
                                                            meta.settings.json.data.find(data => data.is_world_currency).revaluation_rate * (
                                                                1000000000
                                                            )
                                                        ) / 1000000000
                                                    );
												}

												data = (data).toLocaleString('en-US', {
													minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
															2
														) : (
															data.toString().split('.').slice(1).join().length
														),
													maximumFractionDigits: 2
												});

											} else {
												data = null;
											}

											return data;
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(td.innerHTML.replace(/,/g, '')) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									},
                                    {
										title: 'Absolute NOP',
                                        defaultContent: '',
										className: 'text-right',
										render: function(data, type, row, meta) {
											if (row.revaluation_rate && meta.settings.json.data.find(data => data.is_world_currency)) {
												data = (Math.round(row.current_nop * 1000000000) / 1000000000);

												if (!row.is_world_currency) {
													data *= (Math.round(row.revaluation_rate * 1000000000) / 1000000000);
													data /= (
                                                        Math.round(
                                                            meta.settings.json.data.find(data => data.is_world_currency).revaluation_rate * (
                                                                1000000000
                                                            )
                                                        ) / 1000000000
                                                    );
												}

                                                data = Math.abs(data);

												data = data.toLocaleString('en-US', {
													minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
															2
														) : (
															data.toString().split('.').slice(1).join().length
														),
													maximumFractionDigits: 2
												});

											} else {
												data = null;
											}

											return data;
										}
									},
									{
										title: 'Profit/Loss',
                                        defaultContent: '',
										className: 'fs-lg fw-500 text-right',
										render: function(data, type, row, meta) {
											if (row.revaluation_rate) {
												data = (Math.round(parseFloat(row.revaluation_rate) * 1000000000) / 1000000000);
												data -= (Math.round(row.average_rate * 1000000000) / 1000000000);
												data *= (Math.round(row.current_nop * 1000000000) / 1000000000);
												
												data = (data).toLocaleString('en-US', {
													minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
															2
														) : (
															data.toString().split('.').slice(1).join().length
														),
													maximumFractionDigits: 2
												});
												
											} else {
												data = null;
											}
											
											return data;
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(td.innerHTML.replace(/,/g, '')) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									},
									{
										title: 'Amount of Deviation',
										data: 'current_adjustment',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return (data).toLocaleString('en-US', {
												minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 6) ? (
														6
													) : (
														data.toString().split('.').slice(1).join().length
													),
												maximumFractionDigits: 6
											});
										},
                                        createdCell: function (td, cellData, rowData, row, col) {
                                            if (Math.sign(cellData) < 0) {
                                                td.classList.add('text-warning');
                                            }
                                        }
									}
								],
								language: {
									infoFiltered: ''
								},
								footerCallback: function (row, data, start, end, display) {
									if (this.api().table().node().children.length < 3) {
										this.api().table().node().appendChild(this.api().table().header().cloneNode(true))
										this.api().table().node().children[this.api().table().node().children.length - 1].outerHTML = (
											this.api().table().node().children[this.api().table().node().children.length - 1].outerHTML
												.replace('thead', 'tfoot')
										);

										$(this.api().table().node().children[this.api().table().node().children.length - 1]).find('th')
										.each( function(key, element) {
											if (key > 0) {
												element.innerHTML = '';
											} else {
												element.innerHTML = 'Total';
											}
										});

									} else {
                                        let columns = $(this.api().table().node().children[this.api().table().node().children.length - 1]).find('th');

										columns.eq(start + 8).text(
											this.api().cells('', (start + 8)).render('display').toArray()
                                            .reduce( function (accumulator, currentValue) {
                                                currentValue = currentValue.replace(/,/g, '');

                                                if (!currentValue) {
                                                    currentValue = '0';
                                                }

                                                return (accumulator + parseFloat(currentValue));
                                            }, 0)
											.toLocaleString('en-US', {
												minimumFractionDigits: ((
                                                    this.api().cells('', (start + 8)).render('display').toArray()
                                                    .reduce( function (accumulator, currentValue) {
                                                        currentValue = currentValue.replace(/,/g, '');

                                                        if (!currentValue) {
                                                            currentValue = '0';
                                                        }

                                                        return (accumulator + parseFloat(currentValue));
                                                    }, 0)
												).toString().split('.').slice(1).join().length > 2) ? (
													2
												) : (
													(
                                                        this.api().cells('', (start + 8)).render('display').toArray()
                                                        .reduce( function(accumulator, currentValue) {
                                                            currentValue = currentValue.replace(/,/g, '');

                                                            if (!currentValue) {
                                                                currentValue = '0';
                                                            }

                                                            return (accumulator + parseFloat(currentValue));
                                                        }, 0)
													).toString().split('.').slice(1).join().length
												),
												maximumFractionDigits: 2
											})
										);

                                        columns.eq(start + 9).text(
											this.api().cells('', (start + 9)).render('display').toArray()
                                            .reduce( function (accumulator, currentValue) {
                                                currentValue = currentValue.replace(/,/g, '');

                                                if (!currentValue) {
                                                    currentValue = '0'
                                                }

                                                return (accumulator + parseFloat(currentValue));
                                            }, 0)
											.toLocaleString('en-US', {
												minimumFractionDigits: ((
                                                    this.api().cells('', (start + 9)).render('display').toArray()
                                                    .reduce( function (accumulator, currentValue) {
                                                        currentValue = currentValue.replace(/,/g, '');

                                                        if (!currentValue) {
                                                            currentValue = '0'
                                                        }

                                                        return (accumulator + parseFloat(currentValue));
                                                    }, 0)
												).toString().split('.').slice(1).join().length > 2) ? (
													2
												) : (
													(
                                                        this.api().cells('', (start + 9)).render('display').toArray()
                                                        .reduce( function(accumulator, currentValue) {
                                                            currentValue = currentValue.replace(/,/g, '');

                                                            if (!currentValue) {
                                                                currentValue = '0'
                                                            }

                                                            return (accumulator + parseFloat(currentValue));
                                                        }, 0)
													).toString().split('.').slice(1).join().length
												),
												maximumFractionDigits: 2
											})
										);

                                        columns.eq(start + 10).text(
											this.api().cells('', (start + 10)).render('display').toArray()
                                            .reduce( function (accumulator, currentValue) {
                                                currentValue = currentValue.replace(/,/g, '');

                                                if (!currentValue) {
                                                    currentValue = '0'
                                                }

                                                return (accumulator + parseFloat(currentValue));
                                            }, 0)
											.toLocaleString('en-US', {
												minimumFractionDigits: ((
                                                    this.api().cells('', (start + 10)).render('display').toArray()
                                                    .reduce( function (accumulator, currentValue) {
                                                        currentValue = currentValue.replace(/,/g, '');

                                                        if (!currentValue) {
                                                            currentValue = '0'
                                                        }

                                                        return (accumulator + parseFloat(currentValue));
                                                    }, 0)
												).toString().split('.').slice(1).join().length > 2) ? (
													2
												) : (
													(
                                                        this.api().cells('', (start + 10)).render('display').toArray()
                                                        .reduce( function(accumulator, currentValue) {
                                                            currentValue = currentValue.replace(/,/g, '');

                                                            if (!currentValue) {
                                                                currentValue = '0'
                                                            }

                                                            return (accumulator + parseFloat(currentValue));
                                                        }, 0)
													).toString().split('.').slice(1).join().length
												),
												maximumFractionDigits: 2
											})
										);

                                        if (Math.sign(columns.eq(start + 8).text().replace(/,/g, '')) < 0) {
                                            columns.get(start + 8).classList.add('text-warning');
                                        }

                                        if (Math.sign(columns.eq(start + 10).text().replace(/,/g, '')) < 0) {
                                            columns.get(start + 10).classList.add('text-warning');
                                        }
									}

								},
								initComplete: function(settings, json) {
									settings.oInstance.api().columns().header().to$().addClass('text-center');
									settings.oInstance.api().table().header().classList.add('thead-dark');
									
									window.setInterval( function () {
										settings.oInstance.api().ajax.reload();
									}, 30000 );
									
									settings.oInstance.closest('.panel').find('[name="date_from"], [name="date_to"]').on('change', function(e) {
										settings.oInstance.api().ajax.reload();
									})
								}
							})
							
							dtAdvance = $('#dt-advance').DataTable({
								responsive: true,
								lengthChange: false,
								order: [],
								paging: true,
								pageLength: 50,
								bInfo: false,
								searching: false,
								searchable: false,
								select: true,
								dom: "<'row mb-3'" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
									">" +
									"<'row'<'col-sm-12'tr>>" +
									"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										text: '<span class="fal fa-plus-square mr-1"></span>Create',
										titleAttr: 'Create Adjustment',
										className: 'btn btn-outline-primary waves-effect waves-themed',
										action: function() {
											$('.modal:not(.js-modal-settings):not(.modal-alert)').eq(0).modal();
										}
									}
								],
								serverSide: true,
								processing: true,
								ajax: {
									method: 'GET',
									url: @json(route('api.nop-adjustments.index')),
									data: {
										api_token: $(document).find('meta[name="api-token"]').attr('content')
									}
								},
								columns: [
									{
										title: 'Currency',
										data: 'currency.currency_code',
										className: 'text-center'
									},
									{
										title: 'Amount of Deviation',
										data: 'amount',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: (data.toString().split('.').slice(1).join().length > 2) ? (
														2
													) : (
														data.toString().split('.').slice(1).join().length
													),
												maximumFractionDigits: 2
											});
										}
									},
									{
										title: 'Note',
										data: 'note'
									},
									{
										title: 'Created At',
										data: 'created_at',
										className: 'text-center',
										render: function(data, type, row, meta) {
											return moment(data).format('lll');
										}
									},
									{
										title: 'Updated At',
										data: 'updated_at',
										className: 'text-center',
										render: function(data, type, row, meta) {
											return moment(data).format('lll');
										}
									}
								],
								language: {
									infoFiltered: ''
								},
								createdRow: function(row, data, dataIndex) {
									$(row).addClass('pointer');
								},
								initComplete: function(settings, json) {
									settings.oInstance.api().columns().header().to$().addClass('text-center');
									settings.oInstance.api().table().header().classList.add('thead-dark');
									
									window.setInterval( function () {
										settings.oInstance.api().ajax.reload(null, false);
									}, 15000 );
								}
							})
							
						})
						
						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').eq(1).on('show.bs.modal', function(e) {
                            e.data = dtAdvance.row({
                                selected: true
                            });
                            
                            e.data = e.data.data();
                            
                            $(e.currentTarget).find('form').attr('action', (
                                $('.modal:not(.js-modal-settings):not(.modal-alert)').eq(0).find('form').attr('action').concat('/').concat(e.data.id)
                            ));
                            
                            $(e.currentTarget).find('input[name="base-primary-code"]').val(e.data.currency.currency_code);
                            $(e.currentTarget).find('input[name="amount"]').next().val(e.data.amount);
                            $(e.currentTarget).find('input[name="amount"]').next().trigger('input');
                            $(e.currentTarget).find('textarea[name="note"]').val(e.data.note);
						})
						
						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('hidden.bs.modal', function(e) {
							dtAdvance.rows().deselect();
						})
						
					</script>
@endsection
