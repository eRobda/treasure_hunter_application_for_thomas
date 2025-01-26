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

<body class="p-5">
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

        const nalezyContainer = document.getElementById('nalezyContainer');
        const searchInput = document.getElementById('searchInput');

        // Function to render items
        function renderNalezy(items) {
            nalezyContainer.innerHTML = ''; // Clear previous items
            items.forEach(nalez => {
                const card = document.createElement('a');
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
        }

        // Escape HTML to prevent XSS
        function escapeHTML(str) {
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        }

        // Initial render
        renderNalezy(nalezy);

        // Filter items based on search input
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredNalezy = nalezy.filter(nalez =>
                nalez.nazev.toLowerCase().includes(searchTerm) ||
                nalez.popis.toLowerCase().includes(searchTerm) ||
                nalez.typ.toLowerCase().includes(searchTerm) ||
                nalez.material.toLowerCase().includes(searchTerm)
            );
            renderNalezy(filteredNalezy);
        });
    </script>
</body>

</html>
