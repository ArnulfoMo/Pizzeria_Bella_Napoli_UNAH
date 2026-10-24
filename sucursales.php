<?php

require 'datos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reiniciar'])) {
    unset($_SESSION['pedidos']);
    $_SESSION['aviso_sucursales'] = 'Las órdenes de la sesión fueron eliminadas.';
    header('Location: sucursales.php');
    exit;
}

// Mensaje de confirmacion (se muestra una sola vez)
$aviso = $_SESSION['aviso_sucursales'] ?? '';
unset($_SESSION['aviso_sucursales']);

// Sucursales 
$sucursales = [
    [
        'nombre'    => 'Tegucigalpa',
        'direccion' => 'Bulevar Morazán, frente a Plaza Morazán',
        'ciudad'    => 'Tegucigalpa, Francisco Morazán',
        'telefono'  => '2220-0001',
        'horario'   => 'Lunes a domingo: 11:00 a.m. – 10:00 p.m.',
    ],
    [
        'nombre'    => 'San Pedro Sula',
        'direccion' => 'Barrio Guamilito, 3.ª avenida, 7.ª calle',
        'ciudad'    => 'San Pedro Sula, Cortés',
        'telefono'  => '2550-0002',
        'horario'   => 'Lunes a domingo: 11:00 a.m. – 10:00 p.m.',
    ],
    [
        'nombre'    => 'La Ceiba',
        'direccion' => 'Avenida San Isidro, a una cuadra del parque central',
        'ciudad'    => 'La Ceiba, Atlántida',
        'telefono'  => '2440-0003',
        'horario'   => 'Lunes a domingo: 11:00 a.m. – 9:30 p.m.',
    ],
];

// Contacto general 
$contacto = [
    'correo'   => 'contacto@bellanapoli.hn',
    'whatsapp' => '9999-0000',
    'redes'    => 'Instagram · Facebook · TikTok: @bellanapoli',
];

// Ordenes guardadas en la sesion 
$pedidos = $_SESSION['pedidos'] ?? [];

include 'includes/header.php';
?>

<section class="suc-encabezado">
    <div class="container">
        <h1>Sucursales y órdenes</h1>
        <p>Encuéntranos en tres ciudades de Honduras y revisa los pedidos registrados en esta sesión.</p>
    </div>
</section>

<!-- ================= SUCURSALES ================= -->
<section class="suc-seccion">
    <div class="container">
        <h2 class="suc-titulo">Nuestras sucursales</h2>

        <div class="row g-4">
            <?php foreach ($sucursales as $sucursal): ?>
                <div class="col-lg-4 col-md-6">
                    <article class="suc-card h-100">
                        <h3><?= limpiar($sucursal['nombre']) ?></h3>
                        <p class="suc-ciudad"><?= limpiar($sucursal['ciudad']) ?></p>

                        <dl class="suc-datos">
                            <dt>Dirección</dt>
                            <dd><?= limpiar($sucursal['direccion']) ?></dd>

                            <dt>Teléfono</dt>
                            <dd><?= limpiar($sucursal['telefono']) ?></dd>

                            <dt>Horario</dt>
                            <dd><?= limpiar($sucursal['horario']) ?></dd>
                        </dl>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Contacto general -->
        <div class="suc-contacto mt-5">
            <h3>Contacto</h3>
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Correo</strong>
                    <p><?= limpiar($contacto['correo']) ?></p>
                </div>
                <div class="col-md-4">
                    <strong>WhatsApp</strong>
                    <p><?= limpiar($contacto['whatsapp']) ?></p>
                </div>
                <div class="col-md-4">
                    <strong>Redes sociales</strong>
                    <p><?= limpiar($contacto['redes']) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= PANEL DE ORDENES ================= -->
<section class="suc-seccion suc-seccion-crema">
    <div class="container">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="suc-titulo mb-0">Órdenes de la sesión</h2>

            <form method="post" action="sucursales.php"
                onsubmit="return confirm('¿Seguro que quieres vaciar todas las órdenes de la sesión?');">
                <button type="submit" name="reiniciar" value="1" class="btn btn-reiniciar"
                    <?= empty($pedidos) ? 'disabled' : '' ?>>
                    Vaciar / Reiniciar sesión
                </button>
            </form>
        </div>

        <?php if ($aviso !== ''): ?>
            <div class="alert alert-success" role="alert"><?= limpiar($aviso) ?></div>
        <?php endif; ?>

        <?php if (empty($pedidos)): ?>

            <div class="suc-vacio">
                <span class="badge bg-warning text-dark mb-3">Sin órdenes</span>
                <p class="mb-3">Todavía no hay pedidos registrados en esta sesión.</p>
                <a href="pedido.php" class="btn btn-ordenar">Hacer un pedido</a>
            </div>

        <?php else: ?>

            <div class="table-responsive suc-tabla-caja">
                <table class="table table-hover align-middle mb-0 suc-tabla">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Detalle de pizzas</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $orden): ?>
                            <?php
                            
                            $detalle = $orden['detalle'] ?? '';
                            if (is_array($detalle)) {
                                $detalle = implode(', ', $detalle);
                            }
                            $estado = $orden['estado'] ?? 'Recibido';
                            ?>
                            <tr>
                                <td class="fw-bold"><?= limpiar($orden['folio'] ?? '—') ?></td>
                                <td><?= limpiar($orden['cliente'] ?? '—') ?></td>
                                <td><?= limpiar($orden['telefono'] ?? '—') ?></td>
                                <td><?= limpiar($detalle !== '' ? $detalle : '—') ?></td>
                                <td class="text-end fw-bold">
                                    <?= isset($orden['total']) ? dinero($orden['total']) : '—' ?>
                                </td>
                                <td><span class="badge suc-estado"><?= limpiar($estado) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="suc-nota mt-3">
                Total de órdenes en la sesión: <strong><?= count($pedidos) ?></strong>
            </p>

        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>