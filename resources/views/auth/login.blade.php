<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | MediFlow</title>
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendors/feather/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/ti-icons/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/typicons/typicons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/simple-line-icons/css/simple-line-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}" />
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .auth-form-light {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-logo img {
            width: 180px;
            height: auto;
        }
        .auth-link {
            color: #667eea;
            text-decoration: none;
        }
        .auth-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth-page">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 col-md-6 col-sm-8 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{ asset('assets/images/MediFlow.svg') }}" alt="MediFlow" style="max-height: 100px;">
                            </div>
                            <h4 class="text-center mb-2">Welcome Back!</h4>
                            <h6 class="fw-light text-center mb-4">Sign in to continue to MediFlow</h6>
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form class="pt-3" action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Enter your email" 
                                           value="{{ old('email') }}"
                                           required 
                                           autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password" 
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input" name="remember">
                                            Keep me signed in
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
                                        SIGN IN
                                    </button>
                                </div>
                                <div class="text-center mt-4 fw-light">
                                    <p class="text-muted mb-0">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Default credentials: <strong>admin@mediflow.com</strong> / <strong>admin</strong>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Vendor JS -->
    <script src="{{asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- App JS -->
    <script src="{{asset('assets/js/off-canvas.js')}}"></script>
    <script src="{{asset('assets/js/template.js')}}"></script>
</body>
</html>
