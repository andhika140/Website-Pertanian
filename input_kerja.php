<?php
include 'koneksi.php';
$pesan = "";

if (isset($_POST['kirim_laporan'])) {
    $id_pegawai  = $_POST['id_pegawai'];
    $id_lahan    = $_POST['id_lahan'];
    $tindakan    = $_POST['tindakan'];
    
    $info_pegawai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_pegawai FROM tabel_pegawai WHERE id_pegawai='$id_pegawai'"));
    $nama_p = $info_pegawai['nama_pegawai'];
    $ket_baru = "Dirawat melalui $tindakan oleh $nama_p pada " . date('H:i') . " WIB";

    $simpan_log = mysqli_query($koneksi, "INSERT INTO tabel_log_aktivitas (id_pegawai, id_lahan, tindakan, status_gps) VALUES ('$id_pegawai', '$id_lahan', '$tindakan', '📍 Akurat (Desa Tugumukti)')");
    $update_lahan = mysqli_query($koneksi, "UPDATE tabel_lahan SET status_terakhir='sudah', waktu_terakhir_update=NOW(), keterangan_info='$ket_baru' WHERE id_lahan='$id_lahan'");

    if ($simpan_log && $update_lahan) {
        $pesan = "<div style='background:#dcfce7; color:#166534; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center;'>✅ Laporan hari ini telah tersimpan! Peta telah di update.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERTAN-GIS | Portal Pegawai</title>
    <style>
        body { background: #f8fafc; font-family: sans-serif; padding: 2rem 1rem; }
        .card { max-width: 420px; margin: 0 auto; background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-top: 6px solid #10b981; }
        h2 { text-align: center; color: #0f172a; margin-bottom: 1.5rem; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 14px; color: #334155; }
        select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; }
        button { width: 100%; padding: 14px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background: #059669; }
        .back { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>🌱 Portal Laporan Pegawai</h2>
        <?= $pesan ?>
        <form action="" method="POST">
            <label>Nama Pegawai:</label>
            <select name="id_pegawai" required>
                <option value="">-- Pilih Nama Anda --</option>
                <?php
                $q = mysqli_query($koneksi, "SELECT * FROM tabel_pegawai WHERE role='pegawai'");
                while($p = mysqli_fetch_assoc($q)) { echo "<option value='".$p['id_pegawai']."'>".$p['nama_pegawai']."</option>"; }
                ?>
            </select>

            <label>Petak Lahan:</label>
            <select name="id_lahan" required>
                <option value="">-- Pilih Lahan --</option>
                <?php
                $q2 = mysqli_query($koneksi, "SELECT * FROM tabel_lahan");
                while($l = mysqli_fetch_assoc($q2)) { echo "<option value='".$l['id_lahan']."'>".$l['nama_lahan']." (".$l['komoditas'].")</option>"; }
                ?>
            </select>

            <label>Aktiviti yang Dilakukan:</label>
            <select name="tindakan" required>
                <option value="">-- Pilih Tindakan --</option>
                <option value="Penyiraman Rutin">💦 Penyiraman Rutin</option>
                <option value="Pemupukan Organik">🌿 Pemupukan Organik</option>
                <option value="Penyemprotan Hama">🛡️ Penyemprotan Hama</option>
            </select>

            <button type="submit" name="kirim_laporan">🚀 SIMPAN LAPORAN</button>
        </form>
        <a href="index.php" class="back">⬅️ Kembali ke Dashboard Utama</a>
    </div>
</body>
</html>
