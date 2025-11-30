document.addEventListener("DOMContentLoaded", () => {

    const tbody = document.querySelector("#favTable tbody");
    const status = document.getElementById("status");

    let favoriteNames = [];
    let arbData = [];

    // Load favorite item names
    async function loadFavorites() {
        const res = await fetch("../src/api/getFavorite.php");
        const data = await res.json();

        if (!data.success) {
            status.textContent = "Failed to load favorites.";
            favoriteNames = [];
            return;
        }

        favoriteNames = data.favorites;
    }


    // Load arbitrage data
    async function loadArbData() {
        const res = await fetch("../src/scripts/get_arbitrage.php");
        arbData = await res.json();
    }

    function renderFavoritesTable() {
        tbody.innerHTML = "";

        // Match EXACT item names
        const favItems = arbData.filter(item =>
            favoriteNames.includes(item.market_hash_name)
        );

        if (favItems.length === 0) {
            status.textContent = "You have no favorites saved.";
            return;
        }

        status.textContent = "";

        favItems.forEach(item => {
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${item.market_hash_name}</td>
                <td>${item.wear_name ?? "N/A"}</td>
                <td>${item.direction}</td>
                <td>$${(item.empire_price / 100).toFixed(2)}</td>
                <td>$${(item.float_price / 100).toFixed(2)}</td>
                <td style="color:${item.profit > 0 ? 'lime' : 'red'}">
                    $${(item.profit / 100).toFixed(2)}
                </td>
                <td>${item.profit_percent}%</td>

                <td>
                    <span class="remove-fav" data-name="${item.market_hash_name}">
                        ✖
                    </span>
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    // Remove favorite
    document.addEventListener("click", async (e) => {
        if (!e.target.classList.contains("remove-fav")) return;

        const itemName = e.target.dataset.name;

        const res = await fetch("../src/api/removeFavorite.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ item: itemName })
        });

        const data = await res.json();

        if (data.success) {
            favoriteNames = favoriteNames.filter(n => n !== itemName);
            renderFavoritesTable();
        } else {
            alert("Failed to remove: " + data.error);
        }
    });


    (async () => {
        await loadFavorites();
        await loadArbData();
        renderFavoritesTable();
    })();

});
