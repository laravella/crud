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

@section('select')
@if($action == 'select')
<h1>DbView</h1>
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
<h1>Edit</h1>
@foreach($data as $record)
{{$record->id}}<br />
@endforeach
@endif
@stop

@section('content')
@yield($action)
@stop

