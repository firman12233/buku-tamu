<?php date_default_timezone_set('Asia/Jakarta'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buku Tamu Digital</title>
    <link rel="icon" href="logo-smkn1slawi1.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }

        #preview {
            display: none;
            width: 150px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        input:focus, textarea:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .school-header img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .school-header h4 {
            margin-bottom: 0;
            font-weight: bold;
        }

        .school-header p {
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5 fade-in">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0">
                <div class="card-body p-5">

                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                        <img src="logo-smkn1slawi1.png" alt="Logo Sekolah" style="width: 70px; height: 70px; object-fit: contain; border-radius: 12px;">
                        <div class="text-start">
                            <h5 class="mb-1 fw-bold text-primary">SMK N 1 SLAWI</h5>
                            <small class="text-muted">Jl. H. Agus Salim, Slawi, Kab. Tegal, Jawa Tengah</small>
                        </div>
                    </div>


                    <h3 class="text-center text-primary mb-4">
                        <i class="bi bi-pencil-square me-2"></i>Form Buku Tamu Digital
                    </h3>

                    <form id="bukuTamuForm" action="proses_buku_tamu.php" method="POST" enctype="multipart/form-data" novalidate>

                        <div class="mb-3">
                            <label for="nama_tamu" class="form-label">Nama Tamu</label>
                            <input type="text" class="form-control" id="nama_tamu" name="nama_tamu" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label for="nomer_hp" class="form-label">Nomor HP</label>
                            <input type="text" class="form-control" id="nomer_hp" name="nomer_hp" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="mb-3">
                            <label for="asal_instansi" class="form-label">Asal Instansi</label>
                            <input type="text" class="form-control" id="asal_instansi" name="asal_instansi" placeholder="Contoh: SMAN 1 / PT. XYZ" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_tujuan" class="form-label">Nama yang Dituju</label>
                            <input type="text" class="form-control" id="nama_tujuan" name="nama_tujuan" placeholder="Contoh: Bapak Andi" required>
                        </div>

                        <div class="mb-3">
                            <label for="keperluan" class="form-label">Keperluan</label>
                            <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Tulis keperluan Anda" required></textarea>
                        </div>

                        <input type="hidden" name="tanggal" value="<?= date('Y-m-d H:i:s') ?>">

                        <div class="mb-3">
                            <label class="form-label">Foto Diri</label><br>
                            <button type="button" class="btn btn-outline-primary rounded-pill mb-2" onclick="document.getElementById('foto').click();">
                                <i class="bi bi-camera me-1"></i> Ambil Foto
                            </button>
                            <input type="file" class="form-control d-none" id="foto" name="foto" accept="image/*" capture="user" required>
                            <img id="preview" alt="Preview Foto">
                            <small class="text-muted d-block mt-2">Gunakan kamera untuk langsung mengambil foto.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            <i class="bi bi-send me-1"></i> Kirim Data
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- NOmer JS -->
<script>
  document.getElementById('nomer_hp').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });
</script>
<!-- Preview Foto -->
<script>
    document.getElementById('foto').addEventListener('change', function () {
        const file = this.files[0];
        const preview = document.getElementById('preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
    // Validasi Form Sebelum Submit
    document.getElementById('bukuTamuForm').addEventListener('submit', function (e) {
        const form = e.target;
        const fields = ['nama_tamu', 'alamat', 'nomer_hp', 'asal_instansi', 'nama_tujuan', 'keperluan'];
        let valid = true;

        fields.forEach(id => {
            if (!form[id].value.trim()) {
                valid = false;
            }
        });

        const foto = form.foto.files.length > 0;
        if (!foto) valid = false;

        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Lengkapi Semua Kolom!',
                text: 'Harap isi seluruh data dan unggah foto terlebih dahulu.'
            });
        }
    });
</script>
<!-- SweetAlert Notifikasi dari URL -->
<?php if (isset($_GET['status'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        Swal.fire({
            icon: "<?= $_GET['status'] === 'success' ? 'success' : 'error' ?>",
            title: "<?= htmlspecialchars($_GET['message']); ?>",
            showConfirmButton: <?= $_GET['status'] === 'success' ? 'false' : 'true' ?>,
            timer: <?= $_GET['status'] === 'success' ? '2000' : 'null' ?>
        });
    });
</script>
<?php endif; ?>

</body>
</html>
