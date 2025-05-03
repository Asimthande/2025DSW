document.addEventListener("DOMContentLoaded", () => {
  const tableBtn = document.getElementById("table-view");
  const listBtn = document.getElementById("list-view");

  const tableContainer = document.getElementById("table-view-container");
  const listContainer = document.getElementById("list-view-container");

  tableBtn.addEventListener("click", () => {
    tableContainer.style.display = "block";
    listContainer.style.display = "none";

    tableBtn.classList.add("active");
    listBtn.classList.remove("active");
  });

  listBtn.addEventListener("click", () => {
    tableContainer.style.display = "none";
    listContainer.style.display = "block";

    listBtn.classList.add("active");
    tableBtn.classList.remove("active");
  });
});
