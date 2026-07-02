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
