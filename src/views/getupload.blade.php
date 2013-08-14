{{-------------------------------------------------------- getInsert --------------}}

@section('getUpload') 
@if($action == 'getUpload')
<div class="page-header">
    <h1>Upload</h1>
</div>

<div class="well">
    <div class="btn-group">
        <a href="#" id="btnVisualize" onclick="javascript:debugBox();" class="btn">Debug</a>
        <a href="#" id="btnLog" onclick="javascript:logBox();" class="btn">Log</a>
    </div>
    <div class="btn-group pull-right">
        <a href="/db/select/{{$tableName}}" id="btnVisualize" class="btn"><i class="icon-remove"></i></a>
    </div>
</div>

@yield('messages')

<form method="POST" action="/dbupload/upload">
        <div class="row">
            <div class="span4">upload</div>
        </div>
    <div class="well"><input type="submit" class="btn" /></div>
</form>
@endif
@stop
