<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

// Format tanggal: Tabel (pendek)
function formatTanggalPendek($datetime) {
    return date('d/m/Y', strtotime($datetime));
}

// Format tanggal: Detail modal (panjang)
function formatTanggalIndo($datetime) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
       
        .table-primary {
            background-color: #0d6efd !important;
            color: white !important;
        }
    
        .table-hover tbody tr:hover {
            background-color: #cfe2ff !important;
            cursor: pointer;
        }
        .navbar-text.admin-greeting {
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            margin-right: 1rem;
            user-select: none;
        }
        .modal-content {
            background-color: #fff !important;
            color: #212529 !important;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            border: none;
        }
        .modal-header {
            background-color: #0d6efd !important;
            color: white !important;
            border-bottom: none;
            position: relative;
        }
        .btn-close {
            filter: invert(1);
        }

        .modal-body > div.mt-4 {
            background-color: #e9ecef !important;
            padding: 8px;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            .navbar-text.admin-greeting {
                font-size: 1rem;
                margin-right: 0;
                text-align: center;
                display: block;
                padding: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin Buku Tamu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarAdmin">
      <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">
        <li class="nav-item me-2">
          <span class="navbar-text admin-greeting">
            Halo, <?= htmlspecialchars($_SESSION['admin_username']); ?>
          </span>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
    <h2 class="mb-4">Data Tamu</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Nama Tamu</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center">Detail</th>
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
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm"
                                data-bs-toggle="modal" data-bs-target="#detailModal"
                                onclick="setDetail(this.closest('tr'))">
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title w-100 text-center fw-bold" id="detailModalLabel">TAMU</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" alt="Foto Tamu" id="fotoTamu" class="img-fluid rounded mb-3" style="max-width: 100%; max-height: 400px;">
        <h5 id="modalNama" class="mb-3"></h5>

        <div class="mb-2">
            <div class="fw-bold">Alamat :</div>
            <div id="modalAlamat"></div>
        </div>

        <div class="mb-2">
            <div class="fw-bold">Nomor Kontak :</div>
            <div id="modalNomerHp"></div>
        </div>

        <div class="mb-2">
            <div class="fw-bold">Asal Instansi :</div>
            <div id="modalNomerHp"></div>
        </div>

        <div class="mb-2">
            <div class="fw-bold">Nama Yang Dituju :</div>
            <div id="modalNamaTujuan"></div>
        </div>

        <div class="mb-2">
            <div class="fw-bold">Keperluan :</div>
            <div id="modalKeperluan"></div>
        </div>

        <div class="mt-4">
            <span id="modalTanggal" class="small"></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setDetail(tr) {
        const nama = tr.getAttribute('data-nama');
        const alamat = tr.getAttribute('data-alamat');
        const nomerHp = tr.getAttribute('data-nomer_hp');
        const asalInstansi = tr.getAttribute('data-asal_instansi');
        const namaTujuan = tr.getAttribute('data-nama_tujuan');
        const keperluan = tr.getAttribute('data-keperluan');
        const tanggal = tr.getAttribute('data-tanggal');
        const foto = tr.getAttribute('data-foto');

        document.getElementById('modalNama').textContent = nama;
        document.getElementById('modalAlamat').textContent = alamat;
        document.getElementById('modalNomerHp').textContent = nomerHp;
        document.getElementById('modalAsalInstansi').textContent = asal_instansi;
        document.getElementById('modalNamaTujuan').textContent = namaTujuan;
        document.getElementById('modalKeperluan').textContent = keperluan;
        document.getElementById('modalTanggal').textContent = tanggal;
        document.getElementById('fotoTamu').src = 'uploads/' + foto;
        document.getElementById('fotoTamu').alt = 'Foto ' + nama;
    }

    document.getElementById('detailModal').addEventListener('shown.bs.modal', () => {
        setTimeout(() => {
            const closeButton = document.querySelector('#detailModal .btn-close');
            if (closeButton) closeButton.focus();
        }, 100);
    });
</script>
</body>
</html>
<?php $conn->close(); ?>
