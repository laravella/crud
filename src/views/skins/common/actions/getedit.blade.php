{{-------------------------------------------------------- getEdit --------------}}
@section('getEdit') 
@if($action == 'getEdit')

@if($displayType == "text/html")

<div class="page-header">
    <h1>Edit <span class="h1a">[{{$tableName}}::{{$pkName}}]</span></h1>
</div>
<div class="well">
    <div class="btn-group">
        <a href="/db/select/{{$tableName}}" id="btnVisualize" class="btn">Back</a>
        <a href="#" onclick="javascript:$('#dataForm').submit();" id="btnSubmit" class="btn">Submit</a>
        <a href="/db/delete/{{$tableName}}/{{$tables[$tableName]['records'][0][$pkName]}}" id="btnDelete" class="btn">Delete</a>
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

@endif

<?php
    $records = array();
    $records = $dataA;
?>

@foreach ($records as $recNo=>$record) 
<form method="POST" id="dataForm" action="/db/edit/{{$tableName}}/{{$record[$pkName]}}">
    @foreach($meta as $field)
    
        @if ($displayTypes[$field['display_type_id']] != 'nodisplay')
        <div class="row">
            <div class="span4">{{$field['label']}}</div>
            
            @if(isset($field['key']) && $field['key'] == 'PRI')
                <div class="span4"><input type="text" disabled name="{{$field['name']}}" value="{{$record[$field['name']]}}" /></div>
            @elseif(isset($field['pk']))
                <div class="span4">
                    <select name="{{$field['name']}}">
                        <option value="">-- {{$field['label']}} --</option>
                        @foreach($selects[$field['name']] as $option)
                            @if($option['value'] == $record[$field['name']])
                            <option selected value="{{$option['value']}}">{{$option['text']}}</option>
                            @else
                            <option value="{{$option['value']}}">{{$option['text']}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @else
                @if ($displayTypes[$field['display_type_id']] == 'link')
                    <div class="span4"><a href="{{$record[$field['name']]}}">{{$record[$field['name']]}}</a></div>
                @elseif ($displayTypes[$field['display_type_id']] == 'widget')
                    @if ($widgetTypes[$field['widget_type_id']] == 'textarea')
                        <div class="span6">
                            <textarea style="width:{{$field['width']}}px" name="{{$field['name']}}">
                                {{htmlentities($record[$field['name']])}}
                            </textarea>
                        </div>
                    @elseif ($widgetTypes[$field['widget_type_id']] == 'input:checkbox')
                        @if ($record[$field['name']]) 
                            <div class="span4" style="text-align:left;height:40px"><input type="checkbox" checked="checked" name="{{$field['name']}}" value="1" /></div>
                        @else
                            <div class="span4" style="text-align:left;height:40px"><input type="checkbox" name="{{$field['name']}}" value="1" /></div>
                        @endif
                    @elseif ($widgetTypes[$field['widget_type_id']] == 'ckeditor')
                        <div class="span6">
                            <textarea class="ckeditor" style="width:{{$field['width']}}px" name="{{$field['name']}}">
                                {{htmlentities($record[$field['name']])}}
                            </textarea>
                            <br />
                        </div>
                    @else
                        <div class="span4"><input type="text" style="width:{{$field['width']}}px" name="{{$field['name']}}" value="{{$record[$field['name']]}}" /></div>
                    @endif
                @else
                    <div class="span4"><input type="text" style="width:{{$field['width']}}px" name="{{$field['name']}}" value="{{$record[$field['name']]}}" /></div>
                @endif
            @endif
            @if(Options::get('configure'))
                <a href="/db/edit/_db_fields/{{$field['id']}}" id="btnFieldEdit" class="btn"><i class="icon-wrench"></i></a>
            @endif    
            
        </div>
        @endif
    
    @endforeach
    
    @if(Options::get('show-pk-tables'))
    <div class="accordion" id="accordion2">
        @foreach($tables as $tableName=>$table)
        
        <div class="accordion-group">
            <div class="accordion-heading btn btn-block">
              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#acc-{{$tableName}}">
                {{$tableName}}
              </a>
            </div>
            <div id="acc-{{$tableName}}" class="accordion-body collapse">
                <div class="accordion-inner">
                  
                </div>
            </div>            
        </div>
        @endforeach
    </div>
    @endif
    
    <div class="well"><input type="submit" class="btn" /></div>
    
</form>
@endforeach

@endif
@stop

