<?php require 'datos.php';
include 'includes/header.php'; ?>

<?php
if (!isset($_SESSION['pedidos'])) {
    $_SESSION['pedidos'] = [];
}

/*FORMULARIO*/
$nombre = '';
$rtn = '';
$telefono = '';
$correo = '';
$direccion = '';
$referencia = '';
$pizza = '';
if (isset($_GET['pizza'])) {
    $pizza = limpiar(trim((string)$_GET['pizza'])); 
}
$tamano = '';
$cantidadTexto = '1';
$modalidad = '';
$metodoPago = '';
$extrasSeleccionados = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*Sanitización*/
    $nombre = limpiar(trim((string) ($_POST['nombre'] ?? '')));
    $rtn = limpiar(trim((string) ($_POST['rtn'] ?? '')));
    $telefono = limpiar(trim((string) ($_POST['telefono'] ?? '')));
    $correo = limpiar(trim((string) ($_POST['correo'] ?? '')));
    $direccion = limpiar(trim((string) ($_POST['direccion'] ?? '')));
    $referencia = limpiar(trim((string) ($_POST['referencia'] ?? '')));
    $pizza = limpiar(trim((string) ($_POST['pizza'] ?? '')));
    $tamano = limpiar(trim((string) ($_POST['tamano'] ?? '')));
    $cantidadTexto = limpiar(trim((string) ($_POST['cantidad'] ?? '')));
    $modalidad = limpiar(trim((string) ($_POST['modalidad'] ?? '')));
    $metodoPago = limpiar(trim((string) ($_POST['metodo_pago'] ?? '')));

    $recibidos = $_POST['ingredientes'] ?? [];

    if (!is_array($recibidos)) {
        $recibidos = [];
    }
    foreach (array_slice($recibidos, 0, count($ingredientes)) as $extra) {
        if (is_string($extra)) {
            $extrasSeleccionados[] = limpiar(trim($extra));
        }
    }

    /*VALIDACIONES*/
    if ($nombre === '') {
        $errores[] = 'El nombre y apellido son obligatorios.';
    } elseif (mb_strlen($nombre, 'UTF-8') < 5) {
        $errores[] = 'El nombre debe tener al menos 5 caracteres.';
    }

    if ($rtn === '') {
        $errores[] = 'El RTN es obligatorio.';
    } elseif (!preg_match('/^[0-9]{14}$/', $rtn)) {
        $errores[] = 'El RTN debe contener exactamente 14 dígitos.';
    }

    if ($telefono === '') {
        $errores[] = 'El teléfono es obligatorio.';
    } elseif (!preg_match('/^[0-9]{8}$/', $telefono)) {
        $errores[] = 'El teléfono debe contener exactamente 8 dígitos.';
    }

    if ($correo === '') {
        $errores[] = 'El correo electrónico es obligatorio.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no tiene un formato válido.';
    }

    if ($direccion === '') {
        $errores[] = 'La dirección de entrega es obligatoria.';
    }

    if ($referencia === '') {
        $errores[] = 'El punto de referencia es obligatorio.';
    }

    if (!array_key_exists($pizza, $menu)) {
        $errores[] = 'Selecciona una pizza válida.';
    } elseif (!array_key_exists($tamano, $menu[$pizza])) {
        $errores[] = 'Selecciona un tamaño válido.';
    }

    if (
        !preg_match('/^[0-9]+$/', $cantidadTexto) ||
        (int) $cantidadTexto < 1 ||
        (int) $cantidadTexto > 100
    ) {
        $errores[] = 'La cantidad debe ser un número entero entre 1 y 100.';
    }

    $modalidadesPermitidas = [
        'restaurante',
        'pickup',
        'delivery'
    ];

    if (!in_array($modalidad, $modalidadesPermitidas, true)) {
        $errores[] = 'Selecciona una modalidad de entrega válida.';
    }

    $metodosPermitidos = ['efectivo', 'tarjeta', 'ach'];

    if (!in_array($metodoPago, $metodosPermitidos, true)) {
        $errores[] = 'Selecciona un método de pago válido.';
    }

    foreach ($extrasSeleccionados as $extra) {
        if (!array_key_exists($extra, $ingredientes)) {
            $errores[] = 'Uno de los ingredientes seleccionados no es válido.';
            break;
        }
    }

    /*calculos pedido*/

    if (empty($errores)) {
        $cantidad = (int) $cantidadTexto;
        $precioUnitario = $menu[$pizza][$tamano];
        $recargoIngredientes = 0;

        foreach ($extrasSeleccionados as $extra) {
            $recargoIngredientes += $ingredientes[$extra];
        }

        $subtotal = (
            $precioUnitario + $recargoIngredientes
        ) * $cantidad;

        $descuento = ($cantidad >= 3)
            ? $subtotal * 0.10
            : 0;

        $subtotalNeto = $subtotal - $descuento;

        $isv = $subtotalNeto * 0.15;

        $delivery = ($modalidad === 'delivery')
            ? 45.00
            : 0.00;

        $total = round($subtotalNeto + $isv + $delivery, 2);

        /*registro*/

        $_SESSION['ultimo_folio'] =
            ($_SESSION['ultimo_folio'] ?? 0) + 1;

        $folio = 'ORD-NAP-' . str_pad(
            (string) $_SESSION['ultimo_folio'],
            5,
            '0',
            STR_PAD_LEFT
        );

        $orden = [
            'folio' => $folio,
            'fecha' => date('d/m/Y H:i:s'),

            'cliente' => [
                'nombre' => $nombre,
                'rtn' => $rtn,
                'telefono' => $telefono,
                'correo' => $correo,
                'direccion' => $direccion,
                'referencia' => $referencia
            ],

            'producto' => [
                'pizza' => $pizza,
                'tamano' => $tamano,
                'cantidad' => $cantidad,
                'ingredientes' => $extrasSeleccionados,
                'precio_unitario' => $precioUnitario,
                'recargo_ingredientes' => $recargoIngredientes
            ],

            'modalidad' => $modalidad,
            'metodo_pago' => $metodoPago,

            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'subtotal_neto' => $subtotalNeto,
            'isv' => $isv,
            'delivery' => $delivery,
            'total' => $total
        ];

        $_SESSION['pedidos'][] = $orden;
    }
}
?>



<section class="section-pad section-cream">
    <div class="container">
        <div class="row g-4 align-items-start">

            <div class="col-12">
                <h2 class="fw-bold">Realiza tu pedido</h2>
                <p class="text-muted">Personaliza tu pizza, completa tus datos y confirma tu compra</p>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">

                        <h3 class="h4 fw-bold mb-4">
                            Información del pedido
                        </h3>

                        <!--VALIDACIONES SERVIDOR-->

                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger" role="alert">
                                <strong>Verifica los siguientes datos:</strong>

                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?= limpiar($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="pedido.php">

                            <!-- DATOS CLIENTE-->

                            <h4 class="h6 fw-bold text-uppercase mb-3">
                                Datos del cliente
                            </h4>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre y apellido</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" minlength="5" maxlength="120" 
                                value="<?= limpiar($nombre) ?>" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="rtn" class="form-label">RTN</label>
                                    <input type="text" class="form-control" id="rtn" name="rtn" inputmode="numeric" 
                                    pattern="[0-9]{14}" maxlength="14" value="<?= limpiar($rtn) ?>" placeholder="14 dígitos" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                    inputmode="numeric" pattern="[0-9]{8}" maxlength="8" value="<?= limpiar($telefono) ?>" placeholder="8 dígitos" required>
                                </div>
                            </div>

                            <div class="mt-3 mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?= limpiar($correo) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección de entrega</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2" required><?= limpiar($direccion) ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="referencia" class="form-label">Punto de referencia</label>

                                <input type="text" class="form-control" id="referencia" name="referencia" 
                                value="<?= limpiar($referencia) ?>" placeholder="Ej. Frente al parque" required >
                            </div>
                            <hr class="my-4">

                            <!--SELECCION-->

                            <h4 class="h6 fw-bold text-uppercase mb-3">Selección de pizza</h4>
                            <div class="mb-3">
                                <label for="pizza" class="form-label">Tipo de pizza</label>

                                <select class="form-select" id="pizza" name="pizza" required>
                                    <option value="">Selecciona una pizza</option>

                                    <?php foreach ($menu as $nombrePizza => $precios): ?>
                                        <option value="<?= limpiar($nombrePizza) ?>" <?= $pizza === $nombrePizza ? 'selected' : '' ?>>
                                            <?= limpiar($nombrePizza) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="tamano" class="form-label">Tamaño</label>

                                    <select class="form-select" id="tamano" name="tamano" required>
                                        <option value="">Selecciona un tamaño</option>

                                        <?php foreach (
                                            ['Pequeña', 'Mediana', 'Grande']
                                            as $opcionTamano
                                        ): ?>

                                            <option
                                                value="<?= limpiar($opcionTamano) ?>" <?= $tamano === $opcionTamano ? 'selected' : '' ?>>
                                                <?= limpiar($opcionTamano) ?>
                                            </option>
                                        <?php endforeach; ?>

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="cantidad" class="form-label">Cantidad</label>

                                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" 
                                    max="100" step="1" value="<?= limpiar($cantidadTexto) ?>" required>
                                </div>
                            </div>

                            <!-- INGREDIENTES -->

                            <div class="mt-4 mb-4">
                                <h4 class="h6 fw-bold">Ingredientes adicionales</h4>

                                <?php foreach ($ingredientes as $extra => $precio): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="ingredientes[]" 
                                        id="extra-<?= md5($extra) ?>" value="<?= limpiar($extra) ?>" 
                                        <?= in_array( $extra, $extrasSeleccionados, true ) ? 'checked' : '' ?> >

                                        <label class="form-check-label" for="extra-<?= md5($extra) ?>"> <?= limpiar($extra) ?>
                                            <span class="text-muted">
                                                (+<?= dinero($precio) ?> por pizza)
                                            </span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>

                            </div>

                            <hr class="my-4">

                            <!-- ENTREGA -->
                            <h4 class="h6 fw-bold text-uppercase mb-3">Entrega y pago</h4>

                            <div class="mb-3">
                                <label for="modalidad" class="form-label">Modalidad de entrega</label>
                                <select class="form-select" id="modalidad" name="modalidad" required>

                                    <option value="">
                                        Selecciona una modalidad
                                    </option>

                                    <option value="restaurante" <?= $modalidad === 'restaurante' ? 'selected' : '' ?>>
                                        Para comer en restaurante
                                    </option>

                                    <option value="pickup" <?= $modalidad === 'pickup' ? 'selected' : '' ?>>
                                        Para llevar (Pick-Up)
                                    </option>

                                    <option value="delivery" <?= $modalidad === 'delivery' ? 'selected' : '' ?>>
                                        Envío a domicilio (Delivery)
                                        (+ L. 45.00)
                                    </option>

                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="metodo_pago" class="form-label">
                                    Método de pago
                                </label>

                                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                    <option value="">
                                        Selecciona un método de pago
                                    </option>

                                    <option value="efectivo" <?= $metodoPago === 'efectivo' ? 'selected' : '' ?>>
                                        Efectivo
                                    </option>

                                    <option value="tarjeta" <?= $metodoPago === 'tarjeta' ? 'selected' : '' ?>>
                                        Tarjeta de crédito / débito
                                    </option>

                                    <option value="ach" <?= $metodoPago === 'ach' ? 'selected' : '' ?>>
                                        Transferencia ACH
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-rojo btn-lg w-100">
                                Confirmar pedido
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RESUMEN INFORMATIVO -->

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <span class="hero-text">RESUMEN</span>

                        <h3 class="h4 fw-bold mt-2">Tu próxima pizza favorita</h3>

                        <p class="text-muted">
                            Preparamos cada pedido con ingredientes
                            seleccionados y masa artesanal.
                        </p>
                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Descuento por volumen</span>
                            <strong>10 %</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>ISV configurado</span>
                            <strong>15 %</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Envío a domicilio</span>
                            <strong>L. 45.00</strong>
                        </div>

                        <div class="alert alert-warning small mb-0">
                            El descuento se aplica automáticamente
                            cuando solicitas tres o más pizzas.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- FACTURA DIGITAL -->

<?php if ($orden !== null): ?>

<section class="section-pad">
    <div class="container">
        <div class="card border-0 shadow rounded-4">

            <div class="card-header bg-success text-white p-4 rounded-top-4">
                <span class="small text-uppercase">
                    Bella Napoli
                </span>

                <h2 class="h3 fw-bold mb-2">
                    Comprobante del pedido
                </h2>

                <span class="badge bg-white text-success fs-6">
                    <?= limpiar($orden['folio']) ?>
                </span>
            </div>

            <div class="card-body p-4 p-lg-5">

                <div class="alert alert-success">

                    <strong>¡Pedido registrado correctamente!</strong>

                    <p class="mb-0">
                        Guarda el folio para futuras consultas.
                    </p>

                </div>

                <div class="row g-4 mb-4">

                    <div class="col-md-6">

                        <h3 class="h6 fw-bold">
                            DATOS DEL CLIENTE
                        </h3>

                        <p>
                            <strong>Nombre:</strong>
                            <?= limpiar($orden['cliente']['nombre']) ?>
                        </p>

                        <p>
                            <strong>RTN:</strong>
                            <?= limpiar($orden['cliente']['rtn']) ?>
                        </p>

                        <p>
                            <strong>Teléfono:</strong>
                            <?= limpiar($orden['cliente']['telefono']) ?>
                        </p>

                        <p>
                            <strong>Correo:</strong>
                            <?= limpiar($orden['cliente']['correo']) ?>
                        </p>

                        <p>
                            <strong>Dirección:</strong>
                            <?= limpiar($orden['cliente']['direccion']) ?>
                        </p>

                        <p>
                            <strong>Referencia:</strong>
                            <?= limpiar($orden['cliente']['referencia']) ?>
                        </p>

                    </div>

                    <div class="col-md-6">

                        <h3 class="h6 fw-bold">
                            DATOS DE FACTURACIÓN
                        </h3>

                        <p>
                            <strong>Folio:</strong>
                            <?= limpiar($orden['folio']) ?>
                        </p>

                        <p>
                            <strong>Fecha:</strong>
                            <?= limpiar($orden['fecha']) ?>
                        </p>

                        <p>
                            <strong>Modalidad:</strong>
                            <?= limpiar($orden['modalidad']) ?>
                        </p>

                        <p>
                            <strong>Método de pago:</strong>
                            <?= limpiar($orden['metodo_pago']) ?>
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Descripción</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio unitario</th>
                                <th class="text-end">Importe</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <strong>
                                        <?= limpiar($orden['producto']['pizza']) ?>
                                    </strong>
                                    <br>

                                    <span class="text-muted">
                                        Tamaño: <?= limpiar($orden['producto']['tamano']) ?>
                                    </span>

                                    <?php if (!empty($orden['producto']['ingredientes'])): ?>
                                        <br>
                                        <small>
                                            Extras:
                                            <?= limpiar(implode(', ', $orden['producto']['ingredientes'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?= (int) $orden['producto']['cantidad'] ?>
                                </td>

                                <td class="text-end">
                                    <?= dinero($orden['producto']['precio_unitario'] + $orden['producto']['recargo_ingredientes']) ?>
                                </td>

                                <td class="text-end">
                                    <?= dinero($orden['subtotal']) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- DESGLOSE -->

                <div class="row justify-content-end">
                    <div class="col-md-6 col-lg-5">

                        <div class="d-flex justify-content-between py-2">
                            <span>Subtotal bruto</span>
                            <strong>
                                <?= dinero($orden['subtotal']) ?>
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span>Descuento (10 %)</span>
                            <strong class="text-success">
                                - <?= dinero($orden['descuento']) ?>
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span>Subtotal neto</span>
                            <strong>
                                <?= dinero($orden['subtotal_neto']) ?>
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span>ISV (15 %)</span>
                            <strong>
                                <?= dinero($orden['isv']) ?>
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span>Delivery</span>
                            <strong>
                                <?= dinero($orden['delivery']) ?>
                            </strong>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="h5">TOTAL A PAGAR</strong>
                            <strong class="fs-4 text-success">
                                <?= dinero($orden['total']) ?>
                            </strong>
                        </div>
                    </div>
                </div>


                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                        Imprimir comprobante
                    </button>

                    <a href="pedido.php" class="btn btn-rojo">Nuevo pedido</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>


<?php include 'includes/footer.php'; ?>