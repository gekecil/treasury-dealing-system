@extends('layouts.master')

@section('title', ucfirst(collect(request()->segments())->last()).' - News')

@section('content')
					<main id="js-page-content" role="main" class="page-content">
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
							<li class="breadcrumb-item"><a href="{{ route('news.index') }}">News</a></li>
                            <li class="breadcrumb-item active">{{ ucfirst(collect(request()->segments())->last()) }}</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i> {{ ucfirst(collect(request()->segments())->last()) }} / News
                            </h1>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-news-edit" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('news.update', ['news' => $news->id]) }}" method="post">
												@method(strtoupper('patch'))
												@csrf
												<input type="hidden" name="id" class="form-control" value="{{ $news->id }}" required>
												<div class="form-group">
                                                    <label class="form-label" for="title">Title</label>
                                                    <input type="text" name="title" class="form-control" value="{!! $news->title !!}" required>
                                                </div>
												<div class="form-group">
                                                    <label class="form-label" for="description">Description</label>
                                                    <textarea name="description" class="form-control" rows="5" maxlength="320">{!! $news->description !!}</textarea>
                                                </div>
												<div class="form-group">
                                                    <label class="form-label" for="content">Content</label>
                                                    <textarea name="content" class="form-control" rows="5"></textarea>
                                                </div>
												<button type="button" class="btn btn-lg btn-default" data-toggle="modal" data-target="#modal-alert">
                                                    <span class="fal fa-check mr-1"></span>
                                                    Submit
                                                </button>
											</form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection

@section('javascript')
					<script src="/tinymce/tinymce.min.js"></script>
					<script type="text/javascript">
						$(document).ready( function() {
							tinymce.init({
								selector: 'textarea[name="content"]',
								height: 500,
								plugins: 'image',
								relative_urls: false,
								images_upload_handler: function (blobInfo, success, failure) {
									var xhr, formData;
									xhr = new XMLHttpRequest();
									xhr.withCredentials = false;
									xhr.open('POST', @json(route('api.image-upload')));
									var token = $(document).find('meta[name="csrf-token"]').attr('content');
									xhr.setRequestHeader("X-CSRF-Token", token);
									xhr.onload = function() {
									   var json;
									   if (xhr.status != 200) {
										   failure('HTTP Error: ' + xhr.status);
										   return;
									   }
									   json = JSON.parse(xhr.responseText);

									   if (!json || typeof json.location != 'string') {
										   failure('Invalid JSON: ' + xhr.responseText);
										   return;
									   }
									   success(json.location);
									};
									formData = new FormData();
									formData.append('api_token', $(document).find('meta[name="api-token"]').attr('content'));
									formData.append('file', blobInfo.blob(), blobInfo.filename());
									xhr.send(formData);
								},
								setup: function (editor) {
									editor.on('init', function (e) {
										editor.setContent(@json(str_replace("'", '"', $news->content)));
									});
								}
							});
						})
					</script>
@endsection
