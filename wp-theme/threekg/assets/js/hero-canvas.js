(function () {
  "use strict";

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ----------------------------------------------------------
     canvas dot field (hero)
     ---------------------------------------------------------- */
  const canvas = document.getElementById("field");
  if (canvas) {
    const ctx = canvas.getContext("2d");
    let w = 0, h = 0, dpr = 1;
    let dots = [];
    const mouse = { x: -9999, y: -9999 };
    let running = true;
    let rafId = null;

    function resize() {
      dpr = Math.min(window.devicePixelRatio || 1, 2);
      const rect = canvas.getBoundingClientRect();
      w = rect.width;
      h = rect.height;
      canvas.width = Math.round(w * dpr);
      canvas.height = Math.round(h * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      build();
      if (reduceMotion) draw(0); // render one static frame
    }

    function build() {
      dots = [];
      const gap = 34;
      const cols = Math.ceil(w / gap) + 2;
      const rows = Math.ceil(h / gap) + 2;
      for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
          const stagger = r % 2 ? gap / 2 : 0;
          const x0 = c * gap + stagger;
          const y0 = r * gap;
          dots.push({ x0: x0, y0: y0, x: x0, y: y0 });
        }
      }
      dots = dots.filter(function (d) { return d.x0 <= w + gap && d.y0 <= h + gap; });
    }

    function draw(t) {
      ctx.clearRect(0, 0, w, h);
      const R = 150; // mouse interaction radius
      for (let i = 0; i < dots.length; i++) {
        const d = dots[i];
        // idle drift
        let x = d.x0 + Math.sin(t * 0.55 + d.x0 * 0.008 + d.y0 * 0.012) * 2.4;
        let y = d.y0 + Math.cos(t * 0.5 + d.x0 * 0.01 + d.y0 * 0.006) * 2.4;

        // mouse repel + highlight
        const dx = x - mouse.x;
        const dy = y - mouse.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        let f = 0;
        if (dist < R && dist > 0.01) {
          f = 1 - dist / R;
          const push = f * 26;
          x += (dx / dist) * push;
          y += (dy / dist) * push;
        }

        const alpha = 0.16 + f * 0.7;
        const size = 1 + f * 1.7;
        ctx.beginPath();
        ctx.arc(x, y, size, 0, Math.PI * 2);
        ctx.fillStyle = f > 0.05
          ? "rgba(255, 59, 0, " + alpha.toFixed(3) + ")"
          : "rgba(16, 16, 16, " + alpha.toFixed(3) + ")";
        ctx.fill();
      }
    }

    function loop(tms) {
      if (!running) return;
      draw(tms / 1000);
      rafId = requestAnimationFrame(loop);
    }

    window.addEventListener("pointermove", function (e) {
      const rect = canvas.getBoundingClientRect();
      mouse.x = e.clientX - rect.left;
      mouse.y = e.clientY - rect.top;
    }, { passive: true });

    window.addEventListener("pointerleave", function () {
      mouse.x = -9999;
      mouse.y = -9999;
    });

    // pause when hero not visible
    if ("IntersectionObserver" in window && !reduceMotion) {
      new IntersectionObserver(function (entries) {
        const visible = entries[0].isIntersecting;
        if (visible && running === false) {
          running = true;
          rafId = requestAnimationFrame(loop);
        } else if (!visible && running === true) {
          running = false;
          if (rafId) cancelAnimationFrame(rafId);
        }
      }).observe(canvas);
    }

    let resizeTimer;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(resize, 150);
    });

    resize();
    if (!reduceMotion) rafId = requestAnimationFrame(loop);
  }
})();
