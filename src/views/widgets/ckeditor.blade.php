{{-------------------------------------------------------- widget.ckeditor --------------}}
@section('ckeditor') 
    <div class="span6">
        <textarea class="ckeditor" style="width:{{$field['width']}}px" name="{{$field['name']}}">
            {{$record[$field['name']]}}
        </textarea>
    </div>
@show

