{{-------------------------------------------------------- widget.select --------------}}
@section('select') 
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
@show

