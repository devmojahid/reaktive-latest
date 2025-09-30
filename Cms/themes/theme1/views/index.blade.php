@extends('theme::layouts.app')
@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection
@section('front-content')
    {{-- hero slider --}}
    @include('theme::components.slider')

    {{-- booking form section --}}
    @include('theme::components.booking-form')

    {{-- about us section --}}
    @include('theme::components.about')

    {{-- package section --}}
    @include('theme::components.package')

    {{-- why choose section --}}
    @include('theme::components.why-choose')

    {{-- banner section --}}
    @include('theme::components.banner')

    {{-- destination section --}}
    @include('theme::components.destination')

    {{-- banner two section --}}
    @include('theme::components.banner-two')

    {{-- testimonial section --}}
    @include('theme::components.testimonial')

    {{-- blog section --}}
    @include('theme::components.blog')

    {{-- cta section --}}
    @include('theme::components.cta')

@endsection
