/* ==========================================================================
   AponBasket — Theme interactions
   File: theme.js
   Vanilla JS, no dependencies. Safe to load on every storefront page.
   ========================================================================== */

(function () {
    "use strict";

    const ready = (fn) => {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", fn, { once: true });
        } else {
            fn();
        }
    };

    /* ----------------------------------------------------------------------
     * 1. Announcement bar: dismiss + remember
     * -------------------------------------------------------------------- */
    function initAnnouncement() {
        const bar = document.querySelector("[data-ab-announce]");

        if (! bar) {
            return;
        }

        const KEY = "ab_announce_dismissed";

        try {
            if (localStorage.getItem(KEY) === "1") {
                bar.remove();

                return;
            }
        } catch (e) {
            /* storage blocked — keep the bar visible */
        }

        bar.querySelector("[data-ab-announce-close]")?.addEventListener("click", () => {
            bar.style.transition = "opacity .25s ease";
            bar.style.opacity = "0";

            setTimeout(() => bar.remove(), 250);

            try {
                localStorage.setItem(KEY, "1");
            } catch (e) {
                /* ignore */
            }
        });
    }

    /* ----------------------------------------------------------------------
     * 2. Hero slider
     * -------------------------------------------------------------------- */
    function initHero() {
        const slider = document.querySelector("[data-ab-hero]");

        if (! slider) {
            return;
        }

        const slides = Array.from(slider.querySelectorAll("[data-ab-slide]"));
        const dots = Array.from(slider.querySelectorAll("[data-ab-dot]"));

        if (slides.length < 2) {
            return;
        }

        let index = 0;
        let timer = null;
        const AUTOPLAY = 7200;
        const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        const go = (next) => {
            index = (next + slides.length) % slides.length;

            slides.forEach((slide, i) => slide.classList.toggle("is-active", i === index));
            dots.forEach((dot, i) => {
                dot.classList.toggle("is-active", i === index);
                dot.setAttribute("aria-selected", i === index ? "true" : "false");
            });

            restart();
        };

        const restart = () => {
            if (reduce) {
                return;
            }

            window.clearInterval(timer);
            timer = window.setInterval(() => go(index + 1), AUTOPLAY);
        };

        dots.forEach((dot, i) => dot.addEventListener("click", () => go(i)));

        slider.querySelector("[data-ab-hero-prev]")?.addEventListener("click", () => go(index - 1));
        slider.querySelector("[data-ab-hero-next]")?.addEventListener("click", () => go(index + 1));

        slider.addEventListener("mouseenter", () => window.clearInterval(timer));
        slider.addEventListener("mouseleave", restart);

        /* Keyboard support for the region */
        slider.addEventListener("keydown", (event) => {
            if (event.key === "ArrowRight") go(index + 1);
            if (event.key === "ArrowLeft") go(index - 1);
        });

        /* Touch swipe */
        let startX = null;

        slider.addEventListener("touchstart", (event) => {
            startX = event.changedTouches[0].clientX;
        }, { passive: true });

        slider.addEventListener("touchend", (event) => {
            if (startX === null) return;

            const delta = event.changedTouches[0].clientX - startX;

            if (Math.abs(delta) > 48) {
                go(delta < 0 ? index + 1 : index - 1);
            }

            startX = null;
        }, { passive: true });

        restart();
    }

    /* ----------------------------------------------------------------------
     * 3. Reveal on scroll
     * -------------------------------------------------------------------- */
    function initReveal() {
        const items = document.querySelectorAll("[data-ab-reveal]");

        if (! items.length) {
            return;
        }

        if (! ("IntersectionObserver" in window)) {
            items.forEach((item) => item.classList.add("is-visible"));

            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });

        items.forEach((item) => observer.observe(item));
    }

    /* ----------------------------------------------------------------------
     * 4. Sticky header shadow + back to top
     * -------------------------------------------------------------------- */
    function initScrollChrome() {
        const wrap = document.querySelector("[data-ab-header]");
        const top = document.querySelector("[data-ab-top]");

        const onScroll = () => {
            const y = window.scrollY || window.pageYOffset;

            if (wrap) {
                wrap.classList.toggle("is-stuck", y > 8);
            }

            if (top) {
                top.classList.toggle("is-visible", y > 620);
            }
        };

        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });

        top?.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    /* ----------------------------------------------------------------------
     * 5. Horizontal rails (product rows, preview only)
     * -------------------------------------------------------------------- */
    function initRails() {
        document.querySelectorAll("[data-ab-rail]").forEach((rail) => {
            const wrap = rail.closest("[data-ab-rail-wrap]");
            const step = () => Math.max(240, rail.clientWidth * 0.8);

            wrap?.querySelector("[data-ab-rail-prev]")?.addEventListener("click", () => {
                rail.scrollBy({ left: -step(), behavior: "smooth" });
            });

            wrap?.querySelector("[data-ab-rail-next]")?.addEventListener("click", () => {
                rail.scrollBy({ left: step(), behavior: "smooth" });
            });
        });
    }

    /* ----------------------------------------------------------------------
     * 6. Copy coupon code
     * -------------------------------------------------------------------- */
    function initCopy() {
        document.querySelectorAll("[data-ab-copy]").forEach((button) => {
            button.addEventListener("click", async () => {
                const value = button.getAttribute("data-ab-copy") || "";
                const label = button.querySelector("[data-ab-copy-label]");
                const original = label ? label.textContent : "";

                try {
                    await navigator.clipboard.writeText(value);

                    if (label) {
                        label.textContent = "Copied!";
                        setTimeout(() => { label.textContent = original; }, 1800);
                    }
                } catch (e) {
                    /* clipboard unavailable */
                }
            });
        });
    }

    /* ----------------------------------------------------------------------
     * 7. Current year
     * -------------------------------------------------------------------- */
    function initYear() {
        document.querySelectorAll("[data-ab-year]").forEach((node) => {
            node.textContent = String(new Date().getFullYear());
        });
    }

    ready(() => {
        initAnnouncement();
        initHero();
        initReveal();
        initScrollChrome();
        initRails();
        initCopy();
        initYear();
    });
})();
