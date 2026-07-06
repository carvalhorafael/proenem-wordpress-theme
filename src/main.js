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

const getYouTubeVideoId = (url) => {
  try {
    const parsedUrl = new URL(url, window.location.href);
    const host = parsedUrl.hostname.toLowerCase();
    const pathParts = parsedUrl.pathname.split("/").filter(Boolean);

    if (host.includes("youtu.be")) {
      return pathParts[0] || "";
    }

    if (!host.includes("youtube.com")) {
      return "";
    }

    if (parsedUrl.searchParams.get("v")) {
      return parsedUrl.searchParams.get("v");
    }

    if ((pathParts[0] === "embed" || pathParts[0] === "shorts") && pathParts[1]) {
      return pathParts[1];
    }
  } catch {
    return "";
  }

  return "";
};

const getTestimonialVideoEmbedUrl = (url) => {
  const youtubeId = getYouTubeVideoId(url);

  if (youtubeId) {
    return `https://www.youtube.com/embed/${encodeURIComponent(youtubeId)}?autoplay=1&rel=0`;
  }

  return url;
};

const createTestimonialVideoIframe = (embedUrl, title = "") => {
  const iframe = document.createElement("iframe");

  iframe.src = embedUrl;
  iframe.title = title;
  iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
  iframe.allowFullscreen = true;
  iframe.loading = "lazy";

  return iframe;
};

document.querySelectorAll("[data-pro-testimonial-play]").forEach((button) => {
  button.addEventListener("click", () => {
    const embedUrl = button.dataset.embedUrl;
    const video = button.closest("[data-pro-testimonial-video]");

    if (!embedUrl || !video) {
      return;
    }

    video.replaceChildren(createTestimonialVideoIframe(embedUrl, button.getAttribute("aria-label") || ""));
  });
});

document.querySelectorAll(".testimonials-card__video").forEach((link) => {
  const videoUrl = link.href;
  const youtubeId = getYouTubeVideoId(videoUrl);
  const media = link.closest(".testimonials-card__media");

  if (youtubeId) {
    link.style.setProperty("--testimonial-video-thumb", `url("https://img.youtube.com/vi/${encodeURIComponent(youtubeId)}/hqdefault.jpg")`);
    link.classList.add("has-video-thumbnail");
  }

  link.addEventListener("click", (event) => {
    const embedUrl = getTestimonialVideoEmbedUrl(videoUrl);

    if (!embedUrl || !media) {
      return;
    }

    event.preventDefault();
    media.replaceChildren(createTestimonialVideoIframe(embedUrl, link.textContent.trim()));
  });
});

document.querySelectorAll(".testimonials-block--slider, .testimonials-block--video-slider").forEach((slider) => {
  const track = slider.querySelector(".testimonials-block__items");
  const cards = Array.from(track?.querySelectorAll(".testimonials-card") || []);
  const intervalMs = 4200;

  if (!track || cards.length < 2 || slider.querySelector(".testimonials-block__controls")) {
    return;
  }

  let activeIndex = 0;
  let intervalId = null;
  let scrollTimeoutId = null;

  const getVisibleCount = () => {
    const firstCard = cards[0];

    if (!firstCard) {
      return 1;
    }

    return Math.max(1, Math.round(track.clientWidth / firstCard.getBoundingClientRect().width));
  };

  const controls = document.createElement("div");
  const previousButton = document.createElement("button");
  const nextButton = document.createElement("button");
  const dots = document.createElement("div");

  controls.className = "testimonials-block__controls";
  dots.className = "testimonials-block__dots";
  previousButton.className = "testimonials-block__control testimonials-block__control--prev";
  nextButton.className = "testimonials-block__control testimonials-block__control--next";
  previousButton.type = "button";
  nextButton.type = "button";
  previousButton.setAttribute("aria-label", "Depoimento anterior");
  nextButton.setAttribute("aria-label", "Próximo depoimento");
  previousButton.textContent = "‹";
  nextButton.textContent = "›";

  const dotButtons = cards.map((card, index) => {
    const dot = document.createElement("button");

    dot.className = "testimonials-block__dot";
    dot.type = "button";
    dot.setAttribute("aria-label", `Ir para depoimento ${index + 1}`);

    dots.append(dot);

    return dot;
  });

  controls.append(previousButton, dots, nextButton);
  slider.append(controls);

  const setActive = (index) => {
    activeIndex = (index + cards.length) % cards.length;

    cards.forEach((card, cardIndex) => {
      const isActive = cardIndex === activeIndex;

      card.classList.toggle("is-active", isActive);
      card.setAttribute("aria-hidden", String(!isActive));
    });

    dotButtons.forEach((dot, dotIndex) => {
      const isActive = dotIndex === activeIndex;

      dot.classList.toggle("is-active", isActive);
      dot.setAttribute("aria-current", isActive ? "true" : "false");
    });
  };

  const scrollToCard = (index, behavior = "smooth") => {
    const nextIndex = (index + cards.length) % cards.length;
    const card = cards[nextIndex];

    setActive(nextIndex);
    track.scrollTo({
      behavior,
      left: card.offsetLeft - track.offsetLeft,
    });
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
      scrollToCard(activeIndex + getVisibleCount());
    }, intervalMs);
  };

  previousButton.addEventListener("click", () => {
    scrollToCard(activeIndex - getVisibleCount());
    startAutoplay();
  });

  nextButton.addEventListener("click", () => {
    scrollToCard(activeIndex + getVisibleCount());
    startAutoplay();
  });

  dotButtons.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      scrollToCard(index);
      startAutoplay();
    });
  });

  track.addEventListener("scroll", () => {
    window.clearTimeout(scrollTimeoutId);
    scrollTimeoutId = window.setTimeout(() => {
      const closestIndex = cards.reduce((closest, card, index) => {
        const currentDistance = Math.abs(card.offsetLeft - track.offsetLeft - track.scrollLeft);
        const closestDistance = Math.abs(cards[closest].offsetLeft - track.offsetLeft - track.scrollLeft);

        return currentDistance < closestDistance ? index : closest;
      }, activeIndex);

      setActive(closestIndex);
    }, 120);
  });

  slider.addEventListener("mouseenter", stopAutoplay);
  slider.addEventListener("mouseleave", startAutoplay);
  slider.addEventListener("focusin", stopAutoplay);
  slider.addEventListener("focusout", startAutoplay);

  setActive(0);
  window.requestAnimationFrame(() => scrollToCard(0, "auto"));
  startAutoplay();
});

document.querySelectorAll("[data-pro-materials-filter]").forEach((form) => {
  const grid = document.querySelector("[data-pro-materials-grid]");
  const count = document.querySelector("[data-pro-materials-count]");
  const emptyState = document.querySelector("[data-pro-materials-empty]");
  const clearLink = form.querySelector("[data-pro-materials-clear]");
  const cards = Array.from(document.querySelectorAll("[data-pro-material-card]"));
  const checkboxes = Array.from(form.querySelectorAll('input[name="material_categoria[]"]'));

  if (!grid || !cards.length || !checkboxes.length) {
    return;
  }

  const getCardCategories = (card) => {
    try {
      return JSON.parse(card.dataset.materialCategories || "[]");
    } catch {
      return [];
    }
  };

  const updateUrl = (selectedCategories) => {
    const url = new URL(window.location.href);

    url.searchParams.delete("material_categoria[]");
    url.searchParams.delete("material_categoria");

    selectedCategories.forEach((category) => {
      url.searchParams.append("material_categoria[]", category);
    });

    window.history.replaceState({}, "", url);
  };

  const render = () => {
    const selectedCategories = checkboxes
      .filter((checkbox) => checkbox.checked)
      .map((checkbox) => checkbox.value);
    let visibleCount = 0;

    cards.forEach((card) => {
      const cardCategories = getCardCategories(card);
      const isVisible =
        selectedCategories.length === 0 ||
        selectedCategories.some((category) => cardCategories.includes(category));

      card.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (count) {
      const countTemplate =
        visibleCount === 1
          ? count.dataset.countTemplateSingular || "%s"
          : count.dataset.countTemplatePlural || "%s";

      count.textContent = countTemplate.replace("%s", visibleCount.toLocaleString("pt-BR"));
    }

    if (emptyState) {
      emptyState.hidden = visibleCount !== 0;
    }

    if (clearLink) {
      clearLink.hidden = selectedCategories.length === 0;
    }

    updateUrl(selectedCategories);
  };

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    render();
  });

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", render);
  });

  clearLink?.addEventListener("click", (event) => {
    event.preventDefault();

    checkboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });

    render();
  });

  render();
});
