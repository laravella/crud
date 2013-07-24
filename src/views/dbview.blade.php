@extends('crud::layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: DbView
@stop

@section('extra_head')

        <script type="text/javascript" src="/assets/scripts/js/vendor/jsonconvert.js"></script>
    
	<style>
		#page {min-height:600px;}
		.package {margin-left:10px;padding:3px;border-radius:2px;margin-top:2px;}
		.header {cursor:pointer;}

		.name {color:gray;}

		.array {background-color:#FFD8BB;border:thin solid #FFB780;}
		.object {background-color:#E7F1FE;border:thin solid #7DA2CE;}
		.string {color:red;}
		.number {color:blue;}
		.function {color:green;}

		.open .children {display:block;}
		.closed .children {display:none;}
		
		.arrow {background-image:url("/assets/images/d.png"); background-repeat:no-repeat; background-color:transparent; height:15px; width:15px; display:inline-block;}

		.open .arrow {background-position:-20px 0;}
		.closed .arrow {background-position:0 0;}

		.type {color:gray;font-size:8pt;float:right;}

		.hide {display:none;}

		#main {width:100%;height:500px;overflow-y:scroll;}
	</style>


<style>
    table.dbtable {
        margin : 0px;
        border : 0px;
        width : auto;
    }
    
    table.dbtable td {
        border : 1px solid #dddddd;
    }
    
    table.dbtable td input[type="text"] {
        margin-bottom: 0px;
    }
    
    div.table_container {
        width:100%; 
        overflow-x: auto;
        padding : 0px;
    }
    td.td_toolbox {
        text-align:center; 
        width: 51px; 
        max-width:51px;
    }
</style>

<script type="text/javascript">
    
    function alertBox(message) {
        $(".alert").alert();
    }
    
    function debugBox() {
    
        //var source = $('#json_source').text();
        
        //var transform = "";
 
        //$('#json_html').json2html(source, transform);
    
        $('.alert-debug').show();
        $('.alert-debug').css('opacity', '1');
    }
    
    function sendSearch() {
        var qString = "";
        var qA = new Object();
        var comma = '';
        var table = '';
        var field = '';
        //turn the search form into JSON
        $('.formfield').each(function( index ) {
            table = $(this).attr('data-table');
            if (qA[table] == null || qA[table] == 'undefined' || !qA[table]) {
                qA[table] = new Object();
            }
            if ($(this).val().length > 0) {
                field = $(this).attr('name');
                qString += comma+'"'+$(this).attr('name')+'" : "'+$(this).val()+'"';
                qA[table][field] = $(this).val();
                comma = ',';
            }
        });
        qString = JSON.stringify(qA);
        window.location.href = "/db/search/"+table+"?q="+qString;
    }
    
    function sendDelete() {
        alertBox("Howzit");
        $('a.record.active').each(function( index ) {
            console.log($(this).attr('id'));
        });
    }
    
    function checkRec(recNo) {
        $('#chkico_'+recNo).toggleClass('icon-ok-sign');
        $('#chkico_'+recNo).toggleClass('icon-ok-circle');
    }
</script>

@stop

@section('getIndex')
@if($action == 'getIndex')
<h1>Index</h1>
<ul>
    <li><a href="/db/select/_db_tables">List Tables</a></li>
</ul>
@endif
@stop

@section('getSelect')
@if($action == 'getSelect' || @action == 'getSearch')
<div class="page-header">
    <h1>{{$title}}</h1>
</div>
<div class="well">
    <div class="btn-group">
        <a href="/db/insert/{{$tableName}}" class="btn">New</a>
        <a href="javascript:sendDelete()" class="btn">Delete</a>
        <a href="#myModal" role="button" class="btn" data-toggle="modal">Search</b></a>
    </div>
    <div class="btn-group">
        <a href="#" id="btnVisualize" onclick="javascript:debugBox();" class="btn">Debug</a>
    </div>
</div>

<!-- <div class="alert alert-success alert-error alert-block"> -->
<div class="alert" style="display:none">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Warning!</strong> Message.
</div>

<!-- 
The params debug box 

<div class="alert alert-success alert-error alert-block"> -->
<div class="alert alert-info alert-debug" style="display:none">
  <button type="button" class="close" onclick="javascript:$('.alert-debug').hide();">&times;</button>
  <strong>Params</strong>
  <div id="top"></div>
  <textarea style="display:none" id="inputJSON"><? print_r($params); ?></textarea>
</div>

{{-- the search popup box --}}
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <h3 id="myModalLabel">Search</h3>
    </div>
    <div class="modal-body">
        <div class="row">
            @foreach($meta as $field)
                @if($field['searchable'] == 1)
                <div class="span2">{{$field['label']}}</div>
                <div class="span3"><input style="width:{{$field['width']}}px" 
                                          class="formfield" type="text" data-table="{{$tableName}}" 
                                          name="{{$field['name']}}" /></div>
                @endif
            @endforeach
        </div>
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      <button class="btn btn-primary" onclick="javascript:sendSearch()">Search</button>
    </div>        
</div>

@if(isset($data) && isset($data[0]))

<div class="table_container">
    <table class="table table-striped dbtable">
        {{-- the field titles --}}
        <tr>
            <th></th>
            @foreach($data[0] as $name=>$field)
                @if ($meta[$name]['display'])
                    <th>{{$meta[$name]['label']}}</th>
                    @if (isset($meta[$name]['pk']))
                    {{-- this is a foreign key, it contains a reference to a primary key --}}
                    <th>{{$meta[$name]['pk']['label']}}</th>
                    @endif
                @endif
            @endforeach
        </tr>
        <?php $i = 0;?>
        @foreach($data as $record)
            <tr id="rec_{{$record->id}}">
                <td class="td_toolbox">
                    <a data-toggle="button" data-recordid="{{$record->id}}" class="record btn" href="#" id="chkbtn_{{$record->id}}" onclick="javascript:checkRec({{$record->id}})">
                        <b id="chkico_{{$record->id}}" class="icon-ok-circle"></b>
                    </a>
                </td>
                @foreach($record as $name=>$value)
                    @if ($meta[$name]['display'])
                        @if((isset($prefix) && isset($prefix[$name])) || (isset($meta) && isset($meta[$name]) && $meta[$name]['key'] == 'PRI'))
                            <td><a href="{{$prefix[$name]}}{{$value}}">{{$value}}</a></td>
                        @else
                            {{-- hover-edit : see : https://github.com/mruoss/HoverEdit-jQuery-Plugin --}}

                            <td><input style="width:{{$meta[$name]['width']}}px" type="text" value="{{$value}}" id="" class="hover-edit" /></div></td>
                            @if(isset($meta[$name]['pk']))
                            {{-- this is a foreign key, it contains a reference to a primary key --}}
                                <td><a href="/db/edit/{{$meta[$name]['pk']['tableName']}}/{{$value}}">{{$pkTables[$meta[$name]['pk']['tableName']][$value]}}</a></td> 
                            @endif

                        @endif
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

@section('getEdit') 
@if($action == 'getEdit')
<div class="page-header">
    <h1>Edit <span class="h1a">[{{$tableName}}::{{$data[$pkName]}}]</span></h1>
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

@section('getInsert') 
@if($action == 'getInsert')
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

