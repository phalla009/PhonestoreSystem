document.addEventListener("DOMContentLoaded", () => {
    // Show dashboard if exists
    const dashboard = document.getElementById("dashboard");
    if (dashboard) dashboard.style.display = "block";

    // Dropdown menu toggle
    const dropdowns = document.querySelectorAll("li.menu-container");
    dropdowns.forEach((dropdown) => {
        dropdown.addEventListener("click", (e) => {
            if (!e.target.closest(".submenu a")) {
                dropdown.classList.toggle("open");
            }
        });
    });

    // Show and auto-hide success message with fade
    const successDiv = document.getElementById("successMessage");
    if (successDiv) {
        successDiv.style.display = "block";
        successDiv.style.opacity = "1";
        successDiv.style.transition = "opacity 0.2s ease";
        setTimeout(() => {
            successDiv.style.opacity = "0";
            setTimeout(() => {
                successDiv.style.display = "none";
            }, 250);
        }, 1500);
    }
});

// document.addEventListener("DOMContentLoaded", function () {
//     const modal = document.getElementById("categoryModal");
//     const modalBody = document.getElementById("modalBody");
//     const closeModal = document.getElementById("closeModal");

//     // Open Create Modal
//     const openCreateBtn = document.getElementById("openCreateModal");
//     if (openCreateBtn) {
//         openCreateBtn.addEventListener("click", function (e) {
//             e.preventDefault();
//             openModal(this.href);
//         });
//     }

//     // Open Edit Modal (multiple buttons)
//     document.querySelectorAll(".openEditModal").forEach(function (btn) {
//         btn.addEventListener("click", function (e) {
//             e.preventDefault();
//             openModal(this.href);
//         });
//     });

//     // Open Show Modal (multiple buttons)
//     document.querySelectorAll(".openShowModal").forEach(function (btn) {
//         btn.addEventListener("click", function (e) {
//             e.preventDefault();
//             openModal(this.href);
//         });
//     });

//     // Handle modal closing (outer span)
//     if (closeModal) {
//         closeModal.addEventListener("click", function () {
//             modal.style.display = "none";
//             modalBody.innerHTML = "";
//         });
//     }

//     // Close modal when clicking outside of it
//     window.addEventListener("click", function (e) {
//         if (e.target === modal) {
//             modal.style.display = "none";
//             modalBody.innerHTML = "";
//         }
//     });

//     function openModal(url) {
//         fetch(url)
//             .then((res) => res.text())
//             .then((html) => {
//                 modalBody.innerHTML = html;
//                 modal.style.display = "block";

//                 // Bind close button inside dynamically loaded content
//                 const innerClose = modalBody.querySelector("#closeModal");
//                 if (innerClose) {
//                     innerClose.addEventListener("click", () => {
//                         modal.style.display = "none";
//                         modalBody.innerHTML = "";
//                     });
//                 }
//             });
//     }
// });
