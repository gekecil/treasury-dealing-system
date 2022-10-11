@extends('layouts.master')

@section('title', 'Search')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
					<main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">Search</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-table'></i> Search
                            </h1>
                        </div>
						<div class="px-3 px-sm-5 pb-4">
                            <div class="tab-content">
                                <div class="tab-pane show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all">
                                    <div class="card">
                                        <ul class="list-group list-group-flush">
@foreach ($results as $value)
                                            <li class="list-group-item py-4 px-4">
                                                <a href="{{ route($value->route, [strtok($value->route, '.') => $value->id]) }}" class="fs-lg fw-500">
													{{
														$value->text }} - {{ ucwords(str_replace('-', ' ', strtok($value->route, '.')))
													}}
												</a>
                                                <div class="fs-xs mt-1">
                                                    <a href="{{ route($value->route, [strtok($value->route, '.') => $value->id]) }}" class="text-primary">
														{{ route($value->route, [strtok($value->route, '.') => $value->id]) }}
													</a>
                                                </div>
                                            </li>
@endforeach
@if (!$results->count())
											<li class="list-group-item py-4 px-4">
                                                <a href="javascript:void(0)" class="fs-lg fw-500">
													0 Results
												</a>
                                            </li>
@endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
					<!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->
@endsection

@section('javascript')
					<script src="/js/datagrid/datatables/datatables.bundle.js"></script>
@endsection
