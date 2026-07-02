import "./styles/main.css";
import { enhanceProenemWeb } from "@carvalhorafael/proenem-web";

document.documentElement.classList.add("proenem-js");
enhanceProenemWeb(document);

document.querySelectorAll("[data-pro-home-navbar]").forEach((navbar) => {
  const toggle = navbar.querySelector(".pro-home-navbar-toggle");

  if (!toggle) {
    return;
  }

  toggle.addEventListener("click", () => {
    const isOpen = navbar.classList.toggle("is-open");

    toggle.setAttribute("aria-expanded", String(isOpen));
  });
});

document.querySelectorAll("[data-pro-home-pillars-slider]").forEach((slider) => {
  const cards = Array.from(slider.querySelectorAll("[data-pro-home-pillar-card]"));
  const previousButton = slider.querySelector("[data-pro-home-pillars-prev]");
  const nextButton = slider.querySelector("[data-pro-home-pillars-next]");

  if (cards.length < 2) {
    return;
  }

  let activeIndex = Math.max(
    0,
    cards.findIndex((card) => card.classList.contains("is-active")),
  );

  const render = () => {
    const previousIndex = (activeIndex - 1 + cards.length) % cards.length;
    const nextIndex = (activeIndex + 1) % cards.length;

    cards.forEach((card, index) => {
      card.classList.toggle("is-active", index === activeIndex);
      card.classList.toggle("is-prev", index === previousIndex);
      card.classList.toggle("is-next", index === nextIndex);
      card.classList.toggle(
        "is-hidden",
        index !== activeIndex && index !== previousIndex && index !== nextIndex,
      );
      card.setAttribute("aria-hidden", String(index !== activeIndex));
    });
  };

  previousButton?.addEventListener("click", () => {
    activeIndex = (activeIndex - 1 + cards.length) % cards.length;
    render();
  });

  nextButton?.addEventListener("click", () => {
    activeIndex = (activeIndex + 1) % cards.length;
    render();
  });

  render();
});

document.querySelectorAll("[data-pro-home-platform-tabs]").forEach((section) => {
  const tabs = Array.from(section.querySelectorAll("[data-pro-home-platform-tab]"));
  const screen = section.querySelector("[data-pro-home-platform-screen]");
  const title = screen?.querySelector("[data-pro-home-platform-title]");
  const body = screen?.querySelector("[data-pro-home-platform-body]");
  const url = screen?.querySelector("[data-pro-home-platform-url]");
  const bulletList = screen?.querySelector("[data-pro-home-platform-bullets]");

  if (!tabs.length || !screen || !title || !body || !url || !bulletList) {
    return;
  }

  const renderBullets = (items) => {
    bulletList.replaceChildren(
      ...items.map((item) => {
        const bullet = document.createElement("li");
        bullet.textContent = item;

        return bullet;
      }),
    );
  };

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((currentTab) => {
        const isActive = currentTab === tab;

        currentTab.classList.toggle("is-active", isActive);
        currentTab.setAttribute("aria-selected", String(isActive));
      });

      title.textContent = tab.dataset.title || "";
      body.textContent = tab.dataset.body || "";
      url.textContent = tab.dataset.url || "";

      try {
        renderBullets(JSON.parse(tab.dataset.bullets || "[]"));
      } catch {
        renderBullets([]);
      }
    });
  });
});

document.querySelectorAll("[data-pro-home-testimonials-slider]").forEach((slider) => {
  const track = slider.querySelector("[data-pro-home-testimonials-track]");
  const cards = Array.from(track?.querySelectorAll("[data-pro-home-testimonial-card]") || []);
  const previousButton = slider.querySelector("[data-pro-home-testimonials-prev]");
  const nextButton = slider.querySelector("[data-pro-home-testimonials-next]");
  const intervalMs = 3800;

  if (!track || cards.length < 2) {
    return;
  }

  const cloneCard = (card) => {
    const clone = card.cloneNode(true);

    clone.classList.remove("is-active");
    clone.classList.add("is-clone");
    clone.setAttribute("aria-hidden", "true");

    return clone;
  };

  const beforeClones = document.createDocumentFragment();
  const afterClones = document.createDocumentFragment();

  cards.forEach((card) => {
    beforeClones.append(cloneCard(card));
    afterClones.append(cloneCard(card));
  });

  track.prepend(beforeClones);
  track.append(afterClones);

  const allCards = Array.from(track.querySelectorAll("[data-pro-home-testimonial-card]"));
  const originalOffset = cards.length;
  const resetDelayMs = 650;
  let activeIndex = Math.max(
    0,
    cards.findIndex((card) => card.classList.contains("is-active")),
  );
  let visualIndex = originalOffset + activeIndex;
  let intervalId = null;
  let resetTimeoutId = null;

  const centerCard = (card, behavior) => {
    track.scrollTo({
      behavior,
      left: card.offsetLeft - (track.clientWidth - card.clientWidth) / 2,
    });
  };

  const setActiveCard = () => {
    allCards.forEach((card, index) => {
      card.classList.toggle("is-active", index === visualIndex);
    });
  };

  const resetIfNeeded = () => {
    if (visualIndex >= originalOffset + cards.length) {
      visualIndex = originalOffset;
    } else if (visualIndex < originalOffset) {
      visualIndex = originalOffset + cards.length - 1;
    } else {
      return;
    }

    activeIndex = (visualIndex - originalOffset + cards.length) % cards.length;
    setActiveCard();
    centerCard(allCards[visualIndex], "auto");
  };

  const render = (behavior = "smooth") => {
    window.clearTimeout(resetTimeoutId);
    activeIndex = (visualIndex - originalOffset + cards.length) % cards.length;
    setActiveCard();
    centerCard(allCards[visualIndex], behavior);

    if (behavior === "auto") {
      resetIfNeeded();
      return;
    }

    resetTimeoutId = window.setTimeout(resetIfNeeded, resetDelayMs);
  };

  const stopAutoplay = () => {
    if (!intervalId) {
      return;
    }

    window.clearInterval(intervalId);
    intervalId = null;
  };

  const startAutoplay = () => {
    stopAutoplay();

    intervalId = window.setInterval(() => {
      visualIndex += 1;
      render();
    }, intervalMs);
  };

  previousButton?.addEventListener("click", () => {
    visualIndex -= 1;
    render();
    startAutoplay();
  });

  nextButton?.addEventListener("click", () => {
    visualIndex += 1;
    render();
    startAutoplay();
  });

  slider.addEventListener("mouseenter", stopAutoplay);
  slider.addEventListener("mouseleave", startAutoplay);
  slider.addEventListener("focusin", stopAutoplay);
  slider.addEventListener("focusout", startAutoplay);

  window.requestAnimationFrame(() => render("auto"));
  startAutoplay();
});
