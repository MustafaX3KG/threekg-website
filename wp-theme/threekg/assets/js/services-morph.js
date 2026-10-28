(function () {
  "use strict";

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

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

    /* belt-and-braces: whenever the section (or its edges) are near the
       viewport, force an update and make the stage symbol visible even if
       scroll-position logic stalls (fast wheel, momentum scrolling) */
    if ("IntersectionObserver" in window) {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          svg.classList.add("is-visible");
          queueUpdate();
        });
      }, { rootMargin: "25% 0px 25% 0px" }).observe(section);
    }

    queueUpdate();
  })();
})();
