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
