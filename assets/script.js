/* ============================================================
   ThreeKG® — interactions
   canvas dot field · clock · cursor · work previews · menu
   ============================================================ */
(function () {
  "use strict";

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ----------------------------------------------------------
     live local clock (header + footer)
     ---------------------------------------------------------- */
  function tickClock() {
    const now = new Date();
    const t = now.toLocaleTimeString("en-GB", { hour12: false });
    const header = document.getElementById("clock");
    const footer = document.getElementById("clockFooter");
    if (header) header.textContent = t;
    if (footer) footer.textContent = t;
  }
  tickClock();
  setInterval(tickClock, 1000);

  const tzEl = document.getElementById("tz");
  if (tzEl) {
    const offset = -new Date().getTimezoneOffset() / 60;
    const sign = offset >= 0 ? "+" : "-";
    tzEl.textContent = "GMT" + sign + Math.abs(offset);
  }

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

  /* ----------------------------------------------------------
     custom cursor
     ---------------------------------------------------------- */
  const cursor = document.querySelector(".cursor");
  if (cursor && window.matchMedia("(pointer: fine)").matches) {
    let cx = -50, cy = -50, tx = -50, ty = -50;
    window.addEventListener("pointermove", function (e) {
      tx = e.clientX;
      ty = e.clientY;
      cursor.classList.add("is-visible");
    }, { passive: true });
    document.documentElement.addEventListener("mouseleave", function () {
      cursor.classList.remove("is-visible");
    });

    (function cursorLoop() {
      cx += (tx - cx) * 0.2;
      cy += (ty - cy) * 0.2;
      cursor.style.transform = "translate(" + (cx - 0) + "px," + (cy - 0) + "px) translate(-50%,-50%)";
      requestAnimationFrame(cursorLoop);
    })();

    document.querySelectorAll("a, button, .work-block").forEach(function (el) {
      el.addEventListener("mouseenter", function () { cursor.classList.add("is-hover"); });
      el.addEventListener("mouseleave", function () { cursor.classList.remove("is-hover"); });
    });
  }

  /* ----------------------------------------------------------
     featured work — editorial alternating blocks
     ---------------------------------------------------------- */

  /* ----------------------------------------------------------
     menu overlay
     ---------------------------------------------------------- */
  const menu = document.getElementById("menu");
  const toggle = document.getElementById("menuToggle");

  if (menu && toggle) {
    function setMenu(open) {
      menu.classList.toggle("is-open", open);
      document.body.classList.toggle("menu-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.textContent = open ? "close" : "menu";
    }
    toggle.addEventListener("click", function () {
      setMenu(!menu.classList.contains("is-open"));
    });
    menu.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () { setMenu(false); });
    });
    window.addEventListener("keydown", function (e) {
      if (e.key === "Escape") setMenu(false);
    });
  }

  /* ----------------------------------------------------------
     showreel — video after hero
     click to play/pause, auto-pause when scrolled out of view
     ---------------------------------------------------------- */
  const reelFrame = document.querySelector(".showreel__frame");
  const reelVideo = document.getElementById("showreelVideo");
  const reelPlay = document.getElementById("showreelPlay");

  if (reelFrame && reelVideo && reelPlay) {
    function reelToggle() {
      if (reelVideo.readyState < 2) {
        reelPlay.querySelector(".mono").textContent = "video not found";
        return;
      }
      if (reelVideo.paused) {
        reelVideo.muted = true; // autoplay policy safe
        reelVideo.play();
        reelFrame.classList.add("is-playing");
        reelPlay.setAttribute("aria-label", "Pause showreel");
      } else {
        reelVideo.pause();
        reelFrame.classList.remove("is-playing");
        reelPlay.setAttribute("aria-label", "Play showreel");
      }
    }

    reelFrame.addEventListener("click", reelToggle);
    reelFrame.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        reelToggle();
      }
    });

    // pause when scrolled out of view
    if ("IntersectionObserver" in window) {
      new IntersectionObserver(function (entries) {
        if (!entries[0].isIntersecting && !reelVideo.paused) {
          reelVideo.pause();
          reelFrame.classList.remove("is-playing");
          reelPlay.setAttribute("aria-label", "Play showreel");
        }
      }, { threshold: 0.15 }).observe(reelFrame);
    }

    // error fallback: show a note if the file is still a placeholder / missing
    reelVideo.addEventListener("error", function () {
      reelPlay.querySelector(".mono").textContent = "add assets/showreel.mp4";
    });
  }

  /* ----------------------------------------------------------
     brand preload overlay — full symbol sequence once per load
     ---------------------------------------------------------- */
  (function loaderInit() {
    const loader = document.getElementById("loader");
    const video = document.getElementById("loaderVideo");
    if (!loader) return;

    let done = false;
    function finish() {
      if (done) return;
      done = true;
      loader.classList.add("is-done");
      document.body.classList.remove("is-loading");
      try { video.pause(); } catch (e) {}
      setTimeout(function () { loader.remove(); }, 700);
    }

    if (reduceMotion) { finish(); return; }

    // 2x pace keeps the brand moment under ~4.5s
    video.playbackRate = 2;
    video.play().catch(function () { /* autoplay blocked — waits for 'playing' */ });

    video.addEventListener("playing", function () {
      loader.classList.add("is-skippable");
    });
    video.addEventListener("ended", finish);
    video.addEventListener("error", finish);

    // click / tap to skip
    loader.addEventListener("pointerdown", function () { finish(); });
    window.addEventListener("keydown", function onKey(e) {
      if (done) { window.removeEventListener("keydown", onKey); return; }
      if (e.key === "Escape" || e.key === "Enter" || e.key === " ") {
        finish();
        window.removeEventListener("keydown", onKey);
      }
    });

    // hard safety cap in case stalling drags on
    setTimeout(finish, 6000);
  })();

  /* ----------------------------------------------------------
     services — dot-to-symbol motion plays when scrolled into view
     ---------------------------------------------------------- */
  (function servicesMotion() {
    const movers = document.querySelectorAll(".services__motion");
    if (!movers.length) return;

    const firstPlay = new WeakSet();
    function playOne(v) {
      if (v.readyState >= 2) {
        v.currentTime = 0;
        const p = v.play();
        if (p && p.catch) p.catch(function () {});
      } else {
        v.addEventListener("loadeddata", function once() {
          v.removeEventListener("loadeddata", once);
          v.play().catch(function () {});
        });
      }
    }

    function arm(row) {
      row.addEventListener("mouseenter", function () {
        const v = row.querySelector(".services__motion");
        if (v) playOne(v);
      });
    }

    if ("IntersectionObserver" in window && !reduceMotion) {
      const svcIO = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          const v = entry.target.querySelector(".services__motion");
          if (v) playOne(v);
          if (!firstPlay.has(entry.target)) {
            firstPlay.add(entry.target);
            arm(entry.target); // replay on hover after the scroll-in reveal
          }
          svcIO.unobserve(entry.target);
        });
      }, { threshold: 0.45 });
      movers.forEach(function (v) { svcIO.observe(v.closest(".services__row") || v); });
    } else {
      // reduced motion / no IO: hold the finished symbol via poster
      movers.forEach(function (v) {
        v.currentTime = v.duration || 0;
        v.pause();
      });
    }
  })();

  /* ----------------------------------------------------------
     scroll reveals
     ---------------------------------------------------------- */
  const revealEls = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && !reduceMotion) {
    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add("is-in"); });
  }
})();
