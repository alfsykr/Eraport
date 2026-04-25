@include('layouts.auth.header')

<body class="hold-transition lockscreen">
  <div class="lockscreen-wrapper">
    <div class="error-content">
      <h1 class="headline text-danger text-center"> 401</h1>
      <h3 class="text-center"><i class="fas fa-exclamation-triangle text-danger"></i> Unauthorized.</h3>

      <p class="text-center">
        Maaf user tidak memiliki akses pada URL yang anda minta.
      </p>

      <div class="row mr-2 ml-2">
        <div class="col-12">
          <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block">Kembali Ke Dashboard</a>
        </div>
      </div>
    </div>

    <div class="lockscreen-footer text-center">
      Copyright &copy; 2026 | <a href="https://homeschoolingalam.sch.id/" target="_black">Homeschooling Alam Depok</a>.</strong>
    </div>
  </div>

  @include('layouts.auth.footer')