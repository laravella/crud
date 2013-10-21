@include("crud::skins.common.inc.assets")

@include("crud::skins.common.inc.title")
@include("crud::skins.common.inc.extra_head")

@include('crud::skins.common.inc.navbar')

{{--
@include("crud::skins.common.actions.getsearch")
@include("crud::skins.common.inc.messages")

@include("crud::skins.common.actions.getdelete")
@include("crud::skins.common.actions.getedit")
@include("crud::skins.common.actions.getindex")
@include("crud::skins.common.actions.getinsert")
@include("crud::skins.common.actions.getselect")
--}}

@include("crud::skins.".$skin['admin'].".inc.bottom")

@include("crud::skins.".$skin['admin'].".inc.footer")

@section('skins.".$skin['admin'].".content')
@yield($action)
@stop

