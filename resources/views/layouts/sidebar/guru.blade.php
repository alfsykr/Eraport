<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{ route('dashboard') }}" class="brand-link">
    <img src="/assets/dist/img/logo.png" alt="Logo" class="brand-image img-circle">
    <span class="brand-text font-weight-light">Aplikasi E-Raport</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        @if(Session::get('kurikulum') == '2013')

          <!-- Kurikulum 2013 - Guru Mapel -->
          <li class="nav-header">RAPORT K-2013</li>

          <li class="nav-item">
            <a href="{{ route('rencanakisi.index') }}" class="nav-link">
              <i class="nav-icon fas fa-list-alt"></i>
              <p>Rencana Penilaian</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('nilaikisi.index') }}" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>Input Nilai Kisi-kisi</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('kirimnilaiakhir.index') }}" class="nav-link">
              <i class="nav-icon fas fa-paper-plane"></i>
              <p>Kirim Nilai Akhir</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('statuspengiriman.index') }}" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Status Pengiriman Nilai</p>
            </a>
          </li>

          <!-- Wali Kelas Section (K13) - hanya tampil jika guru adalah wali kelas -->
          @php
            $guru_wk = \App\Guru::where('user_id', Auth::id())->first();
            $tapel_wk = \App\Tapel::find(session('tapel_id'));
            $is_walikelas = $guru_wk && $tapel_wk
              ? \App\Kelas::where('tapel_id', $tapel_wk->id)->where('guru_id', $guru_wk->id)->exists()
              : false;
          @endphp

          @if($is_walikelas)
            <li class="nav-header" style="color: #aad4f5;">WALI KELAS</li>

            <li class="nav-item">
              <a href="{{ route('kehadiran.index') }}" class="nav-link">
                <i class="nav-icon fas fa-user-check"></i>
                <p>Input Kehadiran Siswa</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('prestasi.index') }}" class="nav-link">
                <i class="nav-icon fas fa-star"></i>
                <p>Input Talents Mapping</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('catatan.index') }}" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
                <p>Catatan Wali Kelas</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('kenaikan.index') }}" class="nav-link">
                <i class="nav-icon fas fa-layer-group"></i>
                <p>Input Kenaikan Kelas</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('terapi.index') }}" class="nav-link">
                <i class="nav-icon fas fa-notes-medical"></i>
                <p>Perkembangan Terapi</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('personalprogram.index') }}" class="nav-link">
                <i class="nav-icon fas fa-user-edit"></i>
                <p>Personal Program</p>
              </a>
            </li>


          @endif
          <!-- End Wali Kelas Section K13 -->

        @endif

        <li class="nav-item bg-danger mt-2">
          <a href="{{ route('logout') }}" class="nav-link" onclick="return confirm('Apakah anda yakin ingin keluar ?')">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Keluar / Logout</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>