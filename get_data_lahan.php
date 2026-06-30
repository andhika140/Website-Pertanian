<?php
header('Content-Type: application/json');
include 'koneksi.php';

$query = "SELECT * FROM tabel_lahan";
$result = mysqli_query($koneksi, $query);

$features = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $coordinates = json_decode($row['koordinat_polygon']);

        $feature = array(
            'type' => 'Feature',
            'properties' => array(
                'id_lahan'  => $row['id_lahan'],
                'nama'      => $row['nama_lahan'],
                'komoditas' => $row['komoditas'],
                'status'    => $row['status_terakhir'],
                'info'      => $row['keterangan_info'],
                'waktu'     => $row['waktu_terakhir_update']
            ),
            'geometry' => array(
                'type' => 'Polygon',
                'coordinates' => $coordinates
            )
        );
        array_push($features, $feature);
    }
}

$geojson = array(
    'type' => 'FeatureCollection',
    'features' => $features
);

echo json_encode($geojson);
?>