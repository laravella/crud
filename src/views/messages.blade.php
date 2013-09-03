{{-------------------------------------------------------- messages --------------}}

@section('messages')

@if(Options::get('debug'))
<!-- definitive status message -->
<div class="alert alert-{{$status}}" id="msg-alert" style="margin-top:auto">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{$status}}</strong>
    {{$message}}
    <br />
</div>   
@endif

<!-- detailed error messages -->
<!-- <div class="alert alert-success alert-error alert-block"> -->
<div class="alert alert-log"@if($status == "success" || $status == "info") style="display:none"@endif>
    <button type="button" class="close" onclick="javascript:$('.alert-log').hide();">&times;</button>
    <strong>Log</strong>
    <table>
        @foreach($log as $logentry)
        <tr>
            <td><span class="label label-{{$logentry['severity']}}">{{$logentry['severity']}}</span></td>
            <td>{{$logentry['message']}}</td>
        </tr>
        @endforeach
    </table>
    <br />
</div>

<!-- The params debug box -->
<!-- <div class="alert alert-success alert-error alert-block"> -->

<div class="alert alert-info alert-debug" style="display:none">
    <button type="button" class="close" onclick="javascript:$('.alert-debug').hide();">&times;</button>
    <strong>Params</strong>
    <div id="top"></div>
    {{-- requires bottom.blade.php to load json at the bottom of the page --}}
    <br />
</div>

@stop
