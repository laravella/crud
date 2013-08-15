<!DOCTYPE html>
<!--[if lt IE 7]>      <html xmlns="http://www.w3.org/1999/xhtml" ng-app="app" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html xmlns="http://www.w3.org/1999/xhtml" ng-app="app" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html xmlns="http://www.w3.org/1999/xhtml" ng-app="app" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html xmlns="http://www.w3.org/1999/xhtml" ng-app="app" class="no-js"> <!--<![endif]-->    
    
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>
            @section('title')
            Laravel Radiate
            @show
        </title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('assets/styles/css/main.css')}} ">
        <link rel="stylesheet" href="/packages/laravella/crud/assets/styles/adminstyles.css">

        <!-- JS -->
        <script src="{{ asset('assets/scripts/js/vendor/modernizr-2.6.2.min.js') }}"></script>
        <script type="text/javascript" src="/packages/laravella/crud/assets/scripts/jsonconvert.js"></script>
        <script type="text/javascript" src="/packages/laravella/crud/assets/scripts/admintools.js"></script>

        <!-- Images -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('assets/images/apple-touch-icon-144-precomposed.png') }}">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ asset('assets/images/apple-touch-icon-114-precomposed.png') }}">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ asset('assets/images/apple-touch-icon-72-precomposed.png') }}">
        <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/apple-touch-icon-57-precomposed.png') }}">

        <!-- ICO -->
        <link rel="shortcut icon" href="favicon.ico">
			
        <!-- Ravel CMS -->
        @include('crud::layouts.admin.assets')
        @yield('assets_css')
        @yield('assets_js')
		
        <!-- Additional javascript defined in the template -->
        @yield('extra_head')
		
    </head>
    <body>
        
        <!-- default.blade.php --> 
        
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
		
        @yield('navbar') 
		
        <!-- Wrapper for the radial gradient background -->
        <div id="body-wrapper"> 

            <div id="main-content"> <!-- Main Content Section with everything -->

                <noscript> <!-- Show a notification if the user has disabled javascript -->
                    <div class="notification error png_bg">
                        <div>
                            Javascript is disabled or is not supported by your browser. Please <a href="http://browsehappy.com/" title="Upgrade to a better browser">upgrade</a> your browser or <a href="http://www.google.com/support/bin/answer.py?answer=23852" title="Enable Javascript in your browser">enable</a> Javascript to navigate the interface properly.
                        </div>
                    </div>
                </noscript>

                <div ng-show='nowloading'><i class="icon-spinner icon-spin"  style="display:none"></i></div>

                @yield('appcontainer')

				<!-- Additiona javascript defined in the template -->
                @yield('javascripts')

				<!-- Add your site or application content here -->
				<!-- Container -->
				<div class="container">

					<!-- Content -->
					@yield('content')
					<!-- ./ content -->

				</div>
				<!-- ./ container -->
				
                @include('crud::layouts.admin.footer')

            </div> <!-- End #main-content -->

        </div>

        @yield('bottom')
        
        <!-- jQuery -->
        <!--
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/scripts/js/vendor/jquery-1.8.3.min.js"><\/script>')</script>
        -->
        
        <script src="{{ asset('assets/scripts/js/plugins.js') }}"></script>
        <script src="{{ asset('assets/scripts/js/main.js') }}"></script>
        <script src="{{ asset('assets/scripts/js/vendor/bootstrap.min.js') }}"></script>
        
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            // var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            // (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            // g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
            // s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>
