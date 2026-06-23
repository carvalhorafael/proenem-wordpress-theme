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
