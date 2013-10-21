@section('assets_css')
<!-- CSS -->

<link rel="stylesheet" href="/assets/styles/css/main.css">
<link rel="stylesheet" href="/packages/laravella/crud/assets/styles/adminstyles.css">
@stop

@section('assets_js')		
<!-- JS -->

<!-- jQuery -->
<script type="text/javascript" src="/packages/laravella/crud/assets/scripts/jquery-1.8.3.min.js"></script>

<!-- JS -->
<script type="text/javascript" src="/packages/laravella/crud/assets/scripts/modernizr-2.6.2.min.js"></script>

@if(Options::get('debug'))
<script type="text/javascript" src="/packages/laravella/crud/assets/scripts/jsonconvert.js"></script>
@endif

<script type="text/javascript" src="/packages/laravella/crud/assets/scripts/admintools.js"></script>

<!-- CKEditor -->
<script type="text/javascript" src="/packages/laravella/crud/assets/scripts/ckeditor/ckeditor.js"></script>

<!-- Internet Explorer .png-fix -->

<!--[if IE 6]>
        <script type="text/javascript" src="packages/laravella/crud/assets/scripts/DD_belatedPNG_0.0.7a.js"></script>
        <script type="text/javascript">
                DD_belatedPNG.fix('.png_bg, img, li');
        </script>
<![endif]-->
@stop
