(function () {
  "use strict";

  document.querySelectorAll("[data-threekg-showreel]").forEach(function (root) {
    const reelFrame = root.querySelector(".showreel__frame");
    const reelVideo = root.querySelector("[data-showreel-video]");
    const reelPlay = root.querySelector("[data-showreel-play]");
    if (!reelFrame || !reelVideo || !reelPlay) return;
    function reelToggle() {
      if (reelVideo.readyState < 2) {
        const label = reelPlay.querySelector(".mono");
        if (label) label.textContent = "video unavailable";
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

    reelPlay.addEventListener("click", function (e) {
      e.stopPropagation();
      reelToggle();
    });
    reelPlay.addEventListener("keydown", function (e) {
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
      const label = reelPlay.querySelector(".mono");
      if (label) label.textContent = "video unavailable";
    });
  });
})();
