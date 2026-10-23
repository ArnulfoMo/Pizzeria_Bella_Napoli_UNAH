<?php require 'datos.php';
include 'includes/header.php'; ?>


<section class="hero">
    <div class="container">
        <div class="row align-items-center min-vh-hero g-5">
            <div class="col-lg-6"><span class="hero-text light">LA MEJOR PIZZERIA DEL MUNDO</span>
                <h1 class="hero-title">El arte de una buena pizza.</h1>
                <p class="hero-intro">Ingredientes frescos, masa hecha a mano y el mejor sabor posible.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="catalogo.php" class="btn btn-rojo btn-lg">Explorar el menú</a>
                    <a href="armar.php" class="btn btn-outline-light btn-lg">Arma tu pizza</a>
                </div>
                <div class="hero-meta">+ Tradición artesanal + Ingredientes seleccionados</div>
            </div>
            <div class="col-lg-6">
                <div class="hero-picture">
                    <img src="assets/img/hero.jpg" alt="Pizza" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-pad section-cream">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5"><span class="hero-text">NUESTRA TRADICION</span>
                <h2>Hecha de la mejor manera</h2>
            </div>
            <div class="col-lg-7">
                <p class="section-text">En Bella Napoli creemos en las recetas que cuentan historias. Dejamos reposar nuestra masa,
                    escogemos cada ingrediente y horneamos a alta temperatura para lograr ese borde dorado que hace inolvidable
                    cada porción.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row g-5 align-items-center">
            <h2>Las 3 pizzas del día</h2>
            <div class="row g-3"><?php foreach (array_slice($menu['Pizzas clasica'], 0, 3) as $pizza): ?>
                    <div class="col-md-4">
                        <div class="card"><img class="pizza-img" src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600" alt="Pizza">
                            <div class="card-body">
                                <span class="badge bg-success">Especial del día</span>
                                <h5><?= limpiar($pizza[0]) ?></h5>
                                <p><?= limpiar($pizza[1]) ?></p>

                                <!-- Precio y boton -->
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-4">

                                    <strong class="precio-pizza">
                                        Desde <?= dinero($pizza[2][0]) ?>
                                    </strong>

                                    <a href="pedido.php" class="btn btn-pedir">
                                        Pedir
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div><?php endforeach; ?>
            </div>
        </div>
    </div>

</section>

<section class="section-pad section-cream">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8"><span class="badge text-bg-warning mb-2">PROMOCIÓN</span>
                <h2>Pide 3 o más pizzas y recibe <strong>10% de descuento</strong></h2>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="pedido.php" class="btn btn-pedir btn-lg">Aprovechar oferta</a>
            </div>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row g-4 align-items-center text-center">
            <h2 class="mb-5">Lo que dicen nuestros <strong>Clientes</strong></h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="client-card">“La masa es deliciosa y la salsa tiene un sabor excelente.”<strong>— María G.</strong></div>
                </div>
                <div class="col-md-4">
                    <div class="client-card">“La pizza personalizada quedó perfecta.”<strong>— Carlos R.</strong></div>
                </div>
                <div class="col-md-4">
                    <div class="client-card">“Excelente variedad y un proceso de pedido muy sencillo.”<strong>— Andrea L.</strong></div>
                </div>
            </div>


        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>