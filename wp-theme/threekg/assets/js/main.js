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
     brand preload overlay — full symbol sequence once per load
     ---------------------------------------------------------- */
  (function loaderInit() {
    const loader = document.getElementById("loader");
    const video = document.getElementById("loaderVideo");
    if (!loader) {
      /* loader markup is absent (inner pages): never leave the page trapped */
      document.body.classList.remove("is-loading");
      return;
    }

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
     services — dot-to-symbol cards
     engine ported from 3KG_Profile_Hover_Experiment.html:
     procedural SVG renderer, hover contracts to a dot and
     ping-pongs; plays a one-shot construct on scroll reveal
     ---------------------------------------------------------- */
  (function servicesSymbols() {
    const buttons = document.querySelectorAll(".symbol-btn");
    if (!buttons.length) return;

    const NS = "http://www.w3.org/2000/svg";
    const N = 192, TAU = 2 * Math.PI, STROKE = 19, ROUND = 0.75;
    const clamp = x => Math.max(0, Math.min(1, x));
    const mix = (a, b, t) => a + (b - a) * t;
    const ease = x => { x = clamp(x); return x * x * x * (x * (x * 6 - 15) + 10); };
    const lock = x => { x = clamp(x); return ease(x) + 0.018 * Math.sin(4 * Math.PI * x) * Math.pow(Math.sin(Math.PI * x), 2); };
    const rotate = (pts, deg) => { const a = deg * Math.PI / 180, c = Math.cos(a), s = Math.sin(a); return pts.map(pt => [pt[0] * c - pt[1] * s, pt[0] * s + pt[1] * c]); };
    const blend = (a, b, q) => a.map((p, i) => [mix(p[0], b[i][0], q), mix(p[1], b[i][1], q)]);
    const cross = (a, b) => a[0] * b[1] - a[1] * b[0];

    function circle(r) {
      return Array.from({ length: N }, function (_, i) {
        const a = -Math.PI / 2 + i * TAU / N;
        return [r * Math.cos(a), r * Math.sin(a)];
      });
    }
    function rounded(vertices) {
      const result = [];
      const corners = vertices.map(function (v, i) {
        const a = vertices[(i + vertices.length - 1) % vertices.length];
        const b = vertices[(i + 1) % vertices.length];
        const da = Math.hypot(v[0] - a[0], v[1] - a[1]);
        const db = Math.hypot(v[0] - b[0], v[1] - b[1]);
        const trim = ROUND * 0.42 * Math.min(da, db);
        return {
          v: v,
          enter: [mix(v[0], a[0], trim / da), mix(v[1], a[1], trim / da)],
          exit: [mix(v[0], b[0], trim / db), mix(v[1], b[1], trim / db)]
        };
      });
      corners.forEach(function (c, i) {
        for (let j = 0; j < 9; j++) {
          const t = j / 8;
          result.push([(1 - t) * (1 - t) * c.enter[0] + 2 * (1 - t) * t * c.v[0] + t * t * c.exit[0],
                       (1 - t) * (1 - t) * c.enter[1] + 2 * (1 - t) * t * c.v[1] + t * t * c.exit[1]]);
        }
        const next = corners[(i + 1) % corners.length].enter;
        for (let j = 1; j < 9; j++) {
          result.push([mix(c.exit[0], next[0], j / 8), mix(c.exit[1], next[1], j / 8)]);
        }
      });
      return result;
    }
    function radial(vertices) {
      const pts = rounded(vertices);
      return Array.from({ length: N }, function (_, i) {
        const a = -Math.PI / 2 + i * TAU / N;
        const ray = [Math.cos(a), Math.sin(a)];
        let near = Infinity;
        pts.forEach(function (v, j) {
          const b = pts[(j + 1) % pts.length];
          const s = [b[0] - v[0], b[1] - v[1]];
          const d = cross(ray, s);
          if (Math.abs(d) < 1e-8) return;
          const t = cross(v, s) / d;
          const u = cross(v, ray) / d;
          if (t > 0 && u >= -1e-7 && u <= 1 + 1e-7) near = Math.min(near, t);
        });
        return [ray[0] * near, ray[1] * near];
      });
    }

    const square = radial([[-72, -72], [72, -72], [72, 72], [-72, 72]]);
    const forms = {
      brand: circle(76),
      studio: rotate(square, 45),
      production: radial([[-65, -78], [65, 0], [-65, 78]]),
      tech: square,
      "selected-work": radial([[0, -86], [74.478, -43], [74.478, 43], [0, 86], [-74.478, 43], [-74.478, -43]])
    };
    const origin = circle(76);

    function makeElement(tag, attributes, parent) {
      const el = document.createElementNS(NS, tag);
      Object.keys(attributes).forEach(function (k) { el.setAttribute(k, attributes[k]); });
      parent.append(el);
      return el;
    }

    function renderer(svg, key) {
      const group = makeElement("g", { fill: "none", stroke: "currentColor", "stroke-width": STROKE, "stroke-linejoin": "round" }, svg);
      const paths = [];
      for (let i = 0; i < 8; i++) paths.push(makeElement("path", {}, group));
      const caps = [];
      for (let i = 0; i < 16; i++) caps.push(makeElement("ellipse", { fill: "currentColor", stroke: "none" }, group));
      const dot = makeElement("circle", { r: STROKE / 2, fill: "currentColor", stroke: "none" }, group);
      function path(pts, index, closed) {
        const el = paths[index];
        el.style.display = "";
        el.setAttribute("d", "M" + pts.map(p => p[0].toFixed(3) + "," + p[1].toFixed(3)).join("L") + (closed ? "Z" : ""));
        if (!closed) {
          [0, 1].forEach(function (end) {
            const j = end ? pts.length - 1 : 0;
            const k = end ? j - 1 : 1;
            const p = pts[j], q = pts[k];
            const cap = caps[index * 2 + end];
            const angle = Math.atan2(p[1] - q[1], p[0] - q[0]) * 180 / Math.PI;
            cap.style.display = "";
            cap.setAttribute("cx", p[0]);
            cap.setAttribute("cy", p[1]);
            cap.setAttribute("rx", STROKE / 2 * ROUND);
            cap.setAttribute("ry", STROKE / 2);
            cap.setAttribute("transform", "rotate(" + angle + " " + p[0] + " " + p[1] + ")");
          });
        }
      }
      return function (progress) {
        paths.forEach(p => { p.style.display = "none"; });
        caps.forEach(p => { p.style.display = "none"; });
        dot.style.display = "none";
        const q = ease(progress);
        if (progress <= 0) { dot.style.display = ""; return; }
        if (forms[key]) {
          const body = blend(origin, forms[key], lock((progress - 0.1) / 0.9));
          const angle = (key === "studio" ? -45 : -10) * Math.pow(Math.sin(Math.PI * progress), 2);
          const pts = rotate(body.map(pt => [pt[0] * q, pt[1] * q]), angle);
          const xs = pts.map(p => p[0]);
          const ys = pts.map(p => p[1]);
          const cx = (Math.min.apply(null, xs) + Math.max.apply(null, xs)) / 2;
          const cy = (Math.min.apply(null, ys) + Math.max.apply(null, ys)) / 2;
          path(pts.map(pt => [pt[0] - cx, pt[1] - cy]), 0, true);
        } else {
          const count = key === "campaigns" ? 8 : 4;
          const base = key === "xperience" ? 45 : 0;
          const index = -32 * Math.pow(Math.sin(Math.PI * progress), 2);
          for (let i = 0; i < count; i++) {
            const a = (-90 + base + i * 360 / count + index) * Math.PI / 180;
            const r = Math.max(0.001, 80 * q);
            path([[0, 0], [Math.cos(a) * r, Math.sin(a) * r]], i);
            caps[i * 2 + 1].setAttribute("rx", STROKE / 2 * mix(1, ROUND, q));
          }
        }
      };
    }

    const states = new Map();
    let active = null, pinned = null;

    buttons.forEach(function (button) {
      const card = button.closest(".svc-card");
      const svg = button.querySelector("svg");
      const key = button.dataset.category;
      const st = {
        key: key,
        button: button,
        card: card,
        draw: renderer(svg, key),
        phase: 1,
        ping: 0,
        reveal: false,
        revealStart: 0,
        settling: false,
        from: 1,
        left: 0
      };
      states.set(key, st);
      st.draw(1);
    });

    function choose(next) {
      if (next === active) return;
      const now = performance.now();
      active = next;
      states.forEach(function (st, key) {
        const isActive = key === active;
        st.button.classList.toggle("is-active", isActive);
        st.button.setAttribute("aria-pressed", String(key === pinned));
        if (isActive) {
          st.ping = now;
          st.settling = false;
          st.reveal = false;
          if (reduceMotion) { st.phase = 1; st.draw(1); }
        } else if (!st.reveal && st.phase !== 1) {
          st.from = st.phase;
          st.left = now;
          st.settling = true;
        }
      });
    }

    function tick(now) {
      states.forEach(function (st, key) {
        if (key === active) {
          /* contract to the dot, then ping-pong (experiment timing) */
          const elapsed = (now - st.ping) / 1000;
          const period = 2.3;
          const cycle = elapsed % period;
          const phase = cycle < 0.95 ? 1 - cycle / 0.95
                      : cycle < 1.12 ? 0
                      : cycle < 2.07 ? (cycle - 1.12) / 0.95
                      : 1;
          st.phase = clamp(phase);
          st.draw(st.phase);
        } else if (st.reveal) {
          const p = (now - st.revealStart) / 1300;
          if (p >= 1) { st.reveal = false; st.phase = 1; st.draw(1); }
          else { st.phase = p; st.draw(p); }
        } else if (st.settling) {
          const q = ease((now - st.left) / 240);
          st.phase = mix(st.from, 1, q);
          st.draw(st.phase);
          if (q === 1) st.settling = false;
        }
      });
      requestAnimationFrame(tick);
    }

    if (reduceMotion) {
      states.forEach(function (st) { st.draw(1); });
      return;
    }

    const coarse = window.matchMedia("(hover: none)").matches;

    states.forEach(function (st) {
      const zone = st.button.closest(".svc-card") || st.button;
      zone.addEventListener("mouseenter", function () { choose(st.key); });
      zone.addEventListener("mouseleave", function () { choose(pinned || null); });
      st.button.addEventListener("focus", function () { choose(st.key); });
      st.button.addEventListener("blur", function () { if (pinned !== st.key) choose(pinned || null); });
      st.button.addEventListener("click", function (e) {
        if (coarse || e.pointerType === "touch") {
          pinned = pinned === st.key ? null : st.key;
          choose(pinned || st.key);
        }
      });
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") { pinned = null; choose(null); }
    });
    document.addEventListener("pointerdown", function (e) {
      if (pinned && !e.target.closest(".svc-card, .work__symbol")) { pinned = null; choose(null); }
    });

    /* one-shot construct when a card scrolls into view */
    if ("IntersectionObserver" in window) {
      const all = Array.prototype.slice.call(document.querySelectorAll(".svc-card .symbol-btn"))
        .map(function (button) { return button.closest(".svc-card"); });
      const svcIO = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          let button, delay = 0;
          if (entry.target.classList.contains("svc-card")) {
            button = entry.target.querySelector(".symbol-btn");
            delay = (all.indexOf(entry.target) % 3) * 170;
          } else {
            button = entry.target;
          }
          const st = states.get(button.dataset.category);
          setTimeout(function () {
            if (st.key === active) return; // already animating via hover
            st.reveal = true;
            st.revealStart = performance.now();
          }, delay);
          svcIO.unobserve(entry.target);
        });
      }, { threshold: 0.35 });
      all.forEach(function (card) { svcIO.observe(card); });
      const hex = document.querySelector(".work__symbol");
      if (hex) svcIO.observe(hex);
    }

    requestAnimationFrame(tick);
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
