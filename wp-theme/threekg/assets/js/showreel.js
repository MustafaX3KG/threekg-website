(function () {
  "use strict";

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

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
})();
