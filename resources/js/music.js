document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("music-search-form");
    const results = document.getElementById("search-results");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        results.innerHTML = `<p class="text-gray-500">Searching...</p>`;

        const response = await fetch("/music/search", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });

        const html = await response.text();
        results.innerHTML = html;
        attachQueueHandlers();
    });

    function attachQueueHandlers() {
        document.querySelectorAll(".add-to-queue").forEach(btn => {
            btn.addEventListener("click", async () => {
                const uri = btn.dataset.uri;
                const service = document.getElementById("service").value;

                btn.disabled = true;
                btn.textContent = "Adding...";

                const response = await fetch("/music/queue", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ uri, service })
                });

                const data = await response.json();
                btn.textContent = data.success ? "✅ Added" : "❌ Error";
            });
        });
    }
});
