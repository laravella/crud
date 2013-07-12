{{-- Web site Title --}}
@section('title')
@parent
:: DbView
@stop

@section('content')
<h1>DbView</h1>
    @foreach($data as $record)
        {{$record->id}}
    @endforeach
@stop
