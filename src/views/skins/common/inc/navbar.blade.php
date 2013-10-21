@section('navbar')
<!-- layouts.partials.navbar -->
<div class="navbar navbar-fixed-top" style="-webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0;">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<div class="nav-collapse collapse">
				<ul class="nav">
					<li {{ (Request::is('/') ? 'class="active"' : '') }}><a href="{{ URL::to('') }}"><i class="icon-home"></i> Home</a></li>
				</ul>
                                
                                @if(isset($menu) && is_array($menu))
                                    @foreach($menu as $label=>$menuGroup)
                                    <ul class="nav">
                                            <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{$label}}<b class="caret"></b></a>
                                                    <ul class="dropdown-menu">
                                                        @foreach($menuGroup as $menuItem)
                                                            @if ($menuItem['m2_label'] == 'divider')
                                                                <li class="divider"></li>
                                                            @else
                                                                <li {{ (Request::is($menuItem['m2_href']) ? 'class="active"' : '') }}><a href="{{ $menuItem['m2_href'] }}"><i class="icon-file"></i> {{$menuItem['m2_label']}}</a></li>
                                                            @endif
                                                        @endforeach    
                                                    </ul>
                                            </li>
                                    </ul>
                                    @endforeach
                                @endif
                                
				<ul class="nav pull-right">
					@if (Auth::check())
					<li class="navbar-text">Logged in as {{ Auth::user()->fullName() }}</li>
					<li class="divider-vertical"></li>
					<li {{ (Request::is('account') ? 'class="active"' : '') }}><a href="{{ URL::to('account') }}">Account</a></li>
					<li><a href="{{ URL::to('account/logout') }}">Logout</a></li>
					@else
					<li {{ (Request::is('admin/login') ? 'class="active"' : '') }}><a href="{{ URL::to('account/login') }}">Login</a></li>
					{{-- <li {{ (Request::is('account/register') ? 'class="active"' : '') }}><a href="{{ URL::to('account/register') }}">Register</a></li> --}}
					@endif
				</ul>
			</div>
			<!-- ./ nav-collapse -->
		</div>
	</div>
</div>
<!-- ./ navbar -->
@stop