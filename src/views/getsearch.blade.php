{{-------------------------------------------------------- getSearch --------------}}

@section('search')
@if($action == 'getSelect' || $action == 'getSearch')
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

                @if(isset($meta[$field['name']]['pk']))
                {{-- this is a foreign key, it contains a reference to a primary key --}}
                    <div class="span3">
                        <select name="{{$field['name']}}" class="formfield" data-table="{{$tableName}}">
                            <option value="">--{{$field['name']}}--</option>
                            @foreach($selects[$field['name']] as $option)
                                <option value="{{$option['value']}}">{{$option['text']}}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="span3"><input style="width:{{$field['width']}}px" 
                                              class="formfield" type="text" data-table="{{$tableName}}" 
                                              name="{{$field['name']}}" /></div>
                @endif
            @endif
            @endforeach
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" onclick="javascript:sendSearch()">Search</button>
    </div>        
</div>
@endif
@stop