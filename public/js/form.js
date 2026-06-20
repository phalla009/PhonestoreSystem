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

// ✅ reuse overlay ពី master layout
function showLoading(msg) {
    const ov = document.getElementById("loading-overlay");
    const lt = document.getElementById("loading-text");
    if (!ov) return;
    if (lt) lt.textContent = msg || "Loading...";
    ov.style.display = "flex";
}

// Page nav links — class "page-link-loading" មិន conflict ជាមួយ sidebar
document.querySelectorAll(".page-link-loading").forEach(function (link) {
    link.addEventListener("click", function (e) {
        const href = this.getAttribute("href");
        const msg = this.getAttribute("data-loading-text") || "Loading...";
        if (href && href !== "#" && href !== "javascript:void(0)") {
            e.preventDefault();
            showLoading(msg);
            window.location.href = href;
        }
    });
});
// Auto-hide toast
const successMsg = document.getElementById("successMessage");
if (successMsg) {
    setTimeout(function () {
        successMsg.style.transition = "opacity 0.5s";
        successMsg.style.opacity = "0";
        setTimeout(function () {
            successMsg.style.display = "none";
        }, 500);
    }, 3000);
}
