
"use strict";

document.addEventListener("DOMContentLoaded", function () {

    // Detectar si el usuario prefiere menos movimiento.
    const reducirMovimiento = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
    ).matches;

    // 1. Animación de tarjetas.
    document.querySelectorAll(".card").forEach(function (card, index) {
        if (!reducirMovimiento) {
            card.classList.add("animacion-tarjeta");
            card.style.animationDelay = Math.min(index * 0.10, 0.6) + "s";
        }
    });

    // 2. Animación de filas de tablas.
    document.querySelectorAll(".table tbody tr").forEach(function (fila, index) {
        if (!reducirMovimiento) {
            fila.classList.add("animacion-fila");
            fila.style.animationDelay = Math.min(index * 0.045, 0.7) + "s";
        }
    });

    // 3. Efecto visual en los títulos.
    document.querySelectorAll(
        ".titulo-liga, .container > h1"
    ).forEach(function (titulo) {
        titulo.classList.add("titulo-liga");

        if (!reducirMovimiento) {
            titulo.classList.add("titulo-animado");
        }
    });

    // 4. Efecto de interacción con los botones.
    document.querySelectorAll(".btn").forEach(function (boton) {

        boton.addEventListener("mouseenter", function () {
            if (!reducirMovimiento && !boton.disabled) {
                boton.style.filter = "brightness(1.10)";
            }
        });

        boton.addEventListener("mouseleave", function () {
            boton.style.filter = "";
        });

        // Evita enviar varias veces el mismo formulario
        // después de un primer envío válido.
        if (boton.type === "submit") {
            boton.addEventListener("click", function () {
                const formulario = boton.form;

                if (formulario && !formulario.checkValidity()) {
                    return;
                }

                if (formulario && formulario.dataset.enviando === "true") {
                    return;
                }

                if (formulario) {
                    formulario.dataset.enviando = "true";
                }
            });
        }
    });

    // 5. Confirmación antes de eliminar.
    document.querySelectorAll("form[onsubmit]").forEach(function (formulario) {

        formulario.addEventListener("submit", function (evento) {
            const texto = formulario.getAttribute("onsubmit") || "";

            // Mantiene las confirmaciones ya definidas en el HTML.
            // No añade una segunda confirmación.
            if (texto.includes("confirm(")) {
                // La confirmación original del HTML se conserva.
            }
        });
    });

    // 6. Aparición de elementos al desplazarse.
    const elementos = document.querySelectorAll(".animar-scroll");

    if (reducirMovimiento || !("IntersectionObserver" in window)) {
        elementos.forEach(function (elemento) {
            elemento.classList.add("visible");
        });
    } else {
        const observador = new IntersectionObserver(
            function (entradas, observer) {
                entradas.forEach(function (entrada) {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add("visible");
                        observer.unobserve(entrada.target);
                    }
                });
            },
            {
                threshold: 0.12,
                rootMargin: "0px 0px -30px 0px"
            }
        );

        elementos.forEach(function (elemento) {
            elemento.classList.add("aparecer-scroll");
            observador.observe(elemento);
        });
    }

    // 7. Año actual, si existe un elemento para mostrarlo.
    const anio = document.querySelector("[data-anio-actual]");

    if (anio) {
        anio.textContent = new Date().getFullYear();
    }

});
