
document.addEventListener("DOMContentLoaded", function () {

    // Animación de las tarjetas
    document.querySelectorAll(".card").forEach(function (card, index) {
        card.classList.add("animacion-tarjeta");
        card.style.animationDelay = (index * 0.12) + "s";
    });

    // Animación de las filas de las tablas
    document.querySelectorAll(".table tbody tr").forEach(function (fila, index) {
        fila.classList.add("animacion-fila");
        fila.style.animationDelay = (index * 0.06) + "s";
    });

    // Efecto de movimiento en los botones
    document.querySelectorAll(".btn").forEach(function (boton) {
        boton.addEventListener("mouseenter", function () {
            boton.style.transform = "translateY(-3px)";
        });

        boton.addEventListener("mouseleave", function () {
            boton.style.transform = "";
        });
    });

    // Aparición de elementos al desplazarse
    const elementos = document.querySelectorAll(".animar-scroll");

    if ("IntersectionObserver" in window) {
        const observador = new IntersectionObserver(
            function (entradas) {
                entradas.forEach(function (entrada) {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add("visible");
                        observador.unobserve(entrada.target);
                    }
                });
            },
            { threshold: 0.15 }
        );

        elementos.forEach(function (elemento) {
            elemento.classList.add("aparecer-scroll");
            observador.observe(elemento);
        });
    } else {
        elementos.forEach(function (elemento) {
            elemento.classList.add("visible");
        });
    }

    // Efecto de brillo en el título
    document.querySelectorAll(".titulo-liga").forEach(function (titulo) {
        titulo.classList.add("titulo-animado");
    });

});
