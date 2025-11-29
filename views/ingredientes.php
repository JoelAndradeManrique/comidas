<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Ingredientes</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/ingredientes.css">
</head>
<body>

    <!-- HEADER -->
    <?php include '../components/header.php'; ?>

    <main class="ingredients-container">

        <h2>Lista de Ingredientes</h2>
        <p class="desc">
            Aquí verás todos los ingredientes necesarios para tus comidas seleccionadas.
            Puedes marcarlos como comprados o agregar nuevos.
        </p>

        <section class="ingredients-panel">

            <!-- FORM PARA AGREGAR INGREDIENTE -->
            <form class="ingredient-form">
                <input type="text" placeholder="Agregar nuevo ingrediente...">
                <button type="submit">Agregar</button>
            </form>

            <!-- LISTA DE INGREDIENTES -->
            <div class="ingredients-list">

                <div class="ingredient-item">
                    <label>
                        <input type="checkbox">
                        Pechuga de pollo
                    </label>
                    <button class="delete">✕</button>
                </div>

                <div class="ingredient-item">
                    <label>
                        <input type="checkbox" checked>
                        Lechuga romana
                    </label>
                    <button class="delete">✕</button>
                </div>

                <div class="ingredient-item">
                    <label>
                        <input type="checkbox">
                        Tomate
                    </label>
                    <button class="delete">✕</button>
                </div>

                <div class="ingredient-item">
                    <label>
                        <input type="checkbox">
                        Arroz blanco
                    </label>
                    <button class="delete">✕</button>
                </div>

            </div>

        </section>

    </main>

</body>
</html>
