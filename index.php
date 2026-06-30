<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BERTAN-GIS | Dashboard Monitoring Lahan</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root { --primary: #0f172a; --accent: #10b981; --success: #2ecc71; --danger: #e74c3c; --bg: #f8fafc; --text: #334155; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: var(--bg); color: var(--text); padding-bottom: 3rem; }
        header { background: linear-gradient(135deg, #1e293b, #0f172a); color: white; padding: 1.2rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .brand { font-size: 1.4rem; font-weight: 800; color: #38bdf8; }
        .user-panel { font-size: 0.9rem; background: rgba(255,255,255,0.1); padding: 0.4rem 1rem; border-radius: 20px; }
        .container { max-width: 1250px; margin: 2rem auto; padding: 0 1.5rem; }
        .hero { background: white; padding: 1.8rem 2rem; border-radius: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; border-left: 6px solid var(--accent); flex-wrap: wrap; gap: 1rem; }
        .hero h1 { font-size: 1.6rem; color: var(--primary); }
        .hero p { color: #64748b; font-size: 0.95rem; }
        .btn { padding: 0.7rem 1.4rem; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.9rem; display: inline-block; }
        .btn-pegawai { background-color: var(--accent); color: white; }
        .map-section { background: white; padding: 1.5rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 2.5rem; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; }
        .section-title { font-size: 1.25rem; font-weight: 700; color: var(--primary); }
        .legend { display: flex; gap: 15px; font-size: 0.85rem; font-weight: 600; }
        .dot { width: 12px; height: 12px; border-radius: 3px; display: inline-block; }
        #map { width: 100%; height: 520px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .grid-lahan { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem; }
        .card-lahan { background: white; padding: 1.3rem; border-radius: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border-top: 5px solid #cbd5e1; }
        .card-lahan.aman { border-top-color: var(--success); }
        .card-lahan.kritis { border-top-color: var(--danger); background: #fffcfc; }
        .status-pill { padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; margin: 8px 0; }
        .status-pill.aman { background: #dcfce7; color: #15803d; }
        .status-pill.kritis { background: #fee2e2; color: #b91c1c; }
        .table-section { background: white; padding: 1.5rem; border-radius: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 0.8rem; }
        th { background-color: #f1f5f9; padding: 0.85rem 1rem; font-size: 0.85rem; color: #475569; text-align: left; }
        td { padding: 0.9rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header>
        <div class="brand">🌱 CV BERKAH TANI</div>
        <div class="user-panel"> <strong></strong></div>
    </header>
    <div class="container">
        <div class="hero">
            <div>
                <h1>Sistem Pemetaan Lahan Perkebunan Sayuran</h1>
                <p>Monitoring Real-Time Penyiraman & Pemupukan 5 Lahan — CV. Berkah Tani</p>
            </div>
            <div>
                <a href="input_kerja.php" class="btn btn-pegawai">📱 Portal Input Pegawai</a>
            </div>
        </div>

        <div class="map-section">
            <div class="section-header">
                <div class="section-title">🗺️ Visualisasi Lahan Perkebunan  (Desa Tugumukti)</div>
                <div class="legend">
                    <div><span class="dot" style="background: var(--success);"></span> Sudah Dirawat</div>
                    <div><span class="dot" style="background: var(--danger);"></span> Belum Dirawat</div>
                </div>
            </div>
            <div id="map"></div>
        </div>

        <div class="section-title" style="margin-bottom: 1rem;">📊 Status Pemeliharaan Hari Ini</div>
        <div class="grid-lahan">
            <?php
            $q_lahan = mysqli_query($koneksi, "SELECT * FROM tabel_lahan");
            while($l = mysqli_fetch_assoc($q_lahan)) {
                $status_class = $l['status_terakhir'] == 'sudah' ? 'aman' : 'kritis';
                $teks_status  = $l['status_terakhir'] == 'sudah' ? 'SUDAH DIRAWAT' : 'BELUM ADA TINDAKAN!';
                echo "
                <div class='card-lahan $status_class'>
                    <h3>".$l['nama_lahan']."</h3>
                    <span class='status-pill $status_class'>$teks_status</span>
                    <p style='font-size:13px; color:#64748b;'>Komoditas: <b>".$l['komoditas']."</b></p>
                    <p style='font-size:12px; margin-top:8px;'>".$l['keterangan_info']."</p>
                </div>";
            }
            ?>
        </div>

        <div class="table-section">
            <div class="section-title">📋 Riwayat Aktivitas Terkini</div>
            <table>
                <thead>
                    <tr><th>Waktu</th><th>Nama Pegawai</th><th>Lahan</th><th>Tindakan</th><th>Status GPS</th></tr>
                </thead>
                <tbody>
                    <?php
                    $q_log = mysqli_query($koneksi, "SELECT * FROM tabel_log_aktivitas log JOIN tabel_pegawai p ON log.id_pegawai=p.id_pegawai JOIN tabel_lahan l ON log.id_lahan=l.id_lahan ORDER BY id_log DESC");
                    while($log = mysqli_fetch_assoc($q_log)) {
                        echo "<tr>
                            <td>".$log['waktu_aktivitas']."</td>
                            <td><b>".$log['nama_pegawai']."</b></td>
                            <td>".$log['nama_lahan']."</td>
                            <td>".$log['tindakan']."</td>
                            <td>".$log['status_gps']."</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([-6.814000, 107.545000], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        fetch('get_data_lahan.php')
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    style: f => ({ color: f.properties.status === 'sudah' ? '#2ecc71' : '#e74c3c', weight: 3, fillOpacity: 0.6 }),
                    onEachFeature: (f, l) => l.bindPopup(`<b>${f.properties.nama}</b><br>Status: ${f.properties.status.toUpperCase()}<br>${f.properties.info}`)
                }).addTo(map);
            });
    </script>
</body>
</html>
