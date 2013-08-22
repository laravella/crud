{{-------------------------------------------------------- widget.input --------------}}
@section('input') 
    <div class="span4"><input type="text" style="width:{{$field['width']}}px" name="{{$field['name']}}" value="{{$record[$field['name']]}}" /></div>
@show

