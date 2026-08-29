import AxeBuilder from "@axe-core/playwright";
import { expect, test } from "@playwright/test";

const SALES_PAGE = "/lp/homologacao-widgets-lp/";

/**
 * The sales page fixture depends on the sales-page plugin, which the base
 * wp-env config does not mount. Skip instead of failing when it is absent.
 */
const gotoSalesPage = async (page) => {
  const response = await page.goto(SALES_PAGE);
  const available = Boolean(response) && response.status() === 200;

  test.skip(!available, "A pagina de venda de fixture nao esta disponivel neste ambiente.");

  return available;
};

const relativeLuminance = ([r, g, b]) => {
  const channel = (value) => {
    const ratio = value / 255;

    return ratio <= 0.03928 ? ratio / 12.92 : ((ratio + 0.055) / 1.055) ** 2.4;
  };

  return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b);
};

const contrastRatio = (foreground, background) => {
  const first = relativeLuminance(foreground);
  const second = relativeLuminance(background);
  const lighter = Math.max(first, second);
  const darker = Math.min(first, second);

  return (lighter + 0.05) / (darker + 0.05);
};

const HOME_WIDGETS_PAGE = "/lp/checagem-widgets-home/";

/**
 * Todo widget que se declara faixa de secao ganha `.pro-section-host`, e o tema
 * solta a goteira do container do Elementor para ele. Sao duas formas de
 * container, caixa e largura total, e por um tempo so a caixa estava coberta:
 * a de largura total mantinha o padding de 10 px do Elementor e deixava duas
 * listras brancas nas laterais. Este teste cobre as duas formas.
 */
for (const [nome, url] of [
  ["widgets de venda", SALES_PAGE],
  ["widgets de home", HOME_WIDGETS_PAGE],
  ["kit de oferta completa", "/lp/kit-oferta-completa/"],
  ["kit de diferencial em foco", "/lp/kit-diferencial-em-foco/"],
  ["kit de metodo completo", "/lp/kit-metodo-completo/"],
]) {
  test(`section hosts span the full width on the page of ${nome}`, async ({ page }) => {
    const response = await page.goto(url);

    test.skip(
      !response || response.status() !== 200,
      "A pagina de fixture nao esta disponivel neste ambiente.",
    );

    const hosts = await page.evaluate(() =>
      [...document.querySelectorAll(".pro-section-host")].map((host) => {
        const rect = host.getBoundingClientRect();

        return { left: Math.round(rect.left), width: Math.round(rect.width) };
      }),
    );

    expect(hosts.length).toBeGreaterThan(0);

    const viewport = page.viewportSize();

    for (const host of hosts) {
      expect(host.left).toBe(0);
      expect(host.width).toBe(viewport.width);
    }

    const overflows = await page.evaluate(
      () => document.documentElement.scrollWidth > window.innerWidth,
    );

    expect(overflows).toBe(false);

    /* O container do Elementor e flex e traz `gap: 20px`, que abre uma costura
       entre cada par de faixas vizinhas. Faixa colada em faixa e contrato.
       Compara so vizinhas de DOM: duas faixas seguidas na lista podem estar em
       containers diferentes, e ai a distancia vertical entre elas e o conteudo
       que existe no meio, nao uma costura. */
    const costuras = await page.evaluate(() => {
      const hosts = [...document.querySelectorAll(".pro-section-host")];

      return hosts
        .map((host, index) => {
          const anterior = hosts[index - 1];

          if (!anterior || anterior.nextElementSibling !== host) {
            return null;
          }

          const vao = Math.round(
            host.getBoundingClientRect().top -
              anterior.getBoundingClientRect().bottom,
          );

          return Math.abs(vao) > 1 ? { indice: index, vao } : null;
        })
        .filter(Boolean);
    });

    expect(costuras).toEqual([]);
  });
}

/**
 * Cada tom de faixa declara superficie e cor de conteudo juntas, entao a
 * promessa e que qualquer texto dentro de qualquer tom da lista fechada
 * permaneca legivel. O que quebra esse contrato nao e o titulo, e o que herda:
 * selo, lista, microcopy e botao. Este teste mede todos.
 */
test("every closed-list section tone keeps every text readable", async ({ page }) => {
  const response = await page.goto("/lp/checagem-tons-de-secao/");

  test.skip(
    !response || response.status() !== 200,
    "A pagina de tons de secao nao esta disponivel neste ambiente.",
  );

  const faixas = await page.evaluate(() => {
    const canal = (valor) => {
      const escala = valor / 255;

      return escala <= 0.03928 ? escala / 12.92 : ((escala + 0.055) / 1.055) ** 2.4;
    };
    const luminancia = ([r, g, b]) => 0.2126 * canal(r) + 0.7152 * canal(g) + 0.0722 * canal(b);
    const numeros = (cor) => cor.match(/[\d.]+/g).map(Number);
    const fundoSolido = (elemento) => {
      let atual = elemento;

      while (atual) {
        const partes = numeros(getComputedStyle(atual).backgroundColor);

        if (partes && (partes.length < 4 || partes[3] > 0)) {
          return partes;
        }

        atual = atual.parentElement;
      }

      return [255, 255, 255];
    };
    const composto = (frente, fundo) => {
      const partes = numeros(frente);
      const alfa = partes.length > 3 ? partes[3] : 1;

      return [0, 1, 2].map((i) => alfa * partes[i] + (1 - alfa) * fundo[i]);
    };
    const razao = (frente, elemento) => {
      const fundo = fundoSolido(elemento);
      const primeira = luminancia(composto(frente, fundo));
      const segunda = luminancia(fundo);
      const claro = Math.max(primeira, segunda);
      const escuro = Math.min(primeira, segunda);

      return +((claro + 0.05) / (escuro + 0.05)).toFixed(2);
    };

    return [...document.querySelectorAll(".pro-sales-section--colored")].map((secao) => {
      const tom = [...secao.classList]
        .find((classe) => classe.startsWith("pro-sales-section--tone-"))
        .replace("pro-sales-section--tone-", "");
      const reprovados = [];

      secao.querySelectorAll("h1,h2,h3,p,li,span,.pen-button").forEach((elemento) => {
        const texto = (elemento.textContent || "").trim();

        if (!texto) {
          return;
        }

        if (elemento.children.length > 0 && !elemento.classList.contains("pen-button")) {
          return;
        }

        const contraste = razao(getComputedStyle(elemento).color, elemento);

        if (contraste < 4.5) {
          reprovados.push({ tom, contraste, texto: texto.slice(0, 40) });
        }
      });

      return { tom, reprovados };
    });
  });

  expect(faixas.length).toBeGreaterThan(0);
  expect(faixas.flatMap((faixa) => faixa.reprovados)).toEqual([]);
});

test("sales page bands span the full width of the viewport", async ({ page }) => {
  await gotoSalesPage(page);

  const bands = await page.evaluate(() =>
    [...document.querySelectorAll(".pro-sales-section")].map((band) => {
      const rect = band.getBoundingClientRect();

      return { left: Math.round(rect.left), width: Math.round(rect.width) };
    }),
  );

  expect(bands.length).toBeGreaterThan(0);

  const viewport = page.viewportSize();

  for (const band of bands) {
    expect(band.left).toBe(0);
    expect(band.width).toBe(viewport.width);
  }

  const overflows = await page.evaluate(
    () => document.documentElement.scrollWidth > window.innerWidth,
  );

  expect(overflows).toBe(false);
});

test("sales page bands carry their own vertical rhythm and stay contiguous", async ({ page }) => {
  await gotoSalesPage(page);

  const report = await page.evaluate(() => {
    const widgets = [...document.querySelectorAll(".elementor-widget")];

    const bandOf = (widget) => widget.querySelector(":scope > .pro-sales-section");

    const rhythm = widgets
      .map(bandOf)
      .filter(Boolean)
      .map((band) => {
        const styles = window.getComputedStyle(band);

        return {
          paddingTop: Number.parseFloat(styles.paddingTop) || 0,
          paddingBottom: Number.parseFloat(styles.paddingBottom) || 0,
        };
      });

    // Only bands that are neighbours in the document may be compared: the page
    // also holds widgets that are cards, not bands, and those create real gaps.
    const seams = [];

    for (let index = 1; index < widgets.length; index += 1) {
      const previous = bandOf(widgets[index - 1]);
      const current = bandOf(widgets[index]);

      if (!previous || !current) {
        continue;
      }

      const previousBottom = Math.round(previous.getBoundingClientRect().bottom + window.scrollY);
      const currentTop = Math.round(current.getBoundingClientRect().top + window.scrollY);

      if (previousBottom !== currentTop) {
        seams.push(`${previousBottom} -> ${currentTop}`);
      }
    }

    return { rhythm, seams };
  });

  expect(report.rhythm.length).toBeGreaterThan(0);

  for (const band of report.rhythm) {
    expect(band.paddingTop).toBeGreaterThan(0);
    expect(band.paddingBottom).toBeGreaterThan(0);
  }

  // A gap between neighbouring bands would show the page canvas as a seam.
  expect(report.seams).toEqual([]);
});

test("sales page keeps one h1 and unique element ids", async ({ page }) => {
  await gotoSalesPage(page);

  await expect(page.locator("h1")).toHaveCount(1);

  const report = await page.evaluate(() => {
    const ids = [...document.querySelectorAll("[id]")].map((element) => element.id);

    return {
      duplicated: ids.filter((id, index) => ids.indexOf(id) !== index),
      orphanLabels: [...document.querySelectorAll("[aria-labelledby]")]
        .map((element) => element.getAttribute("aria-labelledby"))
        .filter((id) => !document.getElementById(id)),
    };
  });

  expect(report.duplicated).toEqual([]);
  expect(report.orphanLabels).toEqual([]);
});

test("every component inside a brand band keeps a readable colour pair", async ({ page }) => {
  await gotoSalesPage(page);

  const samples = await page.evaluate(() => {
    const parse = (value) => (value.match(/[\d.]+/g) || []).slice(0, 3).map(Number);
    const surfaceOf = (element) => {
      let node = element;

      while (node) {
        const background = window.getComputedStyle(node).backgroundColor;

        if (background && background !== "rgba(0, 0, 0, 0)") {
          return parse(background);
        }

        node = node.parentElement;
      }

      return [255, 255, 255];
    };

    return [...document.querySelectorAll(".pro-sales-section--tone-brand")].flatMap((band) =>
      [...band.querySelectorAll("h1, h2, h3, p, li, a, summary, strong")]
        .filter((element) => {
          const rect = element.getBoundingClientRect();

          return element.textContent.trim() !== "" && rect.width > 0 && rect.height > 0;
        })
        .map((element) => ({
          label: `${element.tagName}.${(element.className || "-").toString().split(" ")[0]}`,
          color: parse(window.getComputedStyle(element).color),
          surface: surfaceOf(element),
        })),
    );
  });

  expect(samples.length).toBeGreaterThan(0);

  const failures = samples
    .map((sample) => ({ ...sample, ratio: contrastRatio(sample.color, sample.surface) }))
    .filter((sample) => sample.ratio < 4.5)
    .map((sample) => `${sample.label} -> ${sample.ratio.toFixed(2)}`);

  expect(failures).toEqual([]);
});

test("video facade does not contact the provider before the click", async ({ page }) => {
  const providerRequests = [];

  page.on("request", (request) => {
    if (/youtube\.com|youtu\.be|player\.vimeo\.com/.test(request.url())) {
      providerRequests.push(request.url());
    }
  });

  await gotoSalesPage(page);

  const play = page.locator("[data-pro-lp-video-play]").first();

  test.skip((await play.count()) === 0, "A fixture nao tem widget de video com link preenchido.");

  await expect(page.locator("[data-pro-lp-video] iframe")).toHaveCount(0);
  expect(providerRequests).toEqual([]);

  const embedRequest = page.waitForRequest(/youtube\.com\/embed\//, { timeout: 10_000 });

  await play.click();

  await expect(page.locator("[data-pro-lp-video] iframe").first()).toHaveAttribute(
    "src",
    /youtube\.com\/embed\//,
  );
  await embedRequest;
});

test("sales page has no critical accessibility violations", async ({ page }) => {
  await gotoSalesPage(page);

  const results = await new AxeBuilder({ page })
    .withTags(["wcag2a", "wcag2aa", "wcag21a", "wcag21aa"])
    .analyze();

  const blocking = results.violations.filter((violation) =>
    ["critical", "serious"].includes(violation.impact),
  );

  expect(
    blocking.map((violation) => `${violation.id}: ${violation.nodes.length} no(s)`),
  ).toEqual([]);
});
