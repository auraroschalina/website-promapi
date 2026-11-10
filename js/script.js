document.addEventListener("DOMContentLoaded", () => {
  const nav = document.querySelector(".site-nav");
  const backToTop = document.getElementById("backToTop");
  const year = document.getElementById("year");
  if (year) year.textContent = new Date().getFullYear();

  const updateScroll = () => {
    nav?.classList.toggle("scrolled", window.scrollY > 20);
    backToTop?.classList.toggle("show", window.scrollY > 420);
  };
  window.addEventListener("scroll", updateScroll, { passive: true });
  updateScroll();

  backToTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));

  // Tutup menu navigasi setelah tautan diklik pada layar kecil.
  document.querySelectorAll("#mainNav .nav-link, #mainNav .dropdown-item").forEach(link => {
    link.addEventListener("click", () => {
      const menu = document.getElementById("mainNav");
      if (menu?.classList.contains("show") && window.bootstrap) {
        bootstrap.Collapse.getOrCreateInstance(menu).hide();
      }
    });
  });

  // Galeri demo: gunakan modal yang sama untuk seluruh item.
  const galleryModalEl = document.getElementById("galleryModal");
  const galleryModal = galleryModalEl && window.bootstrap ? new bootstrap.Modal(galleryModalEl) : null;
  document.querySelectorAll(".gallery-item").forEach((item, index) => {
    item.addEventListener("click", () => {
      const preview = galleryModalEl?.querySelector(".modal-gallery-preview");
      const caption = document.getElementById("galleryCaption");
      const backgrounds = [
        "linear-gradient(145deg,#102c4d 15%,#557a97 65%,#d0b44f)",
        "linear-gradient(135deg,#d2b653,#6d8496)",
        "linear-gradient(135deg,#2e536e,#a8b8c2)",
        "linear-gradient(135deg,#203c57,#bd9e4d)",
        "linear-gradient(135deg,#6d879a,#193b5a)"
      ];
      if (preview) preview.style.background = backgrounds[index % backgrounds.length];
      if (caption) caption.textContent = item.dataset.title || "Dokumentasi kegiatan";
      galleryModal?.show();
    });
  });
});