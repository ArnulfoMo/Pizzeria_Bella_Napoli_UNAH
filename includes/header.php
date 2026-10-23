<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bella Napoli</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbarFooter.css">


    <!-- Con este codigo se puede obtener el nombre de la pagina en la que estamos
     asi podemos hacer que el CSS sea dinamico por asi decirlo y no tener que referenciar
     todos los CSS en el header.-->
    <?php
    $pagina = basename($_SERVER['PHP_SELF'], '.php');
    ?>

    <link rel="stylesheet" href="assets/css/<?php echo $pagina; ?>.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-napoli fixed-top">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                <span>🍕</span> BELLA NAPOLI
            </a>

            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="nav">
                <div class="navbar-nav ms-auto align-items-lg-center">

                    <a class="nav-link" href="index.php">Inicio</a>
                    <a class="nav-link" href="catalogo.php">Menú</a>
                    <a class="nav-link" href="armar.php">Arma tu pizza</a>
                    <a class="nav-link" href="pedido.php">Pedidos</a>
                    <a class="nav-link" href="sucursales.php">Sucursales y órdenes</a>

                    <a class="btn btn-ordenar ms-lg-4" href="pedido.php">
                        Ordenar ahora
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <main>