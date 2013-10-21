{{-------------------------------------------------------- widget.input --------------}}
@section('input') 
    <div class="span4"><input type="text" disabled name="{{$field['name']}}" value="{{$record[$field['name']]}}" /></div>
@show

