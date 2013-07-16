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
        <div class="page-header">
        <h1>Install</h1>
        </div>
        <table>
        @foreach($log as $logitem)
            <tr><td><span class="label label-info">Info</span></td><td>{{$logitem}}</td></tr>
        @endforeach
        </table>
    @endif
@stop

@section('index')
    @if($action == 'index')
        <div class="page-header">
        <h1>Index</h1>
        </div>
        <ul>
        <li><a href="/dbinstall/install">Install</a></li>
        <li><a href="/dbinstall/reinstall">Reinstall</a></li>
        <li><a href="/db/select/_db_tables">List Tables</a></li>
        </ul>
    @endif
@stop

@section('content')
    @yield($action)
@stop

