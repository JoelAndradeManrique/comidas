<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Plan Diario / Semanal</title>

    <!-- Header -->
    <link rel="stylesheet" href="header.css">

    <!-- Estilos del módulo -->
    <link rel="stylesheet" href="../css/plan.css">
</head>
<body>

    <!-- CABECERA -->
    <?php include '../components/header.php'; ?>

    <main>

        <h1 class="title">Plan Diario / Semanal</h1>

        <!-- Tabs -->
        <section class="tabs">
            <button id="btnDia" class="tab active">Día</button>
            <button id="btnSemana" class="tab">Semana</button>
        </section>

        <!-- PLAN DIARIO -->
        <section id="planDia" class="plan-dia">

            <div class="dia-selector">
                <button class="nav-day">←</button>
                <h2>Hoy</h2>
                <button class="nav-day">→</button>
            </div>

            <div class="comida-card">
                <h3>Desayuno</h3>
                <p>Huevos revueltos con verduras</p>
                <button class="editar-btn">Editar</button>
            </div>

            <div class="comida-card">
                <h3>Comida</h3>
                <p>Pollo a la plancha con arroz</p>
                <button class="editar-btn">Editar</button>
            </div>

            <div class="comida-card">
                <h3>Cena</h3>
                <p>Ensalada fresca</p>
                <button class="editar-btn">Editar</button>
            </div>

        </section>

        <!-- PLAN SEMANAL -->
        <section id="planSemana" class="plan-semana hidden">

            <table>
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Desayuno</th>
                        <th>Comida</th>
                        <th>Cena</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Lunes</td>
                        <td>Omelette</td>
                        <td>Pechuga asada</td>
                        <td>Ensalada</td>
                    </tr>
                    <tr>
                        <td>Martes</td>
                        <td>Avena</td>
                        <td>Tostadas de atún</td>
                        <td>Sopa de verduras</td>
                    </tr>
                    <tr>
                        <td>Miércoles</td>
                        <td>Yogurt y frutas</td>
                        <td>Arroz con pollo</td>
                        <td>Sándwich integral</td>
                    </tr>
                </tbody>
            </table>

        </section>

    </main>

    <script>
        // Tabs
        const btnDia = document.getElementById("btnDia");
        const btnSemana = document.getElementById("btnSemana");

        const planDia = document.getElementById("planDia");
        const planSemana = document.getElementById("planSemana");

        btnDia.addEventListener("click", () => {
            btnDia.classList.add("active");
            btnSemana.classList.remove("active");

            planDia.classList.remove("hidden");
            planSemana.classList.add("hidden");
        });

        btnSemana.addEventListener("click", () => {
            btnSemana.classList.add("active");
            btnDia.classList.remove("active");

            planSemana.classList.remove("hidden");
            planDia.classList.add("hidden");
        });
    </script>

</body>
</html>
