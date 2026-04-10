<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header-table { width: 100%; border-bottom: 2px solid #003366; margin-bottom: 20px; }
        .logo { width: 70px; }
        .title { text-align: right; color: #003366; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background-color: #003366; color: white; padding: 8px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 8px; vertical-align: top; word-wrap: break-word; }
        
        .col-id { width: 35px; }
        .col-foto { width: 75px; }
        .col-detalle { width: 150px; }
        .col-lugar { width: 100px; }
        .col-desc { width: 180px; } 
        .col-ci { width: 70px; }
        .col-estado { width: 65px; }

        .img-reporte { width: 60px; height: 60px; border-radius: 4px; }
        .footer { text-align: center; font-size: 9px; margin-top: 30px; color: #777; }
        .date-text { color: #003366; font-weight: bold; font-size: 8px; }
    </style>
</head>
<body>
    <?php
    function imgToBase64($path) {
        if (!file_exists($path)) return ''; 
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    $base_path = $_SERVER['DOCUMENT_ROOT'] . '/gestion_incidencias/';
    $logo_base64 = imgToBase64($base_path . 'assets/img/logo_uagrm.png');
    ?>

    <table class="header-table">
        <tr>
            <td>
                <?php if ($logo_base64): ?>
                    <img src="<?= $logo_base64 ?>" class="logo">
                <?php endif; ?>
            </td>
            <td class="title">
                <h2 style="margin:0;">REPORTE GENERAL DE INCIDENCIAS</h2>
                <p style="margin:5px 0;">Universidad Autónoma Gabriel René Moreno</p>
                <small>Generado: <?= date('d/m/Y H:i') ?></small>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-foto">Foto</th>
                <th class="col-detalle">Incidente</th>
                <th class="col-lugar">Ubicación</th>
                <th class="col-desc">Descripción</th> 
                <th class="col-ci">Denunciante</th>
                <th class="col-estado">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($reportes as $r): ?>
            <?php 
                $foto_path = $base_path . 'uploads/' . $r['foto'];
                $foto_base64 = imgToBase64($foto_path);
            ?>
            <tr>
                <td>#<?= $r['id_reportaje'] ?></td>
                <td>
                    <?php if ($foto_base64): ?>
                        <img src="<?= $foto_base64 ?>" class="img-reporte">
                    <?php else: ?>
                        <div style="width:60px;height:60px;background:#eee;font-size:8px;text-align:center;">Sin foto</div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= $r['titulo'] ?></strong><br>
                    <span class="date-text">Reportado: <?= date('d/m/Y', strtotime($r['fecha_creacion'])) ?></span>
                </td>
                <td><?= $r['nombre_lugar'] ?></td>
                <td>
                    <small><?= $r['descripcion'] ?? 'Sin descripción adicional' ?></small>
                </td>
                <td>CI: <?= $r['denunciante'] ?></td>
                <td><strong><?= $r['estado_nombre'] ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento es un reporte oficial generado por el Software de Gestión de Incidencias UAGRM.</p>
    </div>
</body>
</html>