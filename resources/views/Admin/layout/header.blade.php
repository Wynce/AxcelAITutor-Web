  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
       <ul class="navbar-nav ml-auto">
        @php
        
        // Default logo path if not found in the database
        $defaultLogoPath = asset('assets/settings/axcel_logo.png');

        @endphp
  

    <div class="dropdown">
    <a class=" dropdown-toggle"  id="menu1" data-toggle="dropdown">
      <img src="{{ $defaultLogoPath }}" alt="{{config('constants.APP_NAME')}} Logo" class="brand-image img-circle elevation-3"  style="opacity: .8;height: 33px;width:33px " />
    </a>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
      <a href="javascript:void(0);" class="dropdown-item has-icon">
      
              <img src="{{ $defaultLogoPath }}" alt="{{config('constants.APP_NAME')}} Logo" class="brand-image img-circle elevation-3"  style="opacity: .8;height: 33px;width:33px;margin-right: 10px;" />
    
            {{config('constants.APP_NAME')}}
          </a>
        <a href="{{route('adminLogout')}}" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
    </ul>
  </div>
</ul>
 </nav>

