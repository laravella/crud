{{-- bottom of page --}}
@section('bottom')
    @if(Options::get('debug'))
    <textarea style="display:none" id="inputJSON"><?php print_r($params); ?></textarea>
    @endif
@stop
