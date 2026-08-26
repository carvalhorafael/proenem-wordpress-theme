import "./styles/main.css";
import { enhanceProenemWeb } from "@carvalhorafael/proenem-web";

document.documentElement.classList.add("proenem-js");
enhanceProenemWeb(document);

document.querySelectorAll("[data-pro-home-navbar]").forEach((navbar) => {
  const toggle = navbar.querySelector(".pro-home-navbar-toggle");
  const mobileViewport = window.matchMedia("(max-width: 760px)");
  const submenuToggles = Array.from(
    navbar.querySelectorAll(".pro-home-navbar-submenu-toggle"),
  );

  if (!toggle) {
    return;
  }

  const setSubmenuExpanded = (submenuToggle, expanded) => {
    const item = submenuToggle.closest(".pen-navbar__item");
    const primaryTrigger = item?.querySelector(
      ":scope > .pen-navbar__link, :scope > .pen-navbar__action",
    );

    item?.classList.toggle("is-submenu-open", expanded);
    submenuToggle.setAttribute("aria-expanded", String(expanded));
    primaryTrigger?.setAttribute("aria-expanded", String(expanded));
  };

  const toggleSubmenu = (submenuToggle) => {
    const item = submenuToggle.closest(".pen-navbar__item");

    if (!item) {
      return;
    }

    const willOpen = !item.classList.contains("is-submenu-open");

    submenuToggles.forEach((currentToggle) => {
      setSubmenuExpanded(currentToggle, currentToggle === submenuToggle && willOpen);
    });
  };

  toggle.addEventListener("click", () => {
    const isOpen = navbar.classList.toggle("is-open");

    toggle.setAttribute("aria-expanded", String(isOpen));

    if (!isOpen) {
      submenuToggles.forEach((submenuToggle) => setSubmenuExpanded(submenuToggle, false));
    }
  });

  submenuToggles.forEach((submenuToggle) => {
    const primaryTrigger = submenuToggle
      .closest(".pen-navbar__item")
      ?.querySelector(":scope > .pen-navbar__link, :scope > .pen-navbar__action");

    primaryTrigger?.setAttribute("aria-expanded", "false");
    primaryTrigger?.addEventListener("click", (event) => {
      if (!mobileViewport.matches) {
        return;
      }

      event.preventDefault();
      toggleSubmenu(submenuToggle);
    });

    submenuToggle.addEventListener("click", () => {
      const item = submenuToggle.closest(".pen-navbar__item");

      if (!item) {
        return;
      }

      toggleSubmenu(submenuToggle);
    });
  });
});

const persistentMobileActions = Array.from(
  document.querySelectorAll("[data-pro-mobile-persistent-action]"),
);

if (persistentMobileActions.length) {
  const mobileViewport = window.matchMedia("(max-width: 760px)");
  let updateFrame = null;

  document.documentElement.classList.add("has-pro-mobile-persistent-action");

  const updatePersistentMobileActions = () => {
    let hasVisibleAction = false;
    const menuIsOpen = Array.from(document.querySelectorAll("[data-pro-home-navbar]")).some(
      (navbar) => navbar.classList.contains("is-open"),
    );

    persistentMobileActions.forEach((action) => {
      const threshold = Number.parseInt(action.dataset.scrollThreshold || "600", 10);
      const persistentLink = action.querySelector("a[href]");
      const isVisibleInViewport = (element) => {
        const bounds = element.getBoundingClientRect();
        const visibleHeight = Math.min(bounds.bottom, innerHeight) - Math.max(bounds.top, 0);

        return (
          bounds.width > 0 &&
          bounds.height > 0 &&
          visibleHeight >= Math.min(bounds.height * 0.5, 32)
        );
      };
      const inlinePrimaryIsVisible = persistentLink
        ? Array.from(document.querySelectorAll("a[href]")).some((link) => {
            if (link === persistentLink || link.href !== persistentLink.href) {
              return false;
            }

            return isVisibleInViewport(link);
          })
        : false;
      const purchaseActionIsVisible = Array.from(
        document.querySelectorAll(".pen-pricing-section .pen-action-link"),
      ).some(isVisibleInViewport);
      const shouldShow =
        mobileViewport.matches &&
        window.scrollY >= threshold &&
        !menuIsOpen &&
        !inlinePrimaryIsVisible &&
        !purchaseActionIsVisible;

      action.hidden = !shouldShow;
      hasVisibleAction ||= shouldShow;
    });

    document.documentElement.classList.toggle(
      "is-pro-mobile-persistent-action-visible",
      hasVisibleAction,
    );

    updateFrame = null;
  };

  const schedulePersistentMobileActionUpdate = () => {
    if (updateFrame !== null) {
      return;
    }

    updateFrame = window.requestAnimationFrame(updatePersistentMobileActions);
  };

  window.addEventListener("scroll", schedulePersistentMobileActionUpdate, { passive: true });
  window.addEventListener("resize", schedulePersistentMobileActionUpdate);
  document.addEventListener("click", schedulePersistentMobileActionUpdate);
  mobileViewport.addEventListener("change", schedulePersistentMobileActionUpdate);
  updatePersistentMobileActions();
}

document.querySelectorAll("[data-pro-home-pillars-slider]").forEach((slider) => {
  const cards = Array.from(slider.querySelectorAll("[data-pro-home-pillar-card]"));
  const previousButton = slider.querySelector("[data-pro-home-pillars-prev]");
  const nextButton = slider.querySelector("[data-pro-home-pillars-next]");
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const intervalMs = 4800;

  if (cards.length < 2) {
    return;
  }

  let activeIndex = Math.max(
    0,
    cards.findIndex((card) => card.classList.contains("is-active")),
  );
  let intervalId = null;
  let pointerStartX = null;

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

  const stopAutoplay = () => {
    if (!intervalId) {
      return;
    }

    window.clearInterval(intervalId);
    intervalId = null;
  };

  const startAutoplay = () => {
    stopAutoplay();

    if (reducedMotion.matches || document.hidden) {
      return;
    }

    intervalId = window.setInterval(() => {
      activeIndex = (activeIndex + 1) % cards.length;
      render();
    }, intervalMs);
  };

  const move = (direction) => {
    activeIndex = (activeIndex + direction + cards.length) % cards.length;
    render();
    startAutoplay();
  };

  previousButton?.addEventListener("click", () => {
    move(-1);
  });

  nextButton?.addEventListener("click", () => {
    move(1);
  });

  slider.addEventListener("pointerdown", (event) => {
    const isNavigationControl =
      event.target instanceof Element &&
      event.target.closest(
        "[data-pro-home-pillars-prev], [data-pro-home-pillars-next]",
      );

    if (!event.isPrimary || isNavigationControl) {
      return;
    }

    pointerStartX = event.clientX;
    slider.setPointerCapture?.(event.pointerId);
    stopAutoplay();
  });

  slider.addEventListener("pointerup", (event) => {
    if (pointerStartX === null || !event.isPrimary) {
      return;
    }

    const distance = event.clientX - pointerStartX;

    pointerStartX = null;

    if (Math.abs(distance) >= 42) {
      move(distance < 0 ? 1 : -1);
      return;
    }

    startAutoplay();
  });

  slider.addEventListener("pointercancel", () => {
    pointerStartX = null;
    startAutoplay();
  });

  slider.addEventListener("mouseenter", stopAutoplay);
  slider.addEventListener("mouseleave", startAutoplay);
  slider.addEventListener("focusin", stopAutoplay);
  slider.addEventListener("focusout", startAutoplay);

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      stopAutoplay();
      return;
    }

    startAutoplay();
  });

  render();
  startAutoplay();
});

document.querySelectorAll("[data-pro-home-platform-tabs]").forEach((section) => {
  const tabList = section.querySelector(".pro-home-platform-tabs");
  const tabs = Array.from(section.querySelectorAll("[data-pro-home-platform-tab]"));
  const screen = section.querySelector("[data-pro-home-platform-screen]");
  const title = screen?.querySelector("[data-pro-home-platform-title]");
  const body = screen?.querySelector("[data-pro-home-platform-body]");
  const url = screen?.querySelector("[data-pro-home-platform-url]");
  const bulletList = screen?.querySelector("[data-pro-home-platform-bullets]");
  const image = screen?.querySelector("[data-pro-home-platform-image]");
  const previousButton = section.querySelector("[data-pro-home-platform-prev]");
  const nextButton = section.querySelector("[data-pro-home-platform-next]");

  if (!tabList || !tabs.length || !screen || !title || !body || !url || !bulletList || !image) {
    return;
  }

  const revealTab = (tab, behavior = "smooth") => {
    if (tabList.scrollWidth <= tabList.clientWidth) {
      return;
    }

    tabList.scrollTo({
      behavior,
      left: tab.offsetLeft - (tabList.clientWidth - tab.clientWidth) / 2,
    });
  };

  const updateControls = () => {
    const maximumScroll = Math.max(0, tabList.scrollWidth - tabList.clientWidth);

    if (previousButton instanceof HTMLButtonElement) {
      previousButton.disabled = tabList.scrollLeft <= 2;
    }

    if (nextButton instanceof HTMLButtonElement) {
      nextButton.disabled = tabList.scrollLeft >= maximumScroll - 2;
    }
  };

  const scrollTabs = (direction) => {
    const firstItem = tabList.querySelector("li");
    const distance = firstItem?.getBoundingClientRect().width || tabList.clientWidth * 0.7;

    tabList.scrollBy({
      behavior: "smooth",
      left: direction * distance,
    });
  };

  previousButton?.addEventListener("click", () => scrollTabs(-1));
  nextButton?.addEventListener("click", () => scrollTabs(1));
  tabList.addEventListener("scroll", updateControls, { passive: true });
  window.addEventListener("resize", updateControls);

  const renderBullets = (items) => {
    bulletList.replaceChildren(
      ...items.map((item) => {
        const bullet = document.createElement("li");
        bullet.textContent = item;

        return bullet;
      }),
    );
  };

  const activateTab = (tab) => {
    tabs.forEach((currentTab) => {
      const isActive = currentTab === tab;

      currentTab.classList.toggle("is-active", isActive);
      currentTab.setAttribute("aria-selected", String(isActive));
      currentTab.setAttribute("tabindex", isActive ? "0" : "-1");
    });

    title.textContent = tab.dataset.title || "";
    body.textContent = tab.dataset.body || "";
    url.textContent = tab.dataset.url || "";
    image.alt = tab.dataset.imageAlt || "";
    image.srcset = tab.dataset.imageSrcset || "";
    image.src = tab.dataset.image || "";
    image.width = Number.parseInt(tab.dataset.imageWidth || "", 10) || image.width;
    image.height = Number.parseInt(tab.dataset.imageHeight || "", 10) || image.height;

    try {
      renderBullets(JSON.parse(tab.dataset.bullets || "[]"));
    } catch {
      renderBullets([]);
    }

    revealTab(tab);
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener("click", () => activateTab(tab));
    tab.addEventListener("keydown", (event) => {
      let targetIndex = null;

      if (["ArrowRight", "ArrowDown"].includes(event.key)) {
        targetIndex = (index + 1) % tabs.length;
      } else if (["ArrowLeft", "ArrowUp"].includes(event.key)) {
        targetIndex = (index - 1 + tabs.length) % tabs.length;
      } else if (event.key === "Home") {
        targetIndex = 0;
      } else if (event.key === "End") {
        targetIndex = tabs.length - 1;
      }

      if (targetIndex === null) {
        return;
      }

      event.preventDefault();
      tabs[targetIndex].focus();
      activateTab(tabs[targetIndex]);
    });
  });

  const activeTab = tabs.find((tab) => tab.classList.contains("is-active"));

  if (activeTab) {
    window.requestAnimationFrame(() => {
      revealTab(activeTab, "auto");
      window.requestAnimationFrame(updateControls);
    });
  } else {
    updateControls();
  }
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

// Offer countdown: the server renders a static fallback, and the live countdown
// only replaces it when JavaScript runs. Unit labels come from PHP so they stay
// translatable.
//
// Two modes. A deadline counts down to a date and ticks every minute. A duration
// counts down minutes from the first visit and ticks every second; the start is
// persisted so a reload continues instead of handing out a fresh window.
const readCountdownStart = (element, storage, key) => {
  if (!storage) {
    return Date.now();
  }

  try {
    const stored = Number.parseInt(storage.getItem(key) || "", 10);

    if (Number.isFinite(stored) && stored > 0) {
      return stored;
    }

    const now = Date.now();
    storage.setItem(key, String(now));

    return now;
  } catch {
    return Date.now();
  }
};

document.querySelectorAll("[data-pro-countdown], [data-pro-countdown-duration]").forEach((element) => {
  const durationMinutes = Number.parseInt(element.dataset.proCountdownDuration || "", 10);
  const isDuration = Number.isFinite(durationMinutes) && durationMinutes > 0;
  let target = 0;

  if (isDuration) {
    let storage = null;

    try {
      storage = element.dataset.proCountdownScope === "visitor" ? window.localStorage : window.sessionStorage;
    } catch {
      storage = null;
    }

    const key = `pro-countdown-${element.dataset.proCountdownKey || "default"}`;

    target = readCountdownStart(element, storage, key) + durationMinutes * 60000;
  } else {
    target = Date.parse(element.dataset.proCountdown);
  }

  if (!Number.isFinite(target)) {
    return;
  }

  const fallback = element.querySelector("[data-pro-countdown-fallback]");
  const units = element.querySelector("[data-pro-countdown-units]");
  const days = element.querySelector("[data-pro-countdown-days]");
  const hours = element.querySelector("[data-pro-countdown-hours]");
  const minutes = element.querySelector("[data-pro-countdown-minutes]");
  const seconds = element.querySelector("[data-pro-countdown-seconds]");

  if (!fallback || !units || !minutes) {
    return;
  }

  const pad = (value) => String(value).padStart(2, "0");

  const expire = () => {
    const expiredLabel = element.dataset.proCountdownExpired || "";

    if (!expiredLabel) {
      return;
    }

    units.hidden = true;
    fallback.hidden = false;
    fallback.textContent = expiredLabel;
    element.closest("[data-pro-sticky]")?.classList.add("is-pro-countdown-expired");
  };

  const tick = () => {
    const remaining = target - Date.now();

    if (remaining <= 0) {
      expire();
      return false;
    }

    if (isDuration) {
      const totalSeconds = Math.floor(remaining / 1000);

      minutes.textContent = pad(Math.floor(totalSeconds / 60));

      if (seconds) {
        seconds.textContent = pad(totalSeconds % 60);
      }
    } else {
      const totalMinutes = Math.floor(remaining / 60000);

      if (days) {
        days.textContent = pad(Math.floor(totalMinutes / 1440));
      }

      if (hours) {
        hours.textContent = pad(Math.floor((totalMinutes % 1440) / 60));
      }

      minutes.textContent = pad(totalMinutes % 60);
    }

    fallback.hidden = true;
    units.hidden = false;

    return true;
  };

  if (tick()) {
    const timer = setInterval(() => {
      if (!tick()) {
        clearInterval(timer);
      }
    }, isDuration ? 1000 : 60000);
  }
});

// Sticky offer band: stays in place until the reader passes a share of the page,
// then pins to the top. The space it leaves is held by its own Elementor wrapper
// so pinning does not shift the page.
document.querySelectorAll("[data-pro-sticky]").forEach((band) => {
  const after = Number.parseInt(band.dataset.proStickyAfter || "", 10);
  const threshold = Number.isFinite(after) ? Math.min(Math.max(after, 0), 90) : 20;
  const holder = band.parentElement;
  let pinned = false;

  const scrolled = () => {
    const scrollable = document.documentElement.scrollHeight - window.innerHeight;

    return scrollable > 0 ? (window.scrollY / scrollable) * 100 : 0;
  };

  const update = () => {
    const shouldPin = scrolled() >= threshold;

    if (shouldPin === pinned) {
      return;
    }

    pinned = shouldPin;

    if (pinned && holder) {
      holder.style.minHeight = `${band.offsetHeight}px`;
    }

    band.classList.toggle("is-pro-sticky-pinned", pinned);

    if (!pinned && holder) {
      holder.style.minHeight = "";
    }
  };

  update();
  window.addEventListener("scroll", update, { passive: true });
  window.addEventListener("resize", update, { passive: true });
});

// Landing page video story: the embed is only requested after the click, so no
// third party is contacted while the page loads.
document.querySelectorAll("[data-pro-lp-video-play]").forEach((button) => {
  button.addEventListener("click", () => {
    const embedUrl = button.dataset.embedUrl;
    const stage = button.closest("[data-pro-lp-video]");

    if (!embedUrl || !stage) {
      return;
    }

    stage.replaceChildren(createTestimonialVideoIframe(embedUrl, button.getAttribute("aria-label") || ""));
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

document.querySelectorAll("[data-pro-testimonials-filter]").forEach((form) => {
  const grid = document.querySelector("[data-pro-testimonials-grid]");
  const count = document.querySelector("[data-pro-testimonials-count]");
  const emptyState = document.querySelector("[data-pro-testimonials-empty]");
  const clearLink = form.querySelector("[data-pro-testimonials-clear]");
  const cards = Array.from(document.querySelectorAll("[data-pro-testimonial-card]"));
  const checkboxes = Array.from(form.querySelectorAll('input[name="depoimento_categoria[]"]'));

  if (!grid || !cards.length || !checkboxes.length) {
    return;
  }

  const getCardCategories = (card) => {
    try {
      return JSON.parse(card.dataset.testimonialCategories || "[]");
    } catch {
      return [];
    }
  };

  const updateUrl = (selectedCategories) => {
    const url = new URL(window.location.href);

    url.searchParams.delete("depoimento_categoria[]");
    url.searchParams.delete("depoimento_categoria");

    selectedCategories.forEach((category) => {
      url.searchParams.append("depoimento_categoria[]", category);
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

const copyTextToClipboard = async (text) => {
  if (navigator.clipboard?.writeText) {
    await navigator.clipboard.writeText(text);
    return;
  }

  const textarea = document.createElement("textarea");

  textarea.value = text;
  textarea.setAttribute("readonly", "");
  textarea.style.position = "fixed";
  textarea.style.opacity = "0";
  document.body.append(textarea);
  textarea.select();

  const copied = document.execCommand("copy");

  textarea.remove();

  if (!copied) {
    throw new Error("Copy command failed");
  }
};

document.querySelectorAll("[data-pro-testimonial-share]").forEach((shareDetails) => {
  const summary = shareDetails.querySelector("summary");
  const copyButton = shareDetails.querySelector("[data-pro-testimonial-copy-link]");
  const status = shareDetails.querySelector("[data-pro-testimonial-share-status]");
  const shareData = {
    title: shareDetails.dataset.shareTitle || document.title,
    text: shareDetails.dataset.shareText || "",
    url: shareDetails.dataset.shareUrl || window.location.href,
  };

  summary?.addEventListener("click", async (event) => {
    if (typeof navigator.share !== "function") {
      return;
    }

    event.preventDefault();

    try {
      await navigator.share(shareData);
    } catch (error) {
      if (error?.name !== "AbortError") {
        shareDetails.open = true;
      }
    }
  });

  copyButton?.addEventListener("click", async () => {
    const copyLabel = copyButton.dataset.copyLabel || copyButton.textContent;
    const copiedLabel = copyButton.dataset.copiedLabel || copyLabel;
    const errorLabel = copyButton.dataset.copyErrorLabel || copyLabel;

    try {
      await copyTextToClipboard(shareData.url);
      copyButton.textContent = copiedLabel;

      if (status) {
        status.textContent = copiedLabel;
      }
    } catch {
      copyButton.textContent = errorLabel;

      if (status) {
        status.textContent = errorLabel;
      }
    }

    window.setTimeout(() => {
      copyButton.textContent = copyLabel;
    }, 2400);
  });
});
