{{-------------------------------------------------------- getSelect --------------}}

@section('getSelect')
@if($action == 'getSelect' || @action == 'getSearch')

@if($displayType == "text/html")

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
        @if(Options::get('debug'))
        <a href="#" id="btnVisualize" onclick="javascript:debugBox();" class="btn">Debug</a>
        @endif
        <a href="#" id="btnLog" onclick="javascript:logBox();" class="btn">Log</a>
    </div>
    <div class="btn-group pull-right">
        <a href="/db/select/{{$tableName}}" id="btnVisualize" class="btn"><i class="icon-remove"></i></a>
    </div>
</div>

@yield('messages')

@yield('search')

@endif


@if(isset($data) && isset($data[0]))

<div class="table_container">
    <table class="table table-striped dbtable">
        
        {{-- the field titles --}}
        
        <tr>
            <th></th>
            @foreach($data[0] as $name=>$field)
            @if ($displayTypes[$meta[$name]['display_type_id']] == 'nodisplay')
            
            @elseif ($displayTypes[$meta[$name]['display_type_id']] == 'widget' && $widgetTypes[$meta[$name]['widget_type_id']] == 'ckeditor')

            @elseif ($displayTypes[$meta[$name]['display_type_id']] == 'widget' && $widgetTypes[$meta[$name]['widget_type_id']] == 'textarea')
            
            @else
            <th>{{$meta[$name]['label']}}</th>
            @if (isset($meta[$name]['pk']))
            {{-- this is a foreign key, it contains a reference to a primary key --}}
            <th>{{$meta[$name]['pk']['label']}}</th>
            @endif
            @endif
            @endforeach
        </tr>
        
        {{-- the records --}}
        
        @foreach($data as $record)
        <tr id="tr-{{$tableName}}-{{$record->id}}">
            <td class="td_toolbox">
                <div class="btn-group">
                    <a data-toggle="button" data-tablename="{{$tableName}}" data-recordid="{{$record->id}}" class="record btn" href="#" id="chkbtn_{{$tableName}}_{{$record->id}}" onclick="javascript:checkRec('{{$tableName}}', {{$record->id}})">
                        <b id="chkico_{{$record->id}}" class="icon-ok-circle"></b>
                    </a>
                    @foreach($record as $name=>$value)
                        @if((isset($prefix) && isset($prefix[$name])) || (isset($meta) && isset($meta[$name]) && $meta[$name]['key'] == 'PRI'))
                            <a data-recordid="{{$record->id}}" class="edit btn" href="{{$prefix[$name]}}{{$value}}" id="editbtn_{{$tableName}}_{{$record->id}}">
                                <b id="editico_{{$record->id}}" class="icon-edit"></b>
                            </a>
                        @endif
                    @endforeach
                    <!--
                    <a data-recordid="{{$record->id}}" class="save btn" href="#" id="savebtn_{{$tableName}}_{{$record->id}}" onclick="javascript:saveRec('{{$tableName}}', {{$record->id}})">
                        <b id="saveico_{{$record->id}}" class="icon-save"></b>
                    </a> -->
                </div>
            </td>
            @foreach($record as $name=>$value)
                @if ($displayTypes[$meta[$name]['display_type_id']] == 'nodisplay')
                @elseif ($displayTypes[$meta[$name]['display_type_id']] == 'thumbnail' 
                || ($displayTypes[$meta[$name]['display_type_id']] == 'widget' 
                && $widgetTypes[$meta[$name]['widget_type_id']] == 'thumbnail'))
                    <td>
                        @if(file_exists(public_path().'/uploads/thumbnail/'.$record->file_name))
                            <a href="{{$prefix[$name]}}{{$value}}"><img src="/uploads/thumbnail/{{$record->file_name}}" class="img-rounded" style="width:80px; height:80px; max-width:80px" /></a>
                        @else
                            <i class="icon-file"> </i>
                        @endif
                            <input data-tablename="{{$tableName}}" data-recordid="{{$record->id}}" data-fieldname="{{$name}}" type="hidden" value="{{$value}}" id="{{$tableName}}-{{$record->id}}-{{$name}}" class="hover-edit fld-{{$tableName}}-{{$record->id}}" />
                    </td>
                @elseif ($displayTypes[$meta[$name]['display_type_id']] == 'widget' && $widgetTypes[$meta[$name]['widget_type_id']] == 'ckeditor')
                
                @elseif ($displayTypes[$meta[$name]['display_type_id']] == 'widget' && $widgetTypes[$meta[$name]['widget_type_id']] == 'textarea')
                @else
                    @if((isset($prefix) && isset($prefix[$name])) || (isset($meta) && isset($meta[$name]) && $meta[$name]['key'] == 'PRI'))
                        <td>
                            <a href="{{$prefix[$name]}}{{$value}}">{{$value}}</a>
                            <input data-tablename="{{$tableName}}" data-recordid="{{$record->id}}" data-fieldname="{{$name}}" type="hidden" value="{{$value}}" id="{{$tableName}}-{{$record->id}}-{{$name}}" class="hover-edit fld-{{$tableName}}-{{$record->id}}" />
                        </td>
                    @else
                        {{-- hover-edit : see : https://github.com/mruoss/HoverEdit-jQuery-Plugin --}}

                        <td>
                            <input data-tablename="{{$tableName}}" data-recordid="{{$record->id}}" data-fieldname="{{$name}}" style="width:{{$meta[$name]['width']}}px" type="text" disabled value="{{$value}}" id="{{$tableName}}-{{$record->id}}-{{$name}}" class="hover-edit fld-{{$tableName}}-{{$record->id}}" />
                        </td>
                        @if(isset($meta[$name]['pk']))
                        {{-- this is a foreign key, it contains a reference to a primary key --}}
                        <td>
                            <a href="/db/edit/{{$meta[$name]['pk']['tableName']}}/{{$value}}">{{$pkTables[$meta[$name]['pk']['tableName']][$value]}}</a>
                            <input data-tablename="{{$tableName}}" data-recordid="{{$record->id}}" data-fieldname="{{$name}}" type="hidden" value="{{$value}}" id="{{$tableName}}-{{$record->id}}-{{$name}}" class="hover-edit fld-{{$tableName}}-{{$record->id}}" />
                        </td> 
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