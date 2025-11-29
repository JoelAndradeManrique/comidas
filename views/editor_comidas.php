<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Comidas</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/editor_comidas.css">
</head>
<body>

    <!-- HEADER -->
    <?php include '../components/header.php'; ?>

    <main class="editor-container">

        <h2>Editor de Comidas</h2>
        <p class="desc">
            Aquí puedes crear, modificar o eliminar tus comidas personalizadas.
        </p>

        <section class="editor-panel">

            <!-- FORMULARIO -->
            <form class="food-form">
                <h3>Crear / Editar comida</h3>

                <label>Nombre de la comida</label>
                <input type="text" placeholder="Ej. Ensalada con pollo">

                <label>Categoría</label>
                <select>
                    <option>Desayuno</option>
                    <option>Comida</option>
                    <option>Cena</option>
                    <option>Snack</option>
                </select>

                <label>Calorías</label>
                <input type="number" placeholder="Ej. 350">

                <label>Ingredientes</label>
                <textarea rows="4" placeholder="Ej. pollo, lechuga, jitomate, limón..."></textarea>

                <button type="submit" class="btn-save">Guardar</button>
            </form>

            <!-- LISTA DE COMIDAS -->
            <div class="food-list">
                <h3>Mis comidas personalizadas</h3>

                <div class="food-item">
                    <div>
                        <strong>Ensalada con pollo</strong>
                        <p>350 kcal</p>
                    </div>
                    <div class="actions">
                        <button class="edit">Editar</button>
                        <button class="delete">Eliminar</button>
                    </div>
                </div>

                <div class="food-item">
                    <div>
                        <strong>Avena con manzana</strong>
                        <p>220 kcal</p>
                    </div>
                    <div class="actions">
                        <button class="edit">Editar</button>
                        <button class="delete">Eliminar</button>
                    </div>
                </div>

            </div>

        </section>

    </main>

</body>
</html>
