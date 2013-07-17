@extends('crud::layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: DbView
@stop

@section('extra_head')
<style>
    table.dbtable td {
        border : 1px solid #303030;
    }
</style>
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
@if(isset($data) && isset($data[0]))        
<table class="dbtable">
    <tr>
        @foreach($data[0] as $name=>$field)
        <th>{{$name}}</th>
        @endforeach
    </tr>
    @foreach($data as $record)
    <tr>
        @foreach($record as $name=>$field)
        @if((isset($prefix) && isset($prefix[$name])) || (isset($meta) && isset($meta[$name]) && $meta[$name]['key'] == 'PRI'))
        <td><a href="{{$prefix[$name]}}{{$field}}">{{$field}}</a></td>
        @else
        <td>{{$field}}</td>
        @endif
        @endforeach
    </tr>
    @endforeach
</table>
@endif
@endif
@stop

@section('edit') 
@if($action == 'edit')
<div class="page-header">
<h1>Edit</h1>
</div>
<form method="POST" action="/db/edit/{{$tableName}}/">
    @foreach($meta as $field)
        @if($field['display']) 
        <div class="row">
            <div class="span4">{{$field['label']}}</div>
            @if(isset($field['pk']))
                <div class="span4">
                    <select>
                        @foreach($selects[$field['name']] as $option)
                        <option value="{{$option['value']}}">{{$option['text']}}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="span4"><input type="text" value="{{$data[0][$field['name']]}}" /></div>
            @endif
        </div>
        @endif
    @endforeach
    <div class="well"><input type="submit" class="btn" /></div>
</form>
@endif
@stop

@section('content')
@yield($action)
@stop

