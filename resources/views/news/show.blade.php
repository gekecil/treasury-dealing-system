@extends('layouts.master')

@section('title', $news->title.' - News')

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content news-read">
                        <ol class="breadcrumb page-breadcrumb d-flex align-items-center">
                            <li class="breadcrumb-item">
								<ul class="pagination">
									<li class="page-item">
										<a class="page-link" href="#" aria-label="Previous">
											<span aria-hidden="true"><i class="fal fa-chevron-left"></i></span>
										</a>
									</li>
								</ul>
							</li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
							<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">News</a></li>
                            <li class="breadcrumb-item active">Read</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
						<div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-newspaper'></i> News
                            </h1>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
								<div id="panel-news-show" class="panel">
									<div class="panel-hdr">
										<h2>
											<span class="fw-300">{{ (new \DateTime($news->updated_at))->format('d-m-Y H:i') }}</span>
										</h2>
										<div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
											<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
											<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
									</div>
									<div class="panel-container show">
										<div class="panel-content">
											<div class="subheader">
												<h2 class="subheader-title">{!! $news->title !!}</h2>
											</div>
											{!! $news->content !!}
										</div>
									</div>
								</div>
							</div>
						</div>
					</main>
@endsection

@section('javascript')
					<script type="text/javascript">
						$(document).ready( function() {
							$('a[href="{!! url()->current() !!}"][title="Statistics"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"][title="Statistics"]').parent().parent().parent().attr('class', 'active open');
							
							$('.news-read img').attr('class', 'img-fluid mx-auto d-block');
						})
					</script>
@endsection
