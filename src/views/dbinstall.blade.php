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

@section('install')
    <h1>Install</h1>
    @foreach($log as $logitem)
        {{$logitem}}<br />
    @endforeach
@stop

@section('index')
    <h1>Index</h1>
@stop

@section('content')
    @yield($action)
@stop

