<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Login - Monitoring Ibu Hamil</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
      body {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         min-height: 100vh;
         display: flex;
         align-items: center;
      }

      .login-card {
         background: white;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         overflow: hidden;
      }

      .login-header {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: white;
         padding: 40px;
         text-align: center;
      }

      .login-body {
         padding: 40px;
      }

      .form-control {
         border-radius: 10px;
         border: 2px solid #e9ecef;
         padding: 12px 15px;
      }

      .form-control:focus {
         border-color: #667eea;
         box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
      }

      .btn-login {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         border: none;
         border-radius: 10px;
         padding: 12px;
         font-weight: 600;
         width: 100%;
      }

      .btn-login:hover {
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-md-6 col-lg-4">
            <div class="login-card">
               <div class="login-header">
                  <i class="fas fa-baby fa-3x mb-3"></i>
                  <h3>Monitoring Ibu Hamil</h3>
                  <p class="mb-0">Silakan login untuk melanjutkan</p>
               </div>

               <div class="login-body">
                  @if($errors->any())
                  <div class="alert alert-danger">
                     <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                     </ul>
                  </div>
                  @endif

                  <form method="POST" action="{{ route('login') }}">
                     @csrf
                     <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                           <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                           <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                     </div>

                     <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                           <span class="input-group-text"><i class="fas fa-lock"></i></span>
                           <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                     </div>

                     <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                     </button>
                  </form>

                  <div class="text-center mt-3">
                     <p class="mb-0">Belum punya akun?
                        <a href="{{ route('register') }}" class="text-decoration-none">Daftar disini</a>
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>