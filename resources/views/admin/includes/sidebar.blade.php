<style>
    .h6, h6 {
  color: white;
</style>
<!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
     <h6><b>HOME</b></h6>
     
      <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

     <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/show_drivers_map') ? 'active' : '' }}" href="{{ url('admin/show_drivers_map') }}">
          <i class="bi bi-card-list"></i>
          <span>Ride Map</span>
        </a>
     </li><!-- End Register Page Nav -->

      
   <!--<h6 class="mt-4"><b>MEMBERS</b></h6>-->
   
    <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/normal_users') ? 'active' : '' }}" href="{{ url('admin/normal_users') }}">
          <i class="bi bi-person"></i>
          <span>All Users</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link collapsed {{ (Request::is('admin/approved_drivers_list') || Request::is('admin/active_drivers_list') || Request::is('admin/add_new_drivers') || Request::is('admin/sespended_drivers_list')) ? 'active' : '' }}" data-bs-target="#tables-drivers" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Manage Drivers</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-drivers" class="nav-content collapse {{ (Request::is('admin/approved_drivers_list') || Request::is('admin/active_drivers_list') || Request::is('admin/add_new_drivers') || Request::is('admin/sespended_drivers_list')) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
            <li>
            <a class="{{ Request::is('admin/add_new_drivers') ? 'active' : '' }}" href="{{ url('admin/add_new_drivers') }}">
              <i class="bi bi-circle"></i><span>Add New Driver</span>
            </a>
          </li>
           <li>
            <a class="{{ Request::is('admin/active_drivers_list') ? 'active' : '' }}" href="{{ url('admin/active_drivers_list') }}">
              <i class="bi bi-circle"></i><span>Online Drivers</span>
            </a>
          </li>
          <li>
            <a class="{{ Request::is('admin/sespended_drivers_list') ? 'active' : '' }}" href="{{ url('admin/sespended_drivers_list') }}">
              <i class="bi bi-circle"></i><span>Suspended Drivers</span>
            </a>
          </li>
          <li>
            <a class="{{ Request::is('admin/approved_drivers_list') ? 'active' : '' }}" href="{{ url('admin/approved_drivers_list') }}">
              <i class="bi bi-circle"></i><span>All Drivers</span>
            </a>
          </li>
        </ul>
      </li>
    
     <li class="nav-item">
        <a class="nav-link collapsed {{ (Request::is('admin/all_vehicles') || Request::is('admin/vehicle_all_types')) ? 'active' : '' }}" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Manage Vehicles</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse {{ (Request::is('admin/all_vehicles') || Request::is('admin/vehicle_all_types')) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
           <li>
            <a class="{{ Request::is('admin/all_vehicles') ? 'active' : '' }}" href="{{ url('admin/all_vehicles') }}">
              <i class="bi bi-circle"></i><span>All Vehicles</span>
            </a>
          </li>
          <li>
            <a class="{{ Request::is('admin/vehicle_all_types') ? 'active' : '' }}" href="{{ url('admin/vehicle_all_types') }}">
              <i class="bi bi-circle"></i><span>Vehicle Category</span>
            </a>
          </li>
        </ul>
      </li>
    

      <!--<li class="nav-item">-->
      <!--  <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">-->
      <!--    <i class="bi bi-journal-text"></i><span>Drivers</span><i class="bi bi-chevron-down ms-auto"></i>-->
      <!--  </a>-->
      <!--  <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">-->

      <!--    <li>-->
      <!--      <a href="{{ url('admin/awative_drivers') }}">-->
      <!--        <i class="bi bi-circle"></i><span>Awaiting Approval</span>-->
      <!--      </a>-->
      <!--    </li>-->

      <!--    <li>-->
      <!--      <a href="{{ url('admin/approved_drivers_list') }}">-->
      <!--        <i class="bi bi-circle"></i><span>Manage Drivers</span>-->
      <!--      </a>-->
      <!--    </li>-->

      <!--  </ul>-->
      <!--</li>-->


      <!--<h6 class="mt-4"><b>BOOKING</b></h6>-->

      <li class="nav-item">
        <a class="nav-link collapsed {{ (Request::is('admin/pending_booking') || Request::is('admin/complete_booking') || Request::is('admin/cancelled_booking') || Request::is('admin/schedule_booking')) ? 'active' : '' }}" data-bs-target="#tables-booking" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>All Rides</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-booking" class="nav-content collapse {{ (Request::is('admin/pending_booking') || Request::is('admin/complete_booking') || Request::is('admin/cancelled_booking') || Request::is('admin/schedule_booking')) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
          <li>
            <a class="{{ Request::is('admin/pending_booking') ? 'active' : '' }}" href="{{ url('admin/pending_booking') }}">
              <i class="bi bi-circle"></i><span>Ongoing Rides</span>
            </a>
          </li>
          <li>
            <a class="{{ Request::is('admin/complete_booking') ? 'active' : '' }}" href="{{ url('admin/complete_booking') }}">
              <i class="bi bi-circle"></i><span>Completed Rides</span>
            </a>
          </li>
           <li>
            <a class="{{ Request::is('admin/cancelled_booking') ? 'active' : '' }}" href="{{ url('admin/cancelled_booking') }}">
              <i class="bi bi-circle"></i><span>Cancelled Rides</span>
            </a>
          </li>
          <li>
            <a class="{{ Request::is('admin/schedule_booking') ? 'active' : '' }}" href="{{ url('admin/schedule_booking') }}">
              <i class="bi bi-circle"></i><span>Schedule Rides</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->


     <!--<h6 class="mt-4"><b>PAYMENTS</b></h6>-->

      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/booking_payments') ? 'active' : '' }}" href="{{ url('admin/booking_payments') }}">
          <i class="bi bi-person"></i>
          <span>Booking Payments</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/coupon_codes') ? 'active' : '' }}" href="{{ url('admin/coupon_codes') }}">
          <i class="bi bi-person"></i>
          <span>Coupons</span>
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/withdrawal_request') ? 'active' : '' }}" href="{{ url('admin/withdrawal_request') }}">
          <i class="bi bi-envelope"></i>
          <span>Withdraw Requests</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/manage_comission') ? 'active' : '' }}" href="{{ url('admin/manage_comission') }}">
          <i class="bi bi-file-earmark"></i>
          <span>Manage Commission</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/weekly_payouts') ? 'active' : '' }}" href="{{ url('admin/weekly_payouts') }}">
          <i class="bi bi-file-earmark"></i>
          <span>Weekly Payout</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/all_notifications') ? 'active' : '' }}" href="{{ url('admin/all_notifications') }}">
          <i class="bi bi-file-earmark"></i>
          <span>Notification</span>
        </a>
      </li>

     <!--<h6 class="mt-4"><b>SETTINGS</b></h6>-->
     
       <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/third_party') ? 'active' : '' }}" href="{{ url('admin/third_party') }}">
          <i class="bi bi-card-list"></i>
          <span>General Settings</span>
        </a>
      </li>
      
      <!-- End Register Page Nav -->
     

     <!--<h6 class="mt-4"><b>CMS</b></h6>-->
     
      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/about_us') ? 'active' : '' }}" href="{{ url('admin/about_us') }}">
          <i class="bi bi-person"></i>
          <span>About Us</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/contact_us') ? 'active' : '' }}" href="{{ url('admin/contact_us') }}">
          <i class="bi bi-question-circle"></i>
          <span>Contact Us</span>
        </a>
      </li><!-- End F.A.Q Page Nav -->


      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/privacy_policy') ? 'active' : '' }}" href="{{ url('admin/privacy_policy') }}">
          <i class="bi bi-dash-circle"></i>
          <span>Privacy Policy User</span>
        </a>
      </li><!-- End Error 404 Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/privacy_policy_driver') ? 'active' : '' }}" href="{{ url('admin/privacy_policy_driver') }}">
          <i class="bi bi-dash-circle"></i>
          <span>Privacy Policy Driver</span>
        </a>
      </li><!-- End Error 404 Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/terms_conditions') ? 'active' : '' }}" href="{{ url('admin/terms_conditions') }}">
          <i class="bi bi-file-earmark"></i>
          <span>Terms & Conditions</span>
        </a>
      </li>
      
      <!--<li class="nav-item">-->
      <!--  <a class="nav-link collapsed {{ Request::is('admin/terms_conditions') ? 'active' : '' }}" href="{{ url('admin/terms_conditions') }}">-->
      <!--    <i class="bi bi-file-earmark"></i>-->
      <!--    <span>Terms & Conditions Driver</span>-->
      <!--  </a>-->
      <!--</li>-->
      
      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/ride_cancel_reasons') ? 'active' : '' }}"  href="{{ url('admin/ride_cancel_reasons') }}">
          <i class="bi bi-card-list"></i>
          <span>Ride Cancel Reasons</span>
        </a>
      </li><!-- End Register Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed {{ Request::is('admin/faqs') ? 'active' : '' }}" href="{{ url('admin/faqs') }}">
          <i class="bi bi-question-circle"></i>
          <span>Faqs</span>
        </a>
      </li><!-- End F.A.Q Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->