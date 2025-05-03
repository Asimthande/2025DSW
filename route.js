document.addEventListener("DOMContentLoaded", () => {
  const tableView = document.getElementById("table-view");
  const pictureView = document.getElementById("picture-view");

  document.getElementById("table-view-btn").addEventListener("click", () => {
    tableView.style.display = "block";
    pictureView.style.display = "none";
  });

  document.getElementById("picture-view-btn").addEventListener("click", () => {
    tableView.style.display = "none";
    pictureView.style.display = "block";
  });
});
