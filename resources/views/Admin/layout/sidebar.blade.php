<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{route('adminDashboard')}}" class="brand-link">
        @php
          $defaultLogoPath = asset('assets/settings/axcel_logo.png');
        @endphp

   
          <img src="{{ $defaultLogoPath }}" alt="{{config('constants.APP_NAME')}} Logo" class="brand-image img-circle elevation-3"  style="opacity: .8;height: 33px;width:33px " />

   
    <span class="brand-text font-weight-light">{{config('constants.APP_NAME')}} </span>
  </a>
<!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->


      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        @php
          if((Request::segment(2)=='dashboard')){
            $activeClass = 'active';
          }
          else{
            $activeClass = '';
          }
        @endphp
        <li class="nav-item">
          <a href="{{route('adminDashboard')}}" class="nav-link {{$activeClass}}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              
            </p>
          </a>
        </li>
        
        
        @php
            if((Request::segment(2)=='settings')){
              $activeClass = 'active';
            }
            else{
             $activeClass = '';
            }
        @endphp

          <li class="nav-item has-treeview">
            <a href="{{route('adminSettings')}}" class="nav-link {{$activeClass}}">
                <i class="nav-icon fas fa-cog"></i>
            <p>
            Settings
            </p>
            </a>
          </li>
   
          @php
            if((Request::segment(2)=='users')){
              $activeClass = 'active';
            }
            else{
             $activeClass = '';
            }
          @endphp

          <li class="nav-item has-treeview">
            <a href="{{route('adminUsers')}}" class="nav-link {{$activeClass}}">
            <i class="nav-icon fas fa-user"></i>
            <p>
            Users
            </p>
            </a>
          </li>


        @php
          if((Request::segment(2)=='change-password')){
          $activeClass = 'active';
          }
          else{
          $activeClass = '';
          }
        @endphp
        <li class="nav-item">
          <a href="{{route('adminChangePassword')}}" class="nav-link {{$activeClass}}">
            <i class="nav-icon fas fa-key"></i>
            <p>
              Change Password
              
            </p>
          </a>
        </li>
        
        
        
        <li class="nav-item">
            <a href="{{route('admin.notifications.index')}}" class="nav-link">
                <i class="nav-icon fas fa-bell"></i>
                <p>Notifications</p>
            </a>
        </li>

        
      

           <li class="nav-item has-treeview">
            <a href="{{route('adminLogout')}}" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>
              Logout
            </p>
          </a>
        </li>
        </ul>
      </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>