<?php date_default_timezone_set('Asia/Jakarta'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card p-4">
                    <h3 class="text-center mb-4">Form Buku Tamu</h3>
                    <form action="proses_buku_tamu.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_tamu" class="form-label">Nama Tamu</label>
                                <input type="text" class="form-control" id="nama_tamu" name="nama_tamu" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="form-label">Alamat Tamu</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" required />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nomer_hp" class="form-label">Nomer Hp</label>
                            <input type="text" class="form-control" id="nomer_hp" name="nomer_hp" required />
                        </div>

                        <div class="mb-3">
                            <label for="nama_tujuan" class="form-label">Nama Yang Dituju</label>
                            <input type="text" class="form-control" id="nama_tujuan" name="nama_tujuan" required />
                        </div>

                        <div class="mb-3">
                            <label for="acara" class="form-label">Keperluan</label>
                            <input type="text" class="form-control" id="keperluan" name="keperluan" required />
                        </div>

                        <input type="hidden" name="tanggal" value="<?= date('Y-m-d H:i:s') ?>" />

                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto Diri</label><br />
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('foto').click();">
                                <i class="bi bi-camera me-1"></i> Buka Kamera
                            </button>
                            <input type="file" class="form-control d-none" id="foto" name="foto" accept="image/*" capture="user" required />
                            <small class="text-muted d-block mt-2">Gunakan kamera untuk langsung mengambil foto.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alert saat file dipilih -->
    <script>
        document.getElementById('foto').addEventListener('change', function () {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                alert('Foto dipilih: ' + fileName);
            }
        });
    </script>

    <!-- SweetAlert untuk notifikasi -->
    <?php if (isset($_GET['status'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                Swal.fire({
                    icon: "<?= $_GET['status'] === 'success' ? 'success' : 'error' ?>",
                    title: "<?= $_GET['message']; ?>",
                    showConfirmButton: <?= $_GET['status'] === 'success' ? 'false' : 'true' ?>,
                    timer: <?= $_GET['status'] === 'success' ? '1500' : 'null' ?>
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
