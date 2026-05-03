@extends('layouts.master', ['title' => 'Detail Tips & Artikel', 'main' => 'Tips & Artikel'])
@push('css')
<style>
    .card-body img {
        width: 100% !important;
    }
</style>
@endpush
@section('content')
<!--begin::Content-->
<div class="content d-flex pt-6 flex-column flex-column-fluid" id="kt_content">
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl">
        <!--begin::Contacts App- Add New Contact-->
        <div class="row g-7">
            <!--begin::Content-->
            <div class="col-xl-12">
                <!--begin::Contacts-->
                <div class="card card-flush h-lg-100" id="kt_contacts_main">
                    <!--begin::Card header-->
                    <div class="card-header pt-7" id="kt_chat_contacts_header">
                        <!--begin::Card title-->
                        <div class="card-title d-flex align-items-center gap-3">
                                <a href="{{ route('article.index') }}">
                                    <span class="menu-icon back pt-1">
                                        <i class="ki-duotone ki-arrow-left">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </a>
                                <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center">
                                    Detail Tips & Artikel</h1>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-4">
                        <div class="row">
                            <div class="col-sm-3 mb-5">
                                <img src="{{asset($article->thumbnail)}}" class="img img-thumbnail" alt="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h1 class="text-dark">{{$article->title}}</h1>
                                <span class="badge badge-primary mb-5">{{$article->article_category->name}}</span>
                                <p>{!!$article->content!!}</p>
                            </div>
                            <div class="col-sm-6 en-feature">
                                <h1 class="text-dark">{{$article->title_en}}</h1>
                                <span class="badge badge-primary mb-5">{{$article->article_category->name_en}}</span>
                                <p>{!!$article->content_en!!}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>#TAG</h4>
                                @foreach ($article->article_tags ?? [] as $tag)
                                <span class="badge badge-secondary">{{$tag->name}}</span>
                                @endforeach
                            </div>
                            <div class="d-flex gap-3">
                                <span><i class="fa fa-comment fa-lg me-1"></i>{{$article->total_comment}}</span>
                                <span><i class="fa fa-heart fa-lg me-1"></i>{{$article->total_like}}</span>
                                <span><i class="fa fa-share-alt fa-lg me-1"></i>{{$article->total_share}}</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Contacts-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Contacts App- Add New Contact-->
    </div>
    <!--end::Container-->
</div>
<!--end::Content-->
<!--end::Wrapper-->
@endsection
