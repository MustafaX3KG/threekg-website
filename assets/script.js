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
     services — direct SVG geometry morphs scrubbed by scroll
     ---------------------------------------------------------- */
  (function servicesScrollStory() {
    const section = document.querySelector(".services");
    const cards = Array.prototype.slice.call(document.querySelectorAll(".services__card"));
    const svg = document.querySelector(".services__morph-svg");
    const indexLabel = document.querySelector(".services__motion-index");
    const nameLabel = document.querySelector(".services__motion-label");
    if (!section || !cards.length || !svg) return;

    const NS = "http://www.w3.org/2000/svg";
    const N = 192;
    const TAU = Math.PI * 2;
    const STRANDS = 8;
    const POINTS = 25;
    const STROKE = 19;
    const ROUNDNESS = 0.09;
    let activeIndex = -1;
    let ticking = false;
    const clamp01 = function (x) { return Math.max(0, Math.min(1, x)); };
    const mix = function (a, b, t) { return a + (b - a) * t; };
    const smooth = function (x) { x = clamp01(x); return x * x * x * (x * (x * 6 - 15) + 10); };
    const lock = function (x) {
      x = clamp01(x);
      return smooth(x) + 0.018 * Math.sin(4 * Math.PI * x) * Math.pow(Math.sin(Math.PI * x), 2);
    };

    function rotatePoint(point, degrees) {
      const angle = degrees * Math.PI / 180;
      const cosine = Math.cos(angle);
      const sine = Math.sin(angle);
      return [point[0] * cosine - point[1] * sine, point[0] * sine + point[1] * cosine];
    }

    function roundedVertices(vertices) {
      const points = [];
      const corners = vertices.map(function (vertex, i) {
        const previous = vertices[(i + vertices.length - 1) % vertices.length];
        const next = vertices[(i + 1) % vertices.length];
        const previousDistance = Math.hypot(vertex[0] - previous[0], vertex[1] - previous[1]);
        const nextDistance = Math.hypot(vertex[0] - next[0], vertex[1] - next[1]);
        const trim = ROUNDNESS * 0.42 * Math.min(previousDistance, nextDistance);
        return {
          vertex: vertex,
          enter: [mix(vertex[0], previous[0], trim / previousDistance), mix(vertex[1], previous[1], trim / previousDistance)],
          exit: [mix(vertex[0], next[0], trim / nextDistance), mix(vertex[1], next[1], trim / nextDistance)]
        };
      });
      corners.forEach(function (corner, i) {
        for (let j = 0; j < 9; j++) {
          const t = j / 8;
          points.push([
            (1 - t) * (1 - t) * corner.enter[0] + 2 * (1 - t) * t * corner.vertex[0] + t * t * corner.exit[0],
            (1 - t) * (1 - t) * corner.enter[1] + 2 * (1 - t) * t * corner.vertex[1] + t * t * corner.exit[1]
          ]);
        }
        const nextEnter = corners[(i + 1) % corners.length].enter;
        for (let j = 1; j < 9; j++) {
          points.push([mix(corner.exit[0], nextEnter[0], j / 8), mix(corner.exit[1], nextEnter[1], j / 8)]);
        }
      });
      return points;
    }

    function cross(a, b) { return a[0] * b[1] - a[1] * b[0]; }

    function radial(vertices) {
      const outline = roundedVertices(vertices);
      return Array.from({ length: N }, function (_, i) {
        const angle = -Math.PI / 2 + i * TAU / N;
        const ray = [Math.cos(angle), Math.sin(angle)];
        let nearest = Infinity;
        outline.forEach(function (point, j) {
          const next = outline[(j + 1) % outline.length];
          const segment = [next[0] - point[0], next[1] - point[1]];
          const denominator = cross(ray, segment);
          if (Math.abs(denominator) < 1e-8) return;
          const distance = cross(point, segment) / denominator;
          const position = cross(point, ray) / denominator;
          if (distance > 0 && position >= -1e-7 && position <= 1 + 1e-7) nearest = Math.min(nearest, distance);
        });
        return [ray[0] * nearest, ray[1] * nearest];
      });
    }

    function circle(radius) {
      return Array.from({ length: N }, function (_, i) {
        const angle = -Math.PI / 2 + i * TAU / N;
        return [Math.cos(angle) * radius, Math.sin(angle) * radius];
      });
    }

    function contourStrands(contour) {
      return Array.from({ length: STRANDS }, function (_, strand) {
        return Array.from({ length: POINTS }, function (_, point) {
          return contour[(strand * 24 + point) % N];
        });
      });
    }

    function armStrands(angles) {
      return angles.map(function (angle) {
        const radians = angle * Math.PI / 180;
        return Array.from({ length: POINTS }, function (_, point) {
          const radius = 80 * point / (POINTS - 1);
          return [Math.cos(radians) * radius, Math.sin(radians) * radius];
        });
      });
    }

    const squareContour = radial([[-72, -72], [72, -72], [72, 72], [-72, 72]]);
    const forms = {
      brand: contourStrands(circle(76)),
      design: armStrands([-90, -90, 0, 0, 90, 90, 180, 180]),
      studio: contourStrands(squareContour.map(function (point) { return rotatePoint(point, 45); })),
      campaigns: armStrands([-90, -45, 0, 45, 90, 135, 180, 225]),
      production: contourStrands(radial([[-65, -78], [65, 0], [-65, 78]])),
      xperience: armStrands([-45, -45, 45, 45, 135, 135, 225, 225])
    };

    const paths = Array.from({ length: STRANDS }, function () {
      const path = document.createElementNS(NS, "path");
      path.setAttribute("fill", "none");
      path.setAttribute("stroke", "currentColor");
      path.setAttribute("stroke-width", STROKE);
      path.setAttribute("stroke-linecap", "round");
      path.setAttribute("stroke-linejoin", "round");
      svg.append(path);
      return path;
    });

    function setPath(path, points) {
      path.setAttribute("d", "M" + points.map(function (point) {
        return point[0].toFixed(3) + "," + point[1].toFixed(3);
      }).join("L"));
    }

    function drawSymbol(key) {
      forms[key].forEach(function (strand, i) { setPath(paths[i], strand); });
    }

    function drawTransition(fromKey, toKey, progress) {
      const source = forms[fromKey];
      const target = forms[toKey];
      const q = clamp01(progress);
      source.forEach(function (strand, i) {
        const delay = (i % 4) * 0.012;
        const local = lock((q - delay) / (1 - 0.036));
        const orbit = Math.sin(Math.PI * local);
        const rotation = (i % 2 ? 1 : -1) * 8 * orbit;
        const angle = -Math.PI / 2 + (i + 0.5) * TAU / STRANDS;
        const offset = 4.5 * orbit;
        const points = strand.map(function (point, j) {
          const destination = target[i][j];
          const blended = [mix(point[0], destination[0], local), mix(point[1], destination[1], local)];
          const rotated = rotatePoint(blended, rotation);
          return [rotated[0] + Math.cos(angle) * offset, rotated[1] + Math.sin(angle) * offset];
        });
        setPath(paths[i], points);
      });
    }

    function setActive(index) {
      if (index === activeIndex) return;
      activeIndex = index;
      cards.forEach(function (card, i) {
        card.classList.toggle("is-active", i === index);
        card.setAttribute("aria-current", i === index ? "step" : "false");
      });
      if (index < 0) {
        if (indexLabel) indexLabel.textContent = "00 / " + String(cards.length).padStart(2, "0");
        if (nameLabel) nameLabel.textContent = "scroll to explore";
        return;
      }
      if (indexLabel) indexLabel.textContent = String(index + 1).padStart(2, "0") + " / " + String(cards.length).padStart(2, "0");
      if (nameLabel) nameLabel.textContent = cards[index].querySelector(".svc-card__name").textContent;
    }

    function draw(index, progress, visibility) {
      const currentKey = cards[index].dataset.service;
      const next = Math.min(index + 1, cards.length - 1);
      const nextKey = cards[next].dataset.service;
      if (next === index) drawSymbol(currentKey);
      else drawTransition(currentKey, nextKey, reduceMotion ? (progress >= 0.5 ? 1 : 0) : progress);
      svg.classList.toggle("is-visible", visibility > 0.02);
      svg.style.opacity = String(visibility);
    }

    function update() {
      ticking = false;
      const sectionRect = section.getBoundingClientRect();
      if (sectionRect.top >= innerHeight || sectionRect.bottom <= 0) {
        setActive(-1);
        svg.classList.remove("is-visible");
        svg.style.opacity = "0";
        return;
      }

      const target = innerHeight * 0.58;
      const centers = cards.map(function (card) {
        const rect = card.getBoundingClientRect();
        return rect.top + rect.height / 2;
      });
      const firstRect = cards[0].getBoundingClientRect();
      if (target < centers[0]) {
        const visibility = smooth((innerHeight * 0.84 - firstRect.top) / (innerHeight * 0.24));
        draw(0, 0, visibility);
        setActive(visibility > 0.45 ? 0 : -1);
        return;
      }

      let index = cards.length - 1;
      let progress = 0;
      for (let i = 0; i < centers.length - 1; i++) {
        if (target <= centers[i + 1]) {
          index = i;
          progress = clamp01((target - centers[i]) / (centers[i + 1] - centers[i]));
          break;
        }
      }
      draw(index, progress, 1);
      setActive(Math.min(index + (progress >= 0.5 ? 1 : 0), cards.length - 1));
    }

    function queueUpdate() {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(update);
      setTimeout(function () { if (ticking) update(); }, 80);
    }

    window.addEventListener("scroll", queueUpdate, { passive: true });
    window.addEventListener("resize", queueUpdate);
    queueUpdate();
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
