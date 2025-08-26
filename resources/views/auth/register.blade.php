<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Register - Monitoring Ibu Hamil</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
      body {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         min-height: 100vh;
         display: flex;
         align-items: center;
         padding: 20px 0;
      }

      .register-card {
         background: white;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         overflow: hidden;
      }

      .register-header {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: white;
         padding: 40px;
         text-align: center;
      }

      .register-body {
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

      .btn-register {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         border: none;
         border-radius: 10px;
         padding: 12px;
         font-weight: 600;
         width: 100%;
      }

      .btn-register:hover {
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-md-8 col-lg-6">
            <div class="register-card">
               <div class="register-header">
                  <i class="fas fa-baby fa-3x mb-3"></i>
                  <h3>Monitoring Ibu Hamil</h3>
                  <p class="mb-0">Daftar akun baru</p>
               </div>

               <div class="register-body">
                  @if($errors->any())
                  <div class="alert alert-danger">
                     <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                     </ul>
                  </div>
                  @endif

                  <form method="POST" action="{{ route('register') }}">
                     @csrf
                     <div class="row">
                        <div class="col-md-12 mb-3">
                           <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                           <div class="input-group">
                              <span class="input-group-text"><i class="fas fa-user"></i></span>
                              <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                           </div>
                        </div>

                        <div class="col-md-12 mb-3">
                           <label for="email" class="form-label">Email</label>
                           <div class="input-group">
                              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                           </div>
                        </div>

                        <div class="col-md-6 mb-3">
                           <label for="password" class="form-label">Password</label>
                           <div class="input-group">
                              <span class="input-group-text"><i class="fas fa-lock"></i></span>
                              <input type="password" class="form-control" id="password" name="password" required>
                           </div>
                        </div>

                        <div class="col-md-6 mb-3">
                           <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                           <div class="input-group">
                              <span class="input-group-text"><i class="fas fa-lock"></i></span>
                              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                           </div>
                        </div>
                     </div>

                     <button type="submit" class="btn btn-primary btn-register">
                        <i class="fas fa-user-plus me-2"></i> Daftar
                     </button>
                  </form>

                  <div class="text-center mt-3">
                     <p class="mb-0">Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-decoration-none">Login disini</a>
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