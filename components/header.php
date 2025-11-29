<header class="main-header">
    <link rel="stylesheet" href="../css/header.css" />

    <div class="logo">
        <h2>MyFoods</h2>
    </div>

    <nav class="menu">
        <a href="seleccion-comidas.html">Comidas</a>
        <a href="sugerencias.html">Sugerencias</a>
        <a href="plan.html">Plan</a>
        <a href="editor.html">Editor</a>
        <a href="ingredientes.html">Ingredientes</a>
    </nav>

    <div class="user-section">
        <div class="user-initials" id="userBtn">JA</div>

        <div class="dropdown" id="dropdownMenu">
            <button id="logoutBtn">Cerrar sesión</button>
        </div>
    </div>

</header>

<script>
    // Mostrar/ocultar menú de usuario
    const userBtn = document.getElementById("userBtn");
    const dropdown = document.getElementById("dropdownMenu");

    userBtn.addEventListener("click", () => {
        dropdown.classList.toggle("show");
    });

    // Ocultar si se hace clic fuera
    document.addEventListener("click", (e) => {
        if (!userBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove("show");
        }
    });
</script>
