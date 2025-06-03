<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

// Format tanggal untuk tabel
function formatTanggalPendek($datetime) {
    return date('d/m/Y', strtotime($datetime));
}

// Format tanggal untuk modal detail
function formatTanggalIndo($datetime) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    $timestamp = strtotime($datetime);
    $hariNama = $hari[date('l', $timestamp)];
    $tgl = date('d', $timestamp);
    $bln = $bulan[(int)date('m', $timestamp)];
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    return "$hariNama, $tgl $bln $tahun $jam";
}

$sql = "SELECT * FROM tamu ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Data Buku Tamu</title>
    <link rel="icon" href="logo-smkn1slawi.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

    <style>
        body { background-color: #f8f9fa; }
        .table-primary { background-color: #0d6efd !important; color: white !important; }
        .table-hover tbody tr:hover { background-color: #cfe2ff !important; cursor: pointer; }
        .modal-content { border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); border: none; }
        .modal-header { background-color: #0d6efd; color: white; }
        .btn-close { filter: invert(1); }
        .modal-body > div.mt-4 { background-color: #e9ecef; padding: 8px; border-radius: 4px; }
    </style>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="bi bi-journal-text me-2"></i>Admin Buku Tamu</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item me-3 text-white fw-semibold">
          <i class="bi bi-person-circle me-1"></i>Halo, <?= htmlspecialchars($_SESSION['admin_username']); ?>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- BUTTON DOWNLOAD -->
<div class="container mt-4">
  <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#downloadModal">
      <i class="bi bi-download me-1"></i> Download Data Tamu
    </button>
  </div>

  <h2 class="mb-4">Data Tamu</h2>

  <?php if ($result && $result->num_rows > 0): ?>
  <div class="table-responsive">
    <table id="dataTamu" class="table table-bordered table-hover align-middle">
      <thead class="table-primary">
        <tr class="text-center">
          <th>#</th>
          <th>Tanggal</th>
          <th>Nama Tamu</th>
          <th>Asal Instansi</th>
          <th>Nama Yang Dituju</th>
          <th>Detail</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while ($row = $result->fetch_assoc()): ?>
        <tr 
          data-nama="<?= htmlspecialchars($row['nama_tamu']) ?>"
          data-alamat="<?= htmlspecialchars($row['alamat']) ?>"
          data-nomer_hp="<?= htmlspecialchars($row['nomer_hp']) ?>"
          data-asal_instansi="<?= htmlspecialchars($row['asal_instansi']) ?>"
          data-nama_tujuan="<?= htmlspecialchars($row['nama_tujuan']) ?>"
          data-keperluan="<?= htmlspecialchars($row['keperluan']) ?>"
          data-tanggal="<?= formatTanggalIndo($row['tanggal']) ?>"
          data-foto="<?= htmlspecialchars($row['foto']) ?>"
        >
          <td class="text-center"><?= $no++ ?></td>
          <td class="text-center"><?= formatTanggalPendek($row['tanggal']) ?></td>
          <td><?= htmlspecialchars($row['nama_tamu']) ?></td>
          <td><?= htmlspecialchars($row['asal_instansi']) ?></td>
          <td><?= htmlspecialchars($row['nama_tujuan']) ?></td>
          <td class="text-center">
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="setDetail(this.closest('tr'))">
              Lihat Detail
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div class="alert alert-warning">Belum ada data tamu.</div>
  <?php endif; ?>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title w-100 text-center">TAMU</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="fotoTamu" class="img-fluid rounded mb-3" style="max-height: 400px;">
        <h5 id="modalNama" class="mb-3"></h5>
        <div class="mb-2"><strong>Alamat:</strong><div id="modalAlamat"></div></div>
        <div class="mb-2"><strong>Nomor Kontak:</strong><div id="modalNomerHp"></div></div>
        <div class="mb-2"><strong>Asal Instansi:</strong><div id="modalAsalInstansi"></div></div>
        <div class="mb-2"><strong>Nama Yang Dituju:</strong><div id="modalNamaTujuan"></div></div>
        <div class="mb-2"><strong>Keperluan:</strong><div id="modalKeperluan"></div></div>
        <div class="mt-4"><span id="modalTanggal" class="small"></span></div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DOWNLOAD -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="downloadModalLabel">
          <i class="bi bi-calendar-range me-2"></i>Unduh Data Tamu
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="download_tamu.php" method="get" target="_blank">
          <div class="mb-3">
            <label for="tanggal_awal_modal" class="form-label">Dari Tanggal</label>
            <input type="date" class="form-control" id="tanggal_awal_modal" name="tanggal_awal" required>
          </div>
          <div class="mb-3">
            <label for="tanggal_akhir_modal" class="form-label">Sampai Tanggal</label>
            <input type="date" class="form-control" id="tanggal_akhir_modal" name="tanggal_akhir" required>
          </div>
          <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-file-earmark-arrow-down me-1"></i> Download File
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
 
    <script>
  function setDetail(tr) {
    document.getElementById('modalNama').textContent = tr.dataset.nama;
    document.getElementById('modalAlamat').textContent = tr.dataset.alamat;
    document.getElementById('modalNomerHp').textContent = tr.dataset.nomer_hp;
    document.getElementById('modalAsalInstansi').textContent = tr.dataset.asal_instansi;
    document.getElementById('modalNamaTujuan').textContent = tr.dataset.nama_tujuan;
    document.getElementById('modalKeperluan').textContent = tr.dataset.keperluan;
    document.getElementById('modalTanggal').textContent = tr.dataset.tanggal;
    document.getElementById('fotoTamu').src = 'uploads/' + tr.dataset.foto;
  }

  document.getElementById('downloadModal').addEventListener('shown.bs.modal', () => {
    document.getElementById('tanggal_awal_modal').focus();
  });

  $(document).ready(function () {
    $('#dataTamu').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
      },
      "pageLength": 25
    });
  });
</script>

</body>
</html>

<?php $conn->close(); ?>
