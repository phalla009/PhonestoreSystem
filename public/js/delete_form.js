document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("deleteConfirmModal");
    const deleteForm = document.getElementById("deleteForm");
    const closeModalBtn = document.getElementById("deleteModalClose");
    const cancelDeleteBtn = document.getElementById("cancelDelete");
    const deleteButtons = document.querySelectorAll(".openDeleteModal");

    deleteButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
            const action = btn.getAttribute("data-action");
            deleteForm.action = action;
            modal.style.display = "block";
        });
    });

    closeModalBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    cancelDeleteBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});
