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
                            <!--
				<ul class="nav">
					<li {{ (Request::is('/admin') ? 'class="active"' : '') }}><a href="{{ URL::to('/admin') }}"><i class="icon-th"></i> Dashboard</a></li>
				</ul>
                            -->
                            
                                @if (Auth::check())
				<ul class="nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Settings<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li {{ (Request::is('/db/select/users') ? 'class="active"' : '') }}><a href="/db/select/users"><i class="icon-user"></i> Users</a></li>
                                                        <!--
                                                        <li class="divider"></li>
							<li {{ (Request::is('/db/select/_db_menus') ? 'class="active"' : '') }}><a href="/db/select/_db_menus"><i class="icon-user"></i> Menus</a></li>
                                                        -->
						</ul>								
					</li>
				</ul>
                                @endif
                            {{--
				<ul class="nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Meta Data<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li {{ (Request::is('/db') ? 'class="active"' : '') }}><a href="/db"><i class="icon-user"></i> Home</a></li>
                                                        <li class="divider"></li>
							<li {{ (Request::is('/db/select/_db_tables') ? 'class="active"' : '') }}><a href="/db/select/_db_tables"><i class="icon-user"></i> Tables</a></li>
							<li {{ (Request::is('/db/select/_db_fields') ? 'class="active"' : '') }}><a href="/db/select/_db_fields"><i class="icon-user"></i> Fields</a></li>
							<li {{ (Request::is('/db/select/_db_actions') ? 'class="active"' : '') }}><a href="/db/select/_db_actions"><i class="icon-user"></i> Actions</a></li>
							<li {{ (Request::is('/db/select/_db_views') ? 'class="active"' : '') }}><a href="/db/select/_db_views"><i class="icon-user"></i> Views</a></li>
                                                        <!--
							<li {{ (Request::is('/db/select/_db_table_action_views') ? 'class="active"' : '') }}><a href="/db/select/_db_table_action_views"><i class="icon-user"></i> Action Views</a></li>
                                                        -->
                                                        <li class="divider"></li>
							<li {{ (Request::is('/db/select/_db_log') ? 'class="active"' : '') }}><a href="/db/select/_db_logs"><i class="icon-user"></i> Log</a></li>
							<li {{ (Request::is('/db/select/_db_audit') ? 'class="active"' : '') }}><a href="/db/select/_db_audit"><i class="icon-user"></i> Audit</a></li>
                                                        <li class="divider"></li>
							<li {{ (Request::is('/db/select/users') ? 'class="active"' : '') }}><a href="/db/select/users"><i class="icon-user"></i> Users</a></li>
							<li {{ (Request::is('/db/select/_db_group_permissions') ? 'class="active"' : '') }}><a href="/db/select/_db_usergroup_permissions"><i class="icon-user"></i> Group Permissions</a></li>
							<li {{ (Request::is('/db/select/_db_user_permissions') ? 'class="active"' : '') }}><a href="/db/select/_db_user_permissions"><i class="icon-user"></i> User Permissions</a></li>
                                                        <li class="divider"></li>
							<li {{ (Request::is('/dbinstall/install') ? 'class="active"' : '') }}><a href="/dbinstall/install"><i class="icon-user"></i> Install</a></li>
							<li {{ (Request::is('/dbinstall/reinstall') ? 'class="active"' : '') }}><a href="/dbinstall/reinstall"><i class="icon-user"></i> Reinstall</a></li>
							<li {{ (Request::is('/dbinstall/seeder') ? 'class="active"' : '') }}><a href="/dbinstall/seeder"><i class="icon-user"></i> Refresh Metadata</a></li>
                                                        
						</ul>								
					</li>
				</ul>
                            --}}
                                @if (Auth::check())
				<ul class="nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Contents<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li {{ (Request::is('/admin/pages/index') ? 'class="active"' : '') }}><a href="{{ URL::to('/admin/pages/index') }}"><i class="icon-file"></i> Pages</a></li>
							<li {{ (Request::is('/admin/posts/index') ? 'class="active"' : '') }}><a href="{{ URL::to('/admin/posts/index') }}"><i class="icon-pencil"></i> Posts</a></li>
							<li {{ (Request::is('/admin/categories/index') ? 'class="active"' : '') }}><a href="{{ URL::to('/admin/categories/index') }}"><i class="icon-list"></i> Post Categories</a></li>
							<li {{ (Request::is('/admin/medias/index') ? 'class="active"' : '') }}><a href="{{ URL::to('/admin/medias/index') }}"><i class="icon-picture"></i> Media Upload</a></li>

							<li {{ (Request::is('/db/select/medias') ? 'class="active"' : '') }}><a href="/db/select/medias"><i class="icon-picture"></i> Media</a></li>
							<li {{ (Request::is('/db/select/mcollection_media') ? 'class="active"' : '') }}><a href="/db/select/mcollection_media"><i class="icon-picture"></i> Media Collections</a></li>
							<li {{ (Request::is('/db/select/mcollections') ? 'class="active"' : '') }}><a href="/db/select/mcollections"><i class="icon-picture"></i> Collections</a></li>
						</ul>								
					</li>
				</ul>
                                @endif
				<ul class="nav pull-right">
					@if (Auth::check())
					<li class="navbar-text">Logged in as {{ Auth::user()->fullName() }}</li>
					<li class="divider-vertical"></li>
					<li {{ (Request::is('account') ? 'class="active"' : '') }}><a href="{{ URL::to('account') }}">Account</a></li>
					<li><a href="{{ URL::to('admin/logout') }}">Logout</a></li>
					@else
					<li {{ (Request::is('admin/login') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/login') }}">Login</a></li>
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