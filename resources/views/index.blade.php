<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Backend</title>
    <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" type="text/css" href="/assets/datepicker3.css"/>    

</head>

<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">{{Lang::get('pages.dashboard.toggle_navigation')}}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">{{Lang::get('pages.dashboard.backend_portal')}}</a>
            </div>

            {{-- MENU TOP RIGHT SIDE --}}
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="#"><i class="fa fa-gear fa-fw"></i>{{Lang::get('pages.common.profile')}}</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/logout"><i class="fa fa-sign-out fa-fw"></i>{{Lang::get('pages.common.logout')}}</a>
                        </li>
                    </ul>
                </li>
            </ul>

            {{-- LEFT MENU --}}
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="/"><i class="fa fa-users"></i> {{Lang::get('pages.dashboard.list_customers')}}</a>
                        </li>
                        <li>
                            <a href="/customers"><i class="fa fa-user"></i> {{Lang::get('pages.dashboard.new_customer')}}</a>
                        </li>
                        <li>
                            <a href="/food"><i class="fa fa-spoon"></i> {{Lang::get('pages.dashboard.food_list')}}</a>
                        </li>
                        <li>
                            <a href="/new_food"><i class="fa fa-plus"></i> {{Lang::get('pages.dashboard.new_food')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <div id="page-wrapper">          
            @yield('content')
        </div>
    </div>
    

    @section('scripts')
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>

    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script type="text/javascript" src="/assets/bootstrap-datepicker.js"></script>
    
    @show
</body>
</html>
