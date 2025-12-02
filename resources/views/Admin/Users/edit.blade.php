@extends('Admin.layout.template')
@section('middlecontent')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-fullscreen/dist/leaflet.fullscreen.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/4.0.0/leaflet-search.min.css" />
    <!-- Main content -->
   <section class="content">
      <div class="container-fluid">
         <form method="POST" action="{{route('adminUserStore')}}" class="needs-validation"    enctype="multipart/form-data" novalidate="" id="user-form">
         @csrf
        <div class="row">
            <div class="col-md-12">
            <div class="card">
            <div class="card-body">
              <input type="hidden" name="user" id="user" value="{{Request::segment(2)}}">
               <input type="hidden" name="id" class="id" id="id" value="{{$id}}">

               <div class="form-group">
                  <label>Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" id="name"  tabindex="2" value="{{ (old('name')) ?  old('name') : $userDetails['name']}}" placeholder="{{ __('admin.name')}}">
                  <div class="text-danger" id="err_name">{{$errors->first('name')}}</div>
               </div>

                <div class="form-group">
                  <label>Upasana Kendra <span class="text-danger">*</span></label>
                    <select name="upasana_kendra" class="form-control" id="upasana_kendra">
                        <option value="">Select upasana kendra</option>
                        @if(!empty($upasana_kendra))
                          @foreach($upasana_kendra as $data)
                            <option value="{{$data->id}}" @if($data->id==$userDetails['upasana_kendra_id']) selected='selected' @endif>{{$data->name}}</option>
                          @endforeach
                        @endif
                    </select>
                  <span id="err_upasana_kendra" class="text-danger"> @if($errors->has('upasana_kendra')) {{ $errors->first('upasana_kendra') }}@endif </span>
                </div>

                <div class="form-group">
                  <label>{{ __('admin.phone_no')}} <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" name="phone_number" id="phone_number"  tabindex="2" value="{{ (old('phone_number')) ?  old('phone_number') : $userDetails['phone_number']}}" placeholder="{{ __('admin.phone_no')}}">
                  <div class="text-danger" id="err_phone_number">{{$errors->first('phone_number')}}</div>
               </div>

               <div class="form-group">
                  <label>{{ __('admin.email_title')}} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="email" id="email"  tabindex="2" value="{{ (old('email')) ?  old('email') : $userDetails['email']}}" placeholder="{{ __('admin.email_title')}}">
                  <div class="text-danger" id="err_email">{{$errors->first('email')}}</div>
               </div>

              
                <div class="form-group">
                  <label>Location <span class="text-danger">*</span></label>
                  <textarea name="location" id="location" class="location form-control" placeholder="Enter your location"> @if(!empty($userDetails['location'])) {{$userDetails['location']}} @endif</textarea>
                  <div class="text-danger" id="err_location">{{$errors->first('location')}}</div>
               </div>

                <div class="loader" style="display:none;"></div>
                    <div class="mb-4 position-relative">
                        <div class="form-group increment cloned">
                        <label  class="label_css">{{ __('admin.select_profile_pic')}}</label>
                        <div class="row">
                        <div class="col-md-4 existing-images">
                        @php
                        if(!empty($userDetails->profile))
                        {
                        echo '<div><img src="'.url('/').'/storage/uploads/User/resized/'.$userDetails->profile.'" class="buyer_profile_update_img" id="buyer_profile_update_img"><!--<a href="javascript:void(0);" class="remove_buyer_profile text-danger"> <i class="fas fa-trash"></i> '.trans("admin.remove_label").'</a>--></div>';

                        }else{
                        echo '<img src="'.url('/').'/storage/uploads/User/default/no_user_circle.png" class="buyer_profile_update_img">';
                        }             
                        @endphp
                        </div>
                        </div>
                        <input type="file" name="profile" class="form-control" id="buyer_profile_image" value="{{old('profile')}}"   style="cursor: pointer;" /></button>
                        <!--  </div> -->

                        <div class="text-danger">{{$errors->first('filename')}}</div>
                        <div class="input-group-btn text-right"> </div>
                    </div>


                  <div id="map" style="height: 500px;"></div>
                  <div class="mb-3">
                    <label for="">latitude <span class="text-danger">*</span></label>
                  <input type="text" id="latitude" name="latitude" class="form-control" value="{{ (isset($userDetails['latitude'])) ?  $userDetails['latitude'] : old('latitude') }}" readonly />
                  <div class="text-danger err_latitude">{{ ($errors->has('latitude')) ? $errors->first('latitude') : '' }}</div>
                  </div>
                  <div class="mb-3">
                    <label for="">longitude <span class="text-danger">*</span></label>
                    <input type="text " id="longitude" name="longitude" class="form-control" value="{{ (isset($userDetails['longitude'])) ?  $userDetails['longitude'] : old('longitude') }}" readonly />
                    <div class="text-danger err_longitude">{{ ($errors->has('longitude')) ? $errors->first('longitude') : '' }}</div>
                  </div>
             
            </div>

         
             <div class="col-12" style="margin-bottom: 60px;">
              <button type="submit" class="btn btn-icon icon-left btn-success userRegisterbtn" id="saveUser" tabindex="15"><i class="fas fa-check"></i>{{ __('admin.update_btn')}}</button>&nbsp;&nbsp;
               <a href="{{route('adminUsers')}}" class="btn btn-icon icon-left btn-danger" tabindex="16"><i class="fas fa-times"></i>{{ __('admin.cancel_btn')}}</a>
              </div>
            

            </div>     

            </div>
         
           
         </div>
         </form>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

<script src="{{url('/')}}/assets/admin/js/jquery.min.js"></script>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-fullscreen/dist/Leaflet.fullscreen.min.js"></script>

<script>
    
$(document).ready(function() {  
    var latitude = document.getElementById('latitude').value;
    var longitude = document.getElementById('longitude').value;
    if(latitude=="" || longitude==""){
        var map = L.map('map').setView([20.5937, 78.9629], 5); //india lat log
    }else{
        var map = L.map('map').setView([latitude, longitude], 13); //current location lat long
    }


    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
    }).addTo(map);
   
    if(latitude!="" || longitude!=""){
        // Add marker to the map
        L.marker([latitude, longitude]).addTo(map);
    }
        
    // Add fullscreen control
    map.addControl(new L.Control.Fullscreen());

   map.on('click', function(e) {
         alert("You clicked the map at " + e.latlng);
          document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
  });

});
</script>

@endsection('middlecontent')
