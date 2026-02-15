<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="{{ url('/') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item nav-category">Clinical</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#patients" aria-expanded="false" aria-controls="patients">
                <i class="menu-icon mdi mdi-account-group"></i>
                <span class="menu-title">Patients</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="patients">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ url('/patients') }}">All Patients</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ url('/patients/create') }}">Register Patient</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#appointments" aria-expanded="false" aria-controls="appointments">
                <i class="menu-icon mdi mdi-calendar-clock"></i>
                <span class="menu-title">Appointments</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="appointments">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ url('/appointments') }}">All Appointments</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ url('/appointments/create') }}">Schedule New</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#emr" aria-expanded="false" aria-controls="emr">
                <i class="menu-icon mdi mdi-file-document-outline"></i>
                <span class="menu-title">Medical Records</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="emr">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ url('/emr') }}">View Records</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ url('/emr/create') }}">New Clinical Note</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item nav-category">Management</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#doctors" aria-expanded="false" aria-controls="doctors">
                <i class="menu-icon mdi mdi-doctor"></i>
                <span class="menu-title">Doctors</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="doctors">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ url('/doctors') }}">Manage Doctors</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ url('/doctors/create') }}">Add Doctor</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#billing" aria-expanded="false" aria-controls="billing">
                <i class="menu-icon mdi mdi-currency-usd"></i>
                <span class="menu-title">Billing</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="billing">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ url('/billing') }}">Invoices</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ url('/billing/create') }}">New Invoice</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item nav-category">System</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Users</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ url('/users') }}">Manage Users</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </nav>