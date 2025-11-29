<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seleccionar Comidas</title>

    <!-- Importar estilos del header -->
    <link rel="stylesheet" href="header.css" />

    <!-- Estilos del módulo -->
    <link rel="stylesheet" href="../css/seleccion_comidas.css" />
</head>
<body>

    <!-- Insertamos la cabecera -->
    <?php include '../components/header.php'; ?>

    <main>

        <h1 class="title">Seleccionar Comida</h1>

        <section class="tabs">
            <button class="tab active">Mañana</button>
            <button class="tab">Tarde</button>
            <button class="tab">Noche</button>
        </section>

        <section class="search-box">
            <input type="text" placeholder="Buscar comida..." />
        </section>

        <section class="comidas-container">

            <div class="card">
                <img src="https://via.placeholder.com/120" alt="Comida">
                <div class="info">
                    <h3>Huevos revueltos</h3>
                    <p>Calorías: 250</p>
                    <button class="btn">Seleccionar</button>
                </div>
            </div>

            <div class="card">
                <img src="https://via.placeholder.com/120" alt="Comida">
                <div class="info">
                    <h3>Ensalada César</h3>
                    <p>Calorías: 180</p>
                    <button class="btn">Seleccionar</button>
                </div>
            </div>

            <div class="card">
                <img src="https://via.placeholder.com/120" alt="Comida">
                <div class="info">
                    <h3>Sandwich integral</h3>
                    <p>Calorías: 300</p>
                    <button class="btn">Seleccionar</button>
                </div>
            </div>

        </section>

    </main>

</body>
</html>
