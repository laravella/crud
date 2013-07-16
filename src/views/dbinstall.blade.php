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
    @if($action == 'install')
        <h1>Install</h1>
        @foreach($log as $logitem)
            {{$logitem}}<br />
        @endforeach
    @endif
@stop

@section('index')
    @if($action == 'index')
        <h1>Index</h1>
        <ul>
        <li><a href="/db/install">Install</a></li>
        <li><a href="/db/reinstall">Reinstall</a></li>
        <li><a href="/db/select/_db_tables">List Tables</a></li>
        </ul>
    @endif
@stop

@section('content')
    @yield($action)
@stop

