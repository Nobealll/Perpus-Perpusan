document.addEventListener("DOMContentLoaded", () => {
  // Tambahkan kolom pencarian pada setiap tabel data.
  document.querySelectorAll(".table-panel table").forEach((table, index) => {
    const body = table.tBodies[0];
    const tablePanel = table.closest(".table-panel");
    const tableScroll = table.closest(".table-scroll");

    if (!body || !tablePanel || !tableScroll) return;

    const rows = Array.from(body.rows);
    const searchForm = document.createElement("div");
    searchForm.className = "table-search";

    const label = document.createElement("label");
    label.htmlFor = `table-search-${index}`;
    label.textContent = "Cari data tabel";

    const input = document.createElement("input");
    input.type = "search";
    input.id = label.htmlFor;
    input.placeholder = "Ketik kata kunci…";
    input.setAttribute("aria-label", "Cari data pada tabel ini");

    searchForm.append(label, input);
    tablePanel.insertBefore(searchForm, tableScroll);

    const noResultsRow = document.createElement("tr");
    noResultsRow.className = "table-search-empty";
    noResultsRow.hidden = true;

    const noResultsCell = document.createElement("td");
    noResultsCell.className = "empty-state";
    noResultsCell.textContent = "Tidak ada data yang cocok.";
    noResultsRow.append(noResultsCell);
    body.append(noResultsRow);

    input.addEventListener("input", () => {
      const searchText = input.value.trim().toLocaleLowerCase("id");
      let matchingRows = 0;

      rows.forEach((row) => {
        // Baris pesan kosong dari halaman tetap tampil saat pencarian dikosongkan.
        if (row.querySelector(".empty-state")) {
          row.hidden = searchText !== "";
          return;
        }

        const rowText = row.textContent.toLocaleLowerCase("id");
        const matches = rowText.includes(searchText);
        row.hidden = !matches;

        if (matches) matchingRows += 1;
      });

      noResultsCell.colSpan = table.tHead?.rows[0]?.cells.length || 1;
      noResultsRow.hidden = searchText === "" || matchingRows > 0;
    });
  });

  // Tampilkan preview sampul sebelum formulir buku dikirim.
  const imageInput = document.querySelector("[data-image-preview]");

  if (imageInput) {
    const previewId = imageInput.dataset.imagePreview;
    const previewImage = document.getElementById(previewId);
    let previousPreviewUrl = null;

    imageInput.addEventListener("change", () => {
      const selectedFile = imageInput.files[0];
      if (!selectedFile || !previewImage) return;

      if (previousPreviewUrl) {
        URL.revokeObjectURL(previousPreviewUrl);
      }

      previousPreviewUrl = URL.createObjectURL(selectedFile);
      previewImage.src = previousPreviewUrl;
    });
  }

  // Tampilkan gambar placeholder jika file sampul tidak ditemukan.
  document.querySelectorAll("[data-image-fallback]").forEach((image) => {
    image.addEventListener("error", () => {
      image.src = image.dataset.imageFallback;
      image.removeAttribute("data-image-fallback");
    });
  });

  // Gunakan dialog Neobrutalism untuk aksi yang membutuhkan konfirmasi.
  const confirmDialog = document.getElementById("action-confirm-dialog");

  if (confirmDialog) {
    const confirmTitle = document.getElementById("action-confirm-title");
    const confirmDescription = document.getElementById(
      "action-confirm-description",
    );
    const cancelButton = confirmDialog.querySelector("[data-dialog-cancel]");
    const acceptButton = confirmDialog.querySelector("[data-dialog-confirm]");
    const confirmedForms = new WeakSet();
    let pendingForm = null;
    let previousFocus = null;

    const restoreFocus = () => {
      if (previousFocus && previousFocus.isConnected) {
        previousFocus.focus();
      }
      previousFocus = null;
    };

    document.querySelectorAll("form[data-confirm]").forEach((form) => {
      form.addEventListener("submit", (event) => {
        if (confirmedForms.has(form)) {
          confirmedForms.delete(form);
          return;
        }

        event.preventDefault();
        pendingForm = form;
        previousFocus = document.activeElement;

        confirmTitle.textContent =
          form.dataset.confirmTitle || "Konfirmasi tindakan";
        confirmDescription.textContent =
          form.dataset.confirmDescription ||
          form.dataset.confirm ||
          "Apakah Anda yakin ingin melanjutkan?";

        const actionLabel = form.dataset.confirmAction || "Lanjutkan";
        const actionStyle =
          form.dataset.confirmVariant === "danger" ? "danger" : "primary";

        acceptButton.textContent = actionLabel;
        acceptButton.className = `ui-button ui-button-${actionStyle}`;
        confirmDialog.showModal();
      });
    });

    cancelButton.addEventListener("click", () => {
      pendingForm = null;
      confirmDialog.close();
    });

    acceptButton.addEventListener("click", () => {
      if (!pendingForm) return;

      const form = pendingForm;
      pendingForm = null;
      confirmedForms.add(form);
      confirmDialog.close();
      form.requestSubmit();
    });

    confirmDialog.addEventListener("cancel", () => {
      pendingForm = null;
    });

    confirmDialog.addEventListener("close", restoreFocus);
  }
});
