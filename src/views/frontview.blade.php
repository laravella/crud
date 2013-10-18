@include("crud::title")
@include("crud::extra_head")

@include('crud::layouts.admin.navbar')

asdf

{{--
@include("crud::getsearch")
@include("crud::messages")

@include("crud::getdelete")
@include("crud::getedit")
@include("crud::getindex")
@include("crud::getinsert")
@include("crud::getselect")
--}}

@include("crud::bottom")

@section('content')
@yield($action)
@stop

