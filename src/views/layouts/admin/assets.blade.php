@section('assets_css')
        <!-- CSS asdfasdf-->

                <link rel="stylesheet" href="{{ asset('assets/styles/css/main.css')}} ">
                <link rel="stylesheet" href="/packages/laravella/crud/assets/styles/adminstyles.css">
	  
		<!-- icons css style -->
		<link rel="stylesheet" href="<?php echo admin_asset('admin/fontawesome/css/font-awesome.min.css');?>" type="text/css" media="screen" />
	  
		<!-- Internet Explorer Fixes Stylesheet -->
		
		<!--[if lte IE 7]>
			
			<link rel="stylesheet" href="<?php echo admin_asset('admin/fontawesome/css/font-awesome-ie7.min.css');?>" type="text/css" media="screen" />
	  
			<link rel="stylesheet" href="<?php echo admin_asset('admin/css/ie.css');?>" type="text/css" media="screen" />
		<![endif]-->

		<!-- jquery ui -->
		<link rel="stylesheet" href="<?php echo admin_asset('admin/css/smoothness/jquery-ui-1.10.0.custom.min.css');?>" type="text/css" media="screen" />
		
@stop

@section('assets_js')		
                <!-- JS -->
  
		<!-- jQuery -->
		<script type="text/javascript" src="<?php echo admin_asset('admin/scripts/jquery.min.js');?>"></script>
		<script type="text/javascript" src="<?php echo admin_asset('admin/scripts/jquery-ui-1.10.0.smoothness.min.js');?>"></script>
                
                <!-- JS -->
                <script src="{{ asset('assets/scripts/js/vendor/modernizr-2.6.2.min.js') }}"></script>
                <script type="text/javascript" src="/packages/laravella/crud/assets/scripts/jsonconvert.js"></script>
                <script type="text/javascript" src="/packages/laravella/crud/assets/scripts/admintools.js"></script>
                
		<!-- CKEditor -->
		<script type="text/javascript" src="<?php echo admin_asset('admin/scripts/ckeditor/ckeditor.js');?>"></script>
                
		<!-- Internet Explorer .png-fix -->
		
		<!--[if IE 6]>
			<script type="text/javascript" src="<?php echo admin_asset('admin/scripts/DD_belatedPNG_0.0.7a.js');?>"></script>
			<script type="text/javascript">
				DD_belatedPNG.fix('.png_bg, img, li');
			</script>
		<![endif]-->
@stop
