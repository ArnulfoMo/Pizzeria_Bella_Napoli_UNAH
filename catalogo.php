<?php require 'datos.php';
include 'includes/header.php'; ?>

<section class="section-pad section-cream">
    <div class="container">
        <h1>Menú de especialidades</h1>
        <p>Lo mejor de nuestra casa.</p>
        <!-- Recorrer categorias -->
        <?php foreach ($menu as $categoria => $productos): ?>

            <h2 class="titulo-categoria">
                <?= limpiar($categoria) ?>
            </h2>

            <div class="row g-4 mb-5">

                <!-- Recorrer productos -->
                <?php foreach ($productos as $pizza): ?>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 card-pizza">

                            <img src="assets/img/hero.jpg"
                                class="card-img-top pizza-img"
                                alt="Pizza">

                            <div class="card-body d-flex flex-column">

                                <h4><?= limpiar($pizza[0]) ?></h4>

                                <p><?= limpiar($pizza[1]) ?></p>

                                <!-- Tabla de precios -->
                                <table class="table mt-3">

                                    <?php foreach ($tamanos as $i => $tamano): ?>
                                        <tr>
                                            <td><?= limpiar($tamano) ?></td>
                                            <td class="text-end fw-bold">
                                                <?= dinero($pizza[2][$i]) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </table>

                                <!-- btn -->
                                <a href="pedido.php?pizza=<?= urlencode($pizza[0]) ?>"
                                    class="btn btn-pedir mt-auto">
                                    Pedir
                                </a>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>


    </div>

</section>



<?php include 'includes/footer.php'; ?>