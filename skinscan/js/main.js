let slideIndex = 1;
let slideTimer; // for auto-slide

showSlides(slideIndex);

// Neste/forrige
function plusSlides(n) {
  showSlides(slideIndex += n);
  resetTimer();
}

// Klikk på dot
function currentSlide(n) {
  showSlides(slideIndex = n);
  resetTimer();
}

// Viser slides og dots
function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");

  if (n > slides.length) { slideIndex = 1 }
  if (n < 1) { slideIndex = slides.length }

  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  slides[slideIndex - 1].style.display = "block";
  dots[slideIndex - 1].className += " active";

  // Automatisk loop
  clearTimeout(slideTimer);
  slideTimer = setTimeout(() => plusSlides(1), 3000); // 3 sekunder per slide
}

// Reset timer hvis brukeren klikker
function resetTimer() {
  clearTimeout(slideTimer);
  slideTimer = setTimeout(() => plusSlides(1), 3000);
}

document.addEventListener("DOMContentLoaded", () => {
  const checkArbBtn = document.getElementById("checkArb");
  const arbTable = document.getElementById("arbResults");
  const tbody = arbTable.querySelector("tbody");
  const status = document.getElementById("status");
  const excludeStattrak = document.getElementById("excludeStattrak");
  const minValueInput = document.getElementById("minValue");
  const maxValueInput = document.getElementById("maxValue");
  const applyFiltersBtn = document.getElementById("applyFilters");
  


  let currentData = []; // Store fetched data for sorting
  let sortDirection = {}; // Track sort direction per column
  let filteredData = [];

  function applyFilters() {
    let filtered = [...currentData];

    // Exclude StatTrak
    if (excludeStattrak.checked) {
      filtered = filtered.filter(item =>
        !item.market_hash_name.toLowerCase().includes("stattrak")
      );
    }

    // Min value
    const minVal = Number(minValueInput.value) * 100;
    if (!isNaN(minVal) && minVal > 0) {
      filtered = filtered.filter(item =>
        Math.min(item.empire_price, item.float_price) >= minVal
      );
    }

    // Max value
    const maxVal = Number(maxValueInput.value) * 100;
    if (!isNaN(maxVal) && maxVal > 0) {
      filtered = filtered.filter(item =>
        Math.min(item.empire_price, item.float_price) <= maxVal
      );
    }

    filteredData = filtered;

    renderTable(filtered);
  }

  applyFiltersBtn.addEventListener("click", () => {
    applyFilters();
  });



  checkArbBtn.addEventListener("click", async () => {
    status.textContent = "Scanning for arbitrage opportunities...";
    arbTable.style.display = "none";
    tbody.innerHTML = "";

    try {
      const res = await fetch("../src/scripts/get_arbitrage.php");
      const data = await res.json();
      currentData = data;

      if (!data || data.length === 0) {
        status.textContent = "No arbitrage opportunities found.";
        return;
      }

      renderTable(currentData);
      arbTable.style.display = "table";
      status.textContent = "";
    } catch (err) {
      status.textContent = "Error loading data: " + err;
    }
  });

  // Add sorting to table headers
  document.querySelectorAll("#arbResults th").forEach(header => {
    header.style.cursor = "pointer";
    header.addEventListener("click", () => {
      const field = header.getAttribute("data-sort");
      sortBy(field, header);
    });
  });

  function sortBy(field, headerElement) {
    // Toggle direction
    sortDirection[field] = !sortDirection[field];

    // Remove sort classes from other headers
    document.querySelectorAll("#arbResults th").forEach(h => {
      h.classList.remove("asc", "desc", "sorted");
    });

    // Add new sort indicator
    headerElement.classList.add("sorted");
    headerElement.classList.add(sortDirection[field] ? "asc" : "desc");

    const dataToSort = filteredData.length > 0 ? filteredData : currentData;

    // Sort logic
    dataToSort.sort((a, b) => {
      let valA = a[field] ?? "";
      let valB = b[field] ?? "";

      if (["empire_price", "float_price", "profit"].includes(field)) {
        valA = Number(valA);
        valB = Number(valB);
      } else if (field === "profit_percent") {
        valA = Number(valA);
        valB = Number(valB);
      } else {
        valA = valA.toString().toLowerCase();
        valB = valB.toString().toLowerCase();
      }

      if (valA < valB) return sortDirection[field] ? -1 : 1;
      if (valA > valB) return sortDirection[field] ? 1 : -1;
      return 0;
    });

    renderTable(dataToSort);
  }

  function renderTable(data) {
    tbody.innerHTML = "";
    data.forEach(item => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${item.market_hash_name}</td>
        <td>${item.wear_name ?? "N/A"}</td>
        <td>${item.direction}</td>
        <td>$${(item.empire_price / 100).toFixed(2)}</td>
        <td>$${(item.float_price / 100).toFixed(2)}</td>
        <td style="color:${item.profit > 0 ? 'green' : 'red'}">
          $${(item.profit / 100).toFixed(2)}
        </td>
        <td>${item.profit_percent}%</td>
      `;
      tbody.appendChild(row);
    });
  }
});
