<?php date_default_timezone_set('Asia/Jakarta'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: 500;
        }

        #preview {
            margin-top: 10px;
            max-width: 200px;
            display: none;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
  <style>
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
    #preview {
      display: none;
      width: 150px;
      height: auto;
      margin-top: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
  </style>

  <div class="container mt-5 fade-in">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-5">
            <h3 class="text-center mb-4 text-primary">
              <i class="bi bi-pencil-square me-2"></i>Form Buku Tamu
            </h3>
            <form id="bukuTamuForm" action="proses_buku_tamu.php" method="POST" enctype="multipart/form-data" novalidate>

              <div class="mb-3">
                <label for="nama_tamu" class="form-label">Nama Tamu</label>
                <input type="text" class="form-control" id="nama_tamu" name="nama_tamu" placeholder="Masukkan nama Anda">
              </div>

              <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Tamu</label>
                <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat">
              </div>

              <div class="mb-3">
                <label for="nomer_hp" class="form-label">Nomor HP</label>
                <input type="text" class="form-control" id="nomer_hp" name="nomer_hp" placeholder="08xxxxxxxxxx">
              </div>

              <div class="mb-3">
                <label for="asal_instansi" class="form-label">Asal Instansi</label>
                <input type="text" class="form-control" id="asal_instansi" name="asal_instansi" placeholder="Contoh: SMAN 1 / PT. XYZ">
              </div>

              <div class="mb-3">
                <label for="nama_tujuan" class="form-label">Nama yang Dituju</label>
                <input type="text" class="form-control" id="nama_tujuan" name="nama_tujuan" placeholder="Contoh: Bapak Andi">
              </div>

              <div class="mb-3">
                <label for="keperluan" class="form-label">Keperluan</label>
                <input type="text" class="form-control" id="keperluan" name="keperluan" placeholder="Tulis keperluan Anda">
              </div>

              <input type="hidden" name="tanggal" value="<?= date('Y-m-d H:i:s') ?>">

              <div class="mb-3">
                <label for="foto" class="form-label">Foto Diri</label><br />
                <button type="button" class="btn btn-outline-primary rounded-pill mb-2" onclick="document.getElementById('foto').click();">
                  <i class="bi bi-camera me-1"></i> Buka Kamera
                </button>
                <input type="file" class="form-control d-none" id="foto" name="foto" accept="image/*" capture="user" onchange="previewFoto(event)">
                <img id="preview" src="#" alt="Preview Foto">
                <small class="text-muted d-block mt-2">Gunakan kamera untuk langsung mengambil foto.</small>
              </div>

              <button type="submit" class="btn btn-primary rounded-pill w-100 py-2">
                <i class="bi bi-send me-1"></i> Kirim
              </button>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Validasi dan Preview -->
<script>
    document.getElementById('bukuTamuForm').addEventListener('submit', function (e) {
        const form = e.target;

        const nama = form.nama_tamu.value.trim();
        const alamat = form.alamat.value.trim();
        const hp = form.nomer_hp.value.trim();
        const asal = form.asal_instansi.value.trim();
        const tujuan = form.nama_tujuan.value.trim();
        const keperluan = form.keperluan.value.trim();
        const fotoInput = form.foto;
        const foto = fotoInput && fotoInput.files && fotoInput.files.length > 0;

        if (!nama || !alamat || !hp || !asal || !tujuan || !keperluan || !foto) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Lengkapi Semua Kolom!',
                text: 'Pastikan semua kolom dan foto sudah terisi.'
            });
        }
    });

    // Preview foto
    document.getElementById('foto').addEventListener('change', function () {
        const preview = document.getElementById('preview');
        const file = this.files[0];

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
</script>
<script>
    function previewFoto(event) {
      const input = event.target;
      const preview = document.getElementById('preview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>

<!-- SweetAlert untuk feedback dari server -->
<?php if (isset($_GET['status'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Swal.fire({
                icon: "<?= $_GET['status'] === 'success' ? 'success' : 'error' ?>",
                title: "<?= htmlspecialchars($_GET['message']); ?>",
                showConfirmButton: <?= $_GET['status'] === 'success' ? 'false' : 'true' ?>,
                timer: <?= $_GET['status'] === 'success' ? '1500' : 'null' ?>
            });
        });
    </script>
<?php endif; ?>
</body>
</html>

