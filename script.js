// 1. Inisialisasi Peta Pusat Lahan Desa Tugumukti
var map = L.map('map').setView([-6.814000, 107.545000], 17);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// 2. Ambil data spasial secara Real-Time dari get_data_lahan.php
fetch('get_data_lahan.php')
    .then(response => response.json())
    .then(data => {
        L.geoJSON(data, {
            style: function(feature) {
                // Warna poligon berubah otomatis: Hijau jika 'sudah', Merah jika 'belum'
                return {
                    color: feature.properties.status === 'sudah' ? '#2ecc71' : '#e74c3c',
                    weight: 3,
                    fillOpacity: 0.55
                };
            },
            onEachFeature: function(feature, layer) {
                // Tampilkan isi data dari database saat poligon lahan diklik
                layer.bindPopup(`
                    <div style="font-family: Arial, sans-serif; font-size: 12px;">
                        <strong style="font-size: 14px; color: #1e293b;">${feature.properties.nama}</strong><br>
                        <hr style="margin: 5px 0; border: 0; border-top: 1px solid #e2e8f0;">
                        <b>Komoditas:</b> ${feature.properties.komoditas}<br>
                        <b>Status Perawatan:</b> <span style="color: ${feature.properties.status === 'sudah' ? '#15803d' : '#b91c1c'}; font-weight: bold;">${feature.properties.status.toUpperCase()}</span><br>
                        <b>Keterangan:</b> ${feature.properties.info}<br>
                        <small style="color: #64748b;">Pembaruan: ${feature.properties.waktu || '-'}</small>
                    </div>
                `);
            }
        }).addTo(map);
    })
    .catch(error => console.error('Gagal memuat data spasial database:', error));
