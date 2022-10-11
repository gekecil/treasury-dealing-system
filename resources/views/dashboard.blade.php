@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
						<div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-chart-pie'></i> Statistics
                            </h1>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="panel-pie" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Pie <span class="fw-300"><i>Chart</i></span>
                                                </h2>
												<div class="panel-toolbar">
													<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
													<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
													<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
												</div>
                                            </div>
											
                                            <div class="panel-container show">
                                                <div class="panel-content">
                                                    <div id="pieChart">
                                                        <canvas style="width:100%; height:300px;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<div class="col-md-6">
                                        <div id="panel-line" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Line <span class="fw-300"><i>Chart</i></span>
                                                </h2>
												<div class="panel-toolbar">
													<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
													<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
													<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
												</div>
                                            </div>
                                            <div class="panel-container show">
                                                <div class="panel-content">
                                                    <div id="lineChart">
                                                        <canvas style="width:100%; height:300px;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<div class="col-xl-12">
                                        <div id="panel-currency" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Currency <span class="fw-300"><i>Chart</i></span>
                                                </h2>
												<div class="panel-toolbar">
													<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
													<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
													<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
												</div>
                                            </div>
                                            <div class="panel-container show">
                                                <div class="panel-content">
                                                    <div id="currencyChart">
                                                        <canvas style="width:100%; height:300px;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@if ($news->count() > 0)
						<div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-newspaper'></i> Articles
                            </h1>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <div id="news-index" class="row">
@foreach ($news as $value)
									<div class="col-md-6">
										<div class="card mb-g">
											<div class="card-body">
												<a href="{{ route('news.show', ['news' => $value->id]) }}" class="d-flex flex-row align-items-center">
													<h2 class="mb-0 flex-1 text-dark fw-500">
														{!! $value->title !!}
														<small class="m-0 text-muted fs-xs opacity-70">
															{!! $value->updated_at->toDayDateTimeString() !!}
														</small>
													</h2>
												</a>
											</div>
											<div class="card-body border-faded border-right-0 border-bottom-0 border-left-0 text-muted">
												{!! $value->description !!}
											</div>
										</div>
									</div>
@endforeach
								</div>
                                <div class="row justify-content-center">
                                    <div class="col-auto">
										<button type="button" id="load-more" class="btn btn-lg btn-primary">Load More</button>
                                        <button type="button" id="loading" class="btn btn-primary" style="display: none" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button>
									</div>
								</div>
							</div>
						</div>
@endif
					</main>
@endsection

@section('javascript')
					<script src="/js/statistics/peity/peity.bundle.js"></script>
					<script src="/js/statistics/flot/flot.bundle.js"></script>
					<script src="/js/statistics/easypiechart/easypiechart.bundle.js"></script>
					<script src="/js/statistics/chartjs/chartjs.bundle.js"></script>
					<script src="/moment/min/moment.min.js"></script>
					<script>
						
						var red = '#FC1349';
						var green = '#69FB13';
						
						/* pie chart */
						var pieChart = function()
						{
							var config = {
								type: 'pie',
								data:
								{
									datasets: [
									{
										data: @json(
                                            $salesDeal->groupBy('buy_sell')->sortKeys()->values()
                                            ->map( function($item) {
                                                return $item->sum('count');
                                            })
                                            ->toArray()
                                        ),
										backgroundColor: [
											red,
											green
										]
									}],
									labels: [
										'Bank Buy',
										'Bank Sell'
									]
								},
								options:
								{
									responsive: true,
									legend:
									{
										display: true,
										position: 'bottom',
									}
								}
							};
							new Chart($("#pieChart > canvas").get(0).getContext("2d"), config);
						}
						/* pie chart -- end */
						
						/* line chart */
						var lineChart = function()
						{
							var config = {
								type: 'line',
								data:
								{
									labels: @json(
										$salesDeal->groupBy('month')->sortKeys()->map( function($item, $key) {
											return \Carbon\Carbon::createFromFormat('m', $key)->shortEnglishMonth;
										})
                                        ->values()
										->toArray()
									),
									datasets: [
									{
										label: 'Bank Buy',
										borderColor: red,
										pointBackgroundColor: red,
										pointBorderColor: 'rgba(0, 0, 0, 0)',
										pointBorderWidth: 1,
										borderWidth: 1,
										pointRadius: 3,
										pointHoverRadius: 4,
										data: @json(
											$salesDeal->groupBy('buy_sell')->sortKeys()->whenEmpty( function($salesDeal) {
                                                return $salesDeal->push(
                                                    collect([(object) ['month' => \Carbon\Carbon::today()->month, 'count' => 0]])
                                                );
                                            })
                                            ->first()
                                            ->groupBy('month')->sortKeys()->map( function($item) {
                                                return $item->sum('count');
											})
                                            ->values()
											->toArray()
										),
										fill: false
									},
									{
										label: 'Bank Sell',
										borderColor: green,
										pointBackgroundColor: green,
										pointBorderColor: 'rgba(0, 0, 0, 0)',
										pointBorderWidth: 1,
										borderWidth: 1,
										pointRadius: 3,
										pointHoverRadius: 4,
										data: @json(
											$salesDeal->groupBy('buy_sell')->sortKeys()->whenEmpty( function($salesDeal) {
                                                return $salesDeal->push(
                                                    collect([(object) ['month' => \Carbon\Carbon::today()->month, 'count' => 0]])
                                                );
                                            })
                                            ->last()
                                            ->groupBy('month')->sortKeys()->map( function($item) {
                                                return $item->sum('count');
											})
                                            ->values()
											->toArray()
										),
										fill: false
									}]
								},
								options:
								{
									responsive: true,
									title:
									{
										display: false,
										text: 'Line Chart'
									},
									tooltips:
									{
										mode: 'index',
										intersect: false,
									},
									hover:
									{
										mode: 'nearest',
										intersect: true
									},
									scales:
									{
										xAxes: [
										{
											display: true,
											scaleLabel:
											{
												display: false
											},
											gridLines:
											{
												display: true,
												color: "#f2f2f2"
											},
											ticks:
											{
												beginAtZero: true,
												fontSize: 11
											}
										}],
										yAxes: [
										{
											display: true,
											scaleLabel:
											{
												display: false
											},
											gridLines:
											{
												display: true,
												color: "#f2f2f2"
											},
											ticks:
											{
												beginAtZero: true,
												fontSize: 11
											}
										}]
									}
								}
							};
							new Chart($("#lineChart > canvas").get(0).getContext("2d"), config);
						}
						/* line chart -- end */
						
						/* currency chart */
						var currencyChart = function()
						{
							var barStackedData = {
								labels: @json(
                                    $salesDeal->groupBy('currencyPair.base_currency_id')->sortKeys()->map( function($item) {
                                        return $item->first()->currencyPair->baseCurrency()->withTrashed()->first()->primary_code;
                                    })
                                    ->values()
                                    ->toArray()
                                ),
								datasets: [
								{
									label: 'Buy',
									backgroundColor: red,
									borderColor: 'rgba(0, 0, 0, 0)',
									borderWidth: 1,
									data: @json(
                                        $salesDeal->groupBy('buy_sell')->sortKeys()->whenEmpty( function($salesDeal) {
                                            return $salesDeal->push(
                                                collect([(object) ['month' => \Carbon\Carbon::today()->month, 'count' => 0]])
                                            );
                                        })
                                        ->first()
                                        ->groupBy('currencyPair.base_currency_id')->sortKeys()->map( function($item) {
                                            return $item->sum('count');
                                        })
                                        ->values()
                                        ->toArray()
                                    )
								},
								{
									label: 'Sell',
									backgroundColor: green,
									borderColor: 'rgba(0, 0, 0, 0)',
									borderWidth: 1,
									data: @json(
                                        $salesDeal->groupBy('buy_sell')->sortKeys()->whenEmpty( function($salesDeal) {
                                            return $salesDeal->push(
                                                collect([(object) ['month' => \Carbon\Carbon::today()->month, 'count' => 0]])
                                            );
                                        })
                                        ->last()
                                        ->groupBy('currencyPair.base_currency_id')->sortKeys()->map( function($item) {
                                            return $item->sum('count');
                                        })
                                        ->values()
                                        ->toArray()
                                    )
								}]

							};
							
							barStackedData.labels = barStackedData.labels.filter(label =>
								barStackedData.datasets[0].data[barStackedData.labels.indexOf(label)] &&
								barStackedData.datasets[1].data[barStackedData.labels.indexOf(label)]
							);
							
							$.each(barStackedData.datasets, function(key, value) {
								value.data = value.data.filter(data =>
									data > 0
								);
							})
							
							var config = {
								type: 'bar',
								data: barStackedData,
								options:
								{
									legend:
									{
										position: 'top'
									},
									scales:
									{
										yAxes: [
										{
											stacked: true,
											gridLines:
											{
												display: true,
												color: "#f2f2f2"
											},
											ticks:
											{
												beginAtZero: true,
												fontSize: 11
											}
										}],
										xAxes: [
										{
											stacked: true,
											gridLines:
											{
												display: true,
												color: "#f2f2f2"
											},
											ticks:
											{
												beginAtZero: true,
												fontSize: 11
											}
										}]
									}
								}
							}
							new Chart($("#currencyChart > canvas").get(0).getContext("2d"), config);
						}
						/* currency chart -- end */
			
						/* initialize all charts */
						$(document).ready(function()
						{
							pieChart();
							lineChart();
							currencyChart();
						});

					</script>
					
					<script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"][title="Dashboard"]').parent().attr('class', 'active');
							initApp.buildNavigation(myapp_config.navHooks);
						})

                        $(document).find('#load-more').on('click', function(e) {
                            $(e.currentTarget).hide();
                            $('#loading').show();

                            $.ajax({
                                url: @json(route('api.news.index')),
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    api_token: $(document).find('meta[name="api-token"]').attr('content'),
                                    start: $('#news-index').children().length,
                                    length: 50
                                }
                            }).done( function(response) {
                                $('#loading').hide();
                                $(e.currentTarget).show();

                                $.each(response.data, function(key, value) {
                                    let col = $('#news-index').children().first().clone();
                                    col.find('a').attr('href', String(@json(route('news.show', ''))).concat('/').concat(value.id));
                                    col.find('h2').contents().get(0).replaceWith(value.title);
                                    col.find('h2').contents().get(1).replaceChildren(moment(value.updated_at).format('llll'));
                                    col.find('.card-body.text-muted').text(value.description);

                                    $('#news-index').append(col);
                                })

                            });

                        })

					</script>
@endsection
