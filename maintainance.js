document.addEventListener("DOMContentLoaded", function () {
    const tableBtn = document.getElementById("table-view-btn");
    const pictureBtn = document.getElementById("picture-view-btn");
    const tableView = document.getElementById("table-view");
    const pictureView = document.getElementById("picture-view");

    tableBtn.addEventListener("click", () => {
        tableView.style.display = "block";
        pictureView.style.display = "none";
    });

    pictureBtn.addEventListener("click", () => {
        tableView.style.display = "none";
        pictureView.style.display = "block";
    });
});
