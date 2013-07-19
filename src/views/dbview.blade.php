@extends('crud::layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: DbView
@stop

@section('extra_head')
<style>
    table.dbtable td {
        border : 1px solid #dddddd;
    }
</style>

<script type="text/javascript">
    function sendSearch() {
        var qString = "";
        var qA = new Object();
        var comma = '';
        var table = '';
        var field = '';
        $('.formfield').each(function( index ) {
            table = $(this).attr('data-table');
            if (qA[table] == null || qA[table] == 'undefined' || !qA[table]) {
                qA[table] = new Object();
            }
            if ($(this).val().length > 0) {
                field = $(this).attr('name');
                qString += comma+'"'+$(this).attr('name')+'" : "'+$(this).val()+'"';
                qA[table][field] = $(this).val();
                alert(qA[table][field]);
                comma = ',';
            }
        });
        qString = JSON.stringify(qA);
        window.location.href = "/db/search/"+table+"?q="+qString;
    }
</script>

@stop

@section('index')
@if($action == 'index')
<h1>Index</h1>
<ul>
    <li><a href="/db/select/_db_tables">List Tables</a></li>
</ul>
@endif
@stop

@section('select')
@if($action == 'select')
<div class="page-header">
    <h1>DbView</h1>
</div>
<div class="well">
    <div class="btn-group">
        <a href="/db/insert/{{$tableName}}" class="btn">New</a>
        <a href="#myModal" role="button" class="btn" data-toggle="modal">Search <b class="caret"></b></a>
    </div>
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <h3 id="myModalLabel">Search</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                @foreach($meta as $field)
                    <div class="span2">{{$field['label']}}</div>
                    <div class="span3"><input style="width:{{$field['width']}}px" class="formfield" type="text" data-table="{{$tableName}}" 
                                              name="{{$field['name']}}" /></div>
                @endforeach
            </div>
        </div>
        <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button class="btn btn-primary" onclick="javascript:sendSearch()">Search</button>
        </div>        
    </div>
</div>
@if(isset($data) && isset($data[0]))
<div style="width:100%; overflow-x: scroll">
    <table class="table table-striped dbtable">
        <tr>
            @foreach($data[0] as $name=>$field)
            <th>{{$meta[$name]['label']}}</th>
            @endforeach
        </tr>
        @foreach($data as $record)
        <tr>
            @foreach($record as $name=>$field)
            @if((isset($prefix) && isset($prefix[$name])) || (isset($meta) && isset($meta[$name]) && $meta[$name]['key'] == 'PRI'))
            <td><a href="{{$prefix[$name]}}{{$field}}">{{$field}}</a></td>
            @else
            {{-- see : https://github.com/mruoss/HoverEdit-jQuery-Plugin --}}
            <td><input style="width:{{$meta[$name]['width']}}px" type="text" value="{{$field}}" id="" class="hover-edit" /></div></td>
            @endif
            @endforeach
        </tr>
        @endforeach
    </table>
</div>
{{$data->links()}}
@endif
@endif
@stop

@section('edit') 
@if($action == 'edit')
<div class="page-header">
    <h1>Edit</h1>
</div>
<form method="POST" action="/db/edit/{{$tableName}}/{{$data[$pkName]}}">
    @foreach($meta as $field)
    @if($field['display'] == 1) 
    <div class="row">
        <div class="span4">{{$field['label']}}</div>
        @if(isset($field['key']) && $field['key'] == 'PRI')
        <div class="span4"><input type="text" disabled name="{{$field['name']}}" value="{{$data[$field['name']]}}" /></div>
        @elseif(isset($field['pk']))
        <div class="span4">
            <select name="{{$field['name']}}">
                @foreach($selects[$field['name']] as $option)
                @if($option['value'] == $data[$field['name']])
                <option selected value="{{$option['value']}}">{{$option['text']}}</option>
                @else
                <option value="{{$option['value']}}">{{$option['text']}}</option>
                @endif
                @endforeach
            </select>
        </div>
        @else
        <div class="span4"><input type="text" style="width:{{$field['width']}}px" name="{{$field['name']}}" value="{{$data[$field['name']]}}" /></div>
        @endif
    </div>
    @endif
    @endforeach<br />
    <div class="well"><input type="submit" class="btn" /></div>
</form>
@endif
@stop

@section('insert') 
@if($action == 'insert')
<div class="page-header">
    <h1>New</h1>
</div>
<form method="POST" action="/db/insert/{{$tableName}}">
    @foreach($meta as $field)
    @if($field['display'] == 1) 
    <div class="row">
        <div class="span4">{{$field['label']}}</div>
        @if(isset($field['key']) && $field['key'] == 'PRI')
        <div class="span4"><input type="text" disabled name="{{$field['name']}}" value="" /></div>
        @elseif(isset($field['pk']))
        <div class="span4">
            <select name="{{$field['name']}}">
                @foreach($selects[$field['name']] as $option)
                @if($option['value'] == $field['default'])
                <option selected value="{{$option['value']}}">{{$option['text']}}</option>
                @else
                <option value="{{$option['value']}}">{{$option['text']}}</option>
                @endif
                @endforeach
            </select>
        </div>
        @else
        <div class="span4"><input type="text" name="{{$field['name']}}" value="{{$field['default']}}" /></div>
        @endif
    </div>
    @endif
    @endforeach<br />
    <div class="well"><input type="submit" class="btn" /></div>
</form>
@endif
@stop

@section('content')
@yield($action)
@stop

