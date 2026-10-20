<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Laravel Class Record System - Login">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Login - Class Record System</title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo e(asset('vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo e(asset('css/sb-admin-2.min.css')); ?>" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card-login {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .login-heading {
            font-weight: 700;
            color: #5a5c69;
        }
        
        .form-control-user {
            font-size: 0.9rem;
            border-radius: 10rem;
            padding: 1.5rem 1rem;
        }
        
        .btn-user {
            font-size: 0.9rem;
            border-radius: 10rem;
            padding: 0.75rem 1rem;
        }
        
        .bg-login-image {
            background: url('https://source.unsplash.com/K4mSJ7kc0As/600x800');
            background-position: center;
            background-size: cover;
            display: none;
        }
        
        .system-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .system-logo i {
            font-size: 2.5rem;
            color: white;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="container">
            <!-- Outer Row -->
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-9">
                    <div class="card card-login o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <div class="system-logo">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <h1 class="h4 text-gray-900 mb-2 login-heading">Class Record System</h1>
                                            <p class="mb-4 text-gray-600">Welcome back! Please login to your account.</p>
                                        </div>

                                        <!-- Session Status -->
                                        <?php if(session('status')): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?php echo e(session('status')); ?>

                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <form method="POST" action="<?php echo e(route('login')); ?>" class="user">
                                            <?php echo csrf_field(); ?>

                                            <!-- Email Address -->
                                            <div class="form-group">
                                                <input type="email" 
                                                       class="form-control form-control-user <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       id="email" 
                                                       name="email" 
                                                       value="<?php echo e(old('email')); ?>"
                                                       placeholder="Enter Email Address..." 
                                                       required 
                                                       autofocus 
                                                       autocomplete="username">
                                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                                                    </div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>

                                            <!-- Password -->
                                            <div class="form-group">
                                                <input type="password" 
                                                       class="form-control form-control-user <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       id="password" 
                                                       name="password" 
                                                       placeholder="Password" 
                                                       required 
                                                       autocomplete="current-password">
                                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?>

                                                    </div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>

                                            <!-- Remember Me -->
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox small">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="remember_me" 
                                                           name="remember">
                                                    <label class="custom-control-label" for="remember_me">
                                                        Remember Me
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Login Button -->
                                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                                <i class="fas fa-sign-in-alt"></i> Login
                                            </button>

                                            <hr>

                                            <!-- Forgot Password Link -->
                                            <?php if(Route::has('password.request')): ?>
                                                <div class="text-center">
                                                    <a class="small" href="<?php echo e(route('password.request')); ?>">
                                                        <i class="fas fa-lock"></i> Forgot Password?
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </form>

                                        <hr>

                                        <div class="text-center">
                                            <p class="small text-muted mb-0">
                                                <i class="fas fa-info-circle"></i> 
                                                For account registration, please contact your administrator.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="text-center text-white mb-4">
                        <p class="mb-2">
                            <i class="fas fa-users"></i> Access Levels: 
                            <span class="badge badge-danger">Admin</span>
                            <span class="badge badge-success">Professor</span>
                            <span class="badge badge-info">Student</span>
                        </p>
                        <p class="small mb-0">
                            &copy; 2025 YBC. All Rights Reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo e(asset('vendor/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo e(asset('js/sb-admin-2.min.js')); ?>"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>
<?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/auth/login.blade.php ENDPATH**/ ?>