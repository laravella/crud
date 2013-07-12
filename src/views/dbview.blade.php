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

@section('content')
<h1>DbView</h1>
    <table class="dbtable">
    @foreach($data as $record)
        <tr>
        @foreach($record as $field)
            <td>{{$field}}</td>
        @endforeach
        </tr>
    @endforeach
    </table>
@stop
