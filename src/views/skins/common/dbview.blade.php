@include("crud::skins.".$skin['admin'].".inc.assets")

@include("crud::skins.".$skin['admin'].".inc.title")
@include("crud::skins.".$skin['admin'].".inc.extra_head")

@include("crud::skins.".$skin['admin'].".inc.navbar")

@include("crud::skins.".$skin['admin'].".actions.getsearch")
@include("crud::skins.".$skin['admin'].".inc.messages")

@include("crud::skins.".$skin['admin'].".actions.getdelete")
@include("crud::skins.".$skin['admin'].".actions.getedit")
@include("crud::skins.".$skin['admin'].".actions.getindex")
@include("crud::skins.".$skin['admin'].".actions.getinsert")
@include("crud::skins.".$skin['admin'].".actions.getselect")

@include("crud::skins.".$skin['admin'].".inc.bottom")
@include("crud::skins.".$skin['admin'].".inc.footer")

@section('content')
@yield($action)
@stop

