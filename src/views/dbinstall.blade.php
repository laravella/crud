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
<h1>Install</h1>
<a href="/db/install">Install</a>
@stop

@section('install')
<h1>Installed</h1>
@stop

@section('content')
    @if($action == 'index')
        @yield('index')
    @elseif($action == 'install')
        @yield('install')
    @endif
@stop

