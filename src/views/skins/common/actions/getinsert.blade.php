{{-------------------------------------------------------- getInsert --------------}}

@section('getInsert') 
@if($action == 'getInsert')
<div class="page-header">
    <h1>New</h1>
</div>

<div class="well">
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

<form method="POST" action="/db/insert/{{$tableName}}">
    @foreach($meta as $field)
    @if ($displayTypes[$field['display_type_id']] != 'nodisplay')
    <div class="row">
        <div class="span4">{{$field['label']}}</div>
        @if(isset($field['key']) && $field['key'] == 'PRI')
        <div class="span4"><input type="text" disabled name="{{$field['name']}}" value="" /></div>
        @elseif(isset($field['pk']))
        <div class="span4">
        @if(!empty($field['widget_type_id']) && $widgetTypes[$field['widget_type_id']] == 'multiselect')
            <select name="{{$field['name']}}" multiple>
        @else 
            <select name="{{$field['name']}}">
                <option value="">-- {{$field['label']}} --</option>
        @endif 
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
            @if ($displayTypes[$field['display_type_id']] == 'widget')
                @if ($widgetTypes[$field['widget_type_id']] == 'textarea')
                    <div class="span6">
                        <textarea style="width:{{$field['width']}}px" name="{{$field['name']}}">
                            {{$field['default']}}
                        </textarea>
                    </div>
                @elseif ($widgetTypes[$field['widget_type_id']] == 'ckeditor')
                    <div class="span6">
                        <textarea class="ckeditor" style="width:{{$field['width']}}px" name="{{$field['name']}}">
                            {{$field['default']}}
                        </textarea>
                        <br />
                    </div>
                @else
                    <div class="span4"><input type="text" style="width:{{$field['width']}}px" name="{{$field['name']}}" value="{{$field['default']}}" /></div>
                @endif
            @else
                <div class="span4"><input type="text" style="width:{{$field['width']}}px" name="{{$field['name']}}" value="{{$field['default']}}" /></div>
            @endif
        @endif
    </div>
    @endif
    @endforeach<br />
    <div class="well"><input type="submit" class="btn" /></div>
</form>
@endif
@stop
