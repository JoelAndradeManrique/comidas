<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sugerencias Automáticas</title>

    <!-- Estilos del header -->
    <link rel="stylesheet" href="header.css" />

    <!-- Estilos del módulo -->
    <link rel="stylesheet" href="../css/sugerencias.css" />
</head>
<body>

    <!-- CABECERA REUTILIZABLE -->
    <?php include '../components/header.php'; ?>

    <main>

        <h1 class="title">Sugerencias Automáticas</h1>

        <!-- Filtros -->
        <section class="filtros">
            <button class="filtro active">Rápido</button>
            <button class="filtro">Saludable</button>
            <button class="filtro">Económico</button>
        </section>

        <!-- Botón para regenerar sugerencias -->
        <div class="regen-box">
            <button class="regen-btn">Generar nuevas sugerencias</button>
        </div>

        <!-- Contenedor de sugerencias -->
        <section class="sugerencias-container">

            <div class="card">
                <img src="https://via.placeholder.com/200" alt="Platillo">
                <div class="info">
                    <h3>Ensalada fresca</h3>
                    <p>Categoría: Saludable</p>
                    <p>Tiempo: 5 min</p>
                    <button class="btn">Usar sugerencia</button>
                </div>
            </div>

            <div class="card">
                <img src="https://via.placeholder.com/200" alt="Platillo">
                <div class="info">
                    <h3>Sándwich integral</h3>
                    <p>Categoría: Rápido</p>
                    <p>Tiempo: 4 min</p>
                    <button class="btn">Usar sugerencia</button>
                </div>
            </div>

            <div class="card">
                <img src="https://via.placeholder.com/200" alt="Platillo">
                <div class="info">
                    <h3>Arroz con verduras</h3>
                    <p>Categoría: Económico</p>
                    <p>Tiempo: 10 min</p>
                    <button class="btn">Usar sugerencia</button>
                </div>
            </div>

        </section>

    </main>

</body>
</html>
