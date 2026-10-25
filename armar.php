<?php 
require 'datos.php'; 
include 'includes/header.php'; 

$mensaje_alerta = '';
$exito = false;
$pizza_seleccionada = [];

$masa = limpiar($_POST['masa'] ?? '');
$salsa = limpiar($_POST['salsa'] ?? '');
$queso = limpiar($_POST['queso'] ?? '');
$ingredientes_seleccionados = $_POST['ingredientes'] ?? [];

$lista_extras = [];
if (is_array($ingredientes_seleccionados)) {
    foreach ($ingredientes_seleccionados as $ing) {
        $lista_extras[] = limpiar($ing);
    }
}

if (!empty($_POST)) {
    if (empty($masa) || empty($salsa) || empty($queso)) {
        $mensaje_alerta = '<div class="alert alert-danger" role="alert">Por favor seleccione la masa, la salsa base y el queso principal.</div>';
    } else {
        $exito = true;
        $pizza_seleccionada = [
            'masa' => $masa,
            'salsa' => $salsa,
            'queso' => $queso,
            'ingredientes' => $lista_extras
        ];
    }
}
?>

<link rel="stylesheet" href="assets/css/armar_pizza.css">
<div class="container my-5 armar-pizza">
    <div>
        <h2 class="pizza-titulo">Arma tu pizza</h2>
        <p class="pizza-subtitulo">Elige tu masa, tu salsa, tu queso</p>
    </div>
    
    <?= $mensaje_alerta ?>
    <?= $mensaje_alerta ?>

    <?php if ($exito): ?>
        <div class="card shadow mb-4 border-success">
            <div class="card-header bg-black text-white">
                <h4 class="mb-0">Pizza creada exitosamente</h4>
            </div>
            <div class="card-body">
                <p><strong>Masa seleccionada:</strong> <?= $pizza_seleccionada['masa'] ?></p>
                <p><strong>Salsa base:</strong> <?= $pizza_seleccionada['salsa'] ?></p>
                <p><strong>Queso principal:</strong> <?= $pizza_seleccionada['queso'] ?></p>
                <p><strong>Ingredientes adicionales:</strong>
                    <?php if (!empty($pizza_seleccionada['ingredientes'])): ?>
                        <?= implode(', ', $pizza_seleccionada['ingredientes']) ?>
                    <?php else: ?>
                        Ninguno
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>    

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="">
                
                <!-- Selección de Masa mediante Radio Buttons -->
                <fieldset class="pizza-seccion sec-masa">
                    <legend class="pizza_legend"><span class="paso">1. </span>Selección de Masa</legend>
                    <div class="row">
                        <?php 
                        $masas = [
                            'Tradicional italiana' => 'Tradicional italiana',
                            'Delgada / Crocante' => 'Delgada / Crocante',
                            'Orilla rellena de queso con recargo' => 'Orilla rellena de queso con recargo'
                        ];
                        foreach ($masas as $val => $label): 
                        ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="masa" id="masa_<?= md5($val) ?>" value="<?= $val ?>" <?= (($_POST['masa'] ?? '') === $val) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="masa_<?= md5($val) ?>">
                                    <?= $label ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <fieldset class="pizza-seccion sec-salsa">
                    <legend class="pizza_legend"><span class="paso">2. </span>Selección de Salsa Base</legend>
                    <div class="mb-3">
                        <select class="form-select" name="salsa" id="salsa">
                            <option value="">Seleccione una salsa...</option>
                            <option value="Pomodoro clasica" <?= (($_POST['salsa'] ?? '') === 'Pomodoro clasica') ? 'selected' : '' ?>>Pomodoro clasica</option>
                            <option value="Salsa blanca Alfredo" <?= (($_POST['salsa'] ?? '') === 'Salsa blanca Alfredo') ? 'selected' : '' ?>>Salsa blanca Alfredo</option>
                            <option value="Pesto genovas" <?= (($_POST['salsa'] ?? '') === 'Pesto genovas') ? 'selected' : '' ?>>Pesto genovas</option>
                            <option value="Barbacoa dulce" <?= (($_POST['salsa'] ?? '') === 'Barbacoa dulce') ? 'selected' : '' ?>>Barbacoa dulce</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="pizza-seccion sec-queso">
                    <legend class="pizza_legend"><span class="paso">3. </span>Selección de Queso Principal</legend>
                    <div class="mb-3">
                        <select class="form-select" name="queso" id="queso">
                            <option value="">Seleccione un queso...</option>
                            <option value="Mozzarella fresco" <?= (($_POST['queso'] ?? '') === 'Mozzarella fresco') ? 'selected' : '' ?>>Mozzarella fresco</option>
                            <option value="Queso parmesano reggiano" <?= (($_POST['queso'] ?? '') === 'Queso parmesano reggiano') ? 'selected' : '' ?>>Queso parmesano reggiano</option>
                            <option value="Queso provolone" <?= (($_POST['queso'] ?? '') === 'Queso provolone') ? 'selected' : '' ?>>Queso provolone</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="pizza-seccion sec-extras">
                    <legend class="pizza_legend"><span class="paso">4. </span>Ingredientes Adicionales</legend>
                    <div class="row">
                        <?php 
                        global $extras;
                        foreach ($extras as $nombre_extra => $precio_extra): 
                            $marcado = (isset($_POST['ingredientes']) && is_array($_POST['ingredientes']) && in_array($nombre_extra, $_POST['ingredientes'])) ? 'checked' : '';
                        ?>
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ingredientes[]" value="<?= $nombre_extra ?>" id="extra_<?= md5($nombre_extra) ?>" <?= $marcado ?>>
                                <label class="form-check-label" for="extra_<?= md5($nombre_extra) ?>">
                                    <?= $nombre_extra ?> (<?= dinero($precio_extra) ?>)
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="text-center">
                    <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold w-100 shadow">CREAR PIZZA</button>
                </div>


            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>