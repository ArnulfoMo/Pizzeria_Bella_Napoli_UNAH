<?php
session_start();
$menu = [
  'Pizzas clasica' => [
    ['Margarita', 'Tomate, mozzarella y albahaca', [110, 170, 250, 340]],
    ['Pepperoni', 'Queso mozzarella y pepperoni', [125, 190, 280, 370]],
    ['Hawaiana', 'Jamón, piña y queso', [125, 190, 280, 370]],
    ['Cuatro Quesos', 'Mozzarella, parmesano, provolone y azul', [140, 210, 310, 410]]
  ],
  'Pizzas gourmet de la casa' => [
    ['Prosciutto di Parma', 'Jamón curado, rúcula y queso', [175, 260, 380, 490]],
    ['Trufada Napolitana', 'Champiñones, trufa y mozzarella', [180, 270, 390, 510]],
    ['Frutti di Mare', 'Mariscos, ajo y tomate', [190, 290, 420, 550]],
    ['Carnívora Suprema', 'Carne, tocineta y pepperoni', [180, 275, 400, 530]]
  ],
  'Calzones y especialidades al horno' => [
    ['Calzone de Carnes', 'Carne, jamón y queso', [130, 195, 285, 375]],
    ['Calzone Vegetariano', 'Vegetales y queso', [120, 180, 265, 350]],
    ['Focaccia con Romero', 'Aceite de oliva y romero', [85, 130, 190, 250]]
  ],
  'Bebidas y postres Italianos' => [
    ['Tiramisú casero', 'Café, cacao y crema', [90, 90, 90, 90]],
    ['Cannoli sicilianos', 'Masa crujiente y crema', [75, 75, 75, 75]],
    ['Bebidas gaseosas', 'Refresco frío', [35, 35, 35, 35]],
    ['Refrescos naturales', 'Fruta de temporada', [45, 45, 45, 45]]
  ]
];

$tamanos = ['Personal (8")', 'Mediana (12")', 'Familiar (16")', 'Gigante (20")'];
$extras = ['Pepperoni' => 25, 'Jamón de pavo' => 20, 'Champiñones frescos' => 20, 'Pimientos morrones' => 15, 'Aceitunas negras' => 15, 'Albahaca fresca' => 10, 'Tocineta ahumada' => 30, 'Carne molida artesanal' => 35];

function limpiar($dato)
{
  return htmlspecialchars(trim((string)$dato), ENT_QUOTES, 'UTF-8');
}
function dinero($numero)
{
  return 'L. ' . number_format($numero, 2);
}
function pizzas()
{
  global $menu;
  $lista = [];
  foreach ($menu as $categoria => $productos) {
    foreach ($productos as $producto) {
      $lista[$producto[0]] = $producto;
    }
  }
  return $lista;
}
