{{-------------------------------------------------------- widget.textarea --------------}}
@section('textarea') 
    <div class="span6">
        <textarea style="width:{{$field['width']}}px" name="{{$field['name']}}">
            {{$record[$field['name']]}}
        </textarea>
    </div>
@show

