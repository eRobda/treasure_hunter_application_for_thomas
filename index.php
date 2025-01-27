<?php
session_start();
require_once 'api/db.php';

$nalezy = get_nalezy(); // Fetch the array of findings
?>

<!DOCTYPE html>
<html lang="cs" class="bg-base-200">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treasure Hunter Application</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.23/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="manifest.json">
</head>

<body class="p-5 mb-20">
    <div class="fixed top-0 left-0 w-full h-full bg-black z-10 bg-opacity-50 flex items-center justify-center hidden"
        id="loading">
        <span class="loading loading-spinner loading-lg invert z-20"></span>
    </div>
    <div class="text-3xl font-bold">Moje nálezy</div>
    <label class="input input-bordered flex items-center gap-2 mt-5">
        <input type="text" id="searchInput" class="grow" placeholder="Hledat..." />
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-4 w-4 opacity-70">
            <path fill-rule="evenodd"
                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                clip-rule="evenodd" />
        </svg>
    </label>
    <div id="nalezyContainer" class="mt-3 flex flex-col gap-3">
        <!-- Items will be dynamically rendered here -->
    </div>

    <div class="btm-nav">
        <a href="index.php" class="active">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </a>
        <a href="add.php">
            <img src="add.svg" class="h-5 w-5" />
        </a>
    </div>

    <script>
        // Pass the PHP data to JavaScript
        const nalezy = <?php echo json_encode($nalezy, JSON_HEX_TAG); ?>;
        console.log(nalezy);

        const nalezyContainer = document.getElementById('nalezyContainer');
        const searchInput = document.getElementById('searchInput');

        // Function to escape HTML to prevent XSS
        function escapeHTML(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Function to parse date in the format '26. 1. 2025 23:32:26'
        function parseCustomDate(datum) {
            if (!datum) return null;

            // Log the raw input
            console.log("Raw datum:", datum);

            // Normalize all whitespace characters
            const normalizedDatum = datum.replace(/\s+/g, ' ').trim();
            console.log("Normalized datum:", normalizedDatum);

            // Split into date and time parts
            const parts = normalizedDatum.split(' '); // Log the split result
            parts[0] = parts[0].replace(/\./g, '');
            parts[1] = parts[1].replace(/\./g, '');
            console.log("Split parts:", parts);

            const hours = parts[3].split(':');

            // Construct and return the Date object
            const parsedDate = new Date(parts[2], parts[1] - 1, parts[0], hours[0], hours[1], hours[2]);
            console.log("Parsed date object:", parsedDate);
            return parsedDate;
        }





        function calculateDaysDifference(date1, date2) {
            const startOfDay1 = new Date(date1.getFullYear(), date1.getMonth(), date1.getDate());
            const startOfDay2 = new Date(date2.getFullYear(), date2.getMonth(), date2.getDate());
            return Math.floor((startOfDay1 - startOfDay2) / (1000 * 60 * 60 * 24));
        }

        // Function to format date based on calendar days
        function formatDate(datum) {
            const today = new Date();
            const itemDate = parseCustomDate(datum);

            if (!itemDate || isNaN(itemDate.getTime())) {
                return 'Neznámé datum'; // Fallback for invalid date
            }

            // Calculate the calendar day difference
            const differenceInDays = calculateDaysDifference(today, itemDate);

            if (differenceInDays === 0) {
                return "Dnes";
            } else if (differenceInDays === 1) {
                return "Včera";
            } else {
                return itemDate.toLocaleDateString('cs-CZ', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }
        }
        // Function to group items by date
        function groupByDate(items) {
            const grouped = {};
            items.forEach(item => {
                const groupKey = formatDate(item.datum);
                if (!grouped[groupKey]) {
                    grouped[groupKey] = [];
                }
                grouped[groupKey].push(item);
            });
            return grouped;
        }

        function sortItemsByDate(items) {
            return items.sort((a, b) => {
                const dateA = parseCustomDate(a.datum);
                const dateB = parseCustomDate(b.datum);

                if (!dateA || isNaN(dateA.getTime())) return 1; // Push invalid dates to the end
                if (!dateB || isNaN(dateB.getTime())) return -1; // Push invalid dates to the end

                return dateB - dateA; // Newest items first
            });
        }

        // Function to render grouped items with dividers
        function renderNalezyGrouped(items) {
            if (!nalezyContainer) {
                console.error('Missing container element with ID "nalezyContainer".');
                return;
            }

            nalezyContainer.innerHTML = ''; // Clear previous items

            // Sort items by date (newest first) before grouping
            const sortedItems = sortItemsByDate(items);

            const groupedItems = groupByDate(sortedItems);

            Object.keys(groupedItems).forEach(groupKey => {
                // Create a divider for the group
                const divider = document.createElement('div');
                divider.className = 'divider';
                divider.textContent = groupKey;
                nalezyContainer.appendChild(divider);

                // Create and append items for the group
                groupedItems[groupKey].forEach(nalez => {
                    const card = document.createElement('a');
                    card.addEventListener("click", () => {
                        document.getElementById('loading').classList.remove('hidden');
                    });
                    card.href = `details.php?id=${nalez.id}`;
                    card.className = 'card bg-base-100 w-full shadow-xl';

                    card.innerHTML = `
                <div class="card-body">
                    <h2 class="card-title">${escapeHTML(nalez.nazev)}</h2>
                    <p>${escapeHTML(nalez.popis)}</p>
                    <div class="card-actions justify-end">
                        <div class="badge badge-outline">${escapeHTML(nalez.typ)}</div>
                        <div class="badge badge-outline">${escapeHTML(nalez.material)}</div>
                    </div>
                </div>
            `;
                    nalezyContainer.appendChild(card);
                });
            });
        }

        // Initial grouped render
        try {
            renderNalezyGrouped(nalezy);
        } catch (error) {
            console.error("Error rendering items:", error);
        }

        // Filter items based on search input
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredNalezy = nalezy.filter(nalez =>
                    nalez.nazev.toLowerCase().includes(searchTerm) ||
                    nalez.popis.toLowerCase().includes(searchTerm) ||
                    nalez.typ.toLowerCase().includes(searchTerm) ||
                    nalez.material.toLowerCase().includes(searchTerm)
                );
                renderNalezyGrouped(filteredNalezy);
            });
        } else {
            console.error('Missing search input element with ID "searchInput".');
        }
    </script>



</body>

</html>