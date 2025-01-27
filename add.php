<?php

session_start();

// if (!isset($_SESSION['user'])) {
//     header('Location: login.php');
//     exit();
// }

require_once 'api/db.php';

$nalezy = get_nalezy();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nazev = $_POST['nazev'];
    $popis = $_POST['popis'];
    $typ = $_POST['typ'];
    $material = $_POST['material'];
    $poloha = $_POST['poloha'];
    $datum = $_POST['datum'];
    $foto_url = null;

    // Handle file upload
    if (isset($_FILES['fotografie']) && $_FILES['fotografie']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'nalezy_foto/';
        $file_ext = pathinfo($_FILES['fotografie']['name'], PATHINFO_EXTENSION); // Get the file extension
        $unique_name = uniqid('foto_', true) . '.' . $file_ext; // Create a unique filename
        $target_file = $upload_dir . $unique_name;

        // Check if the upload folder exists, create it if not
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file to the target folder
        if (move_uploaded_file($_FILES['fotografie']['tmp_name'], $target_file)) {
            $foto_url = $target_file; // Store the file URL
        } else {
            echo "Chyba při nahrávání souboru.";
            exit();
        }
    }

    // Call the add_nalez function with the photo URL
    add_nalez($nazev, $popis, $poloha, $typ, $material, $datum, $foto_url);

    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="cs" class="bg-base-200">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Treasure Hunter Application</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.23/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-5">
    <div class="fixed top-0 left-0 w-full h-full bg-black z-10 bg-opacity-50 flex flex-col gap-2 items-center justify-center hidden" id="loading">
        <span class="loading loading-spinner loading-lg z-20 brightness-[200]"></span>
        <span class="text-white">Načítání...</span>
    </div>
    <div class="text-3xl font-bold mb-5">Přidat nález</div>
    <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
        <div class="flex flex-col gap-1">
            <div>Název nálezu</div>
            <input name="nazev" type="text" class="input input-bordered w-full" required />
        </div>
        <div class="flex flex-col gap-2">
            <div>Popis nálezu</div>
            <textarea id="popis_input" class="textarea textarea-bordered" required></textarea>
            <input name="popis" type="text" hidden id="popis_value" />
        </div>
        <div class="flex flex-col gap-2">
            <div>Typ</div>
            <select id="typ_input" class="select select-bordered w-full" required>
                <option disabled selected>Jiné</option>
                <option>Mince</option>
                <option>Artefakt</option>
            </select>
            <input id="typ_value" name="typ" type="text" hidden />
        </div>
        <div class="flex flex-col gap-2">
            <div>Materiál</div>
            <select id="material_input" class="select select-bordered w-full" required>
                <option disabled selected>Jiné</option>
                <option>Zlato</option>
                <option>Stříbro</option>
                <option>Kov</option>
            </select>
            <input id="material_value" name="material" type="text" hidden />
        </div>
        <div class="flex flex-col gap-2">
            <div>Fotografie</div>
            <input type="file" name="fotografie" class="file-input w-full max-w-xs" accept="image/*" required />
        </div>
        <input type="text" name="poloha" id="poloha" hidden />
        <input type="text" name="datum" id="datum" hidden />
        <input type="submit" class="btn btn-primary w-full mt-5" onclick="OnLoading()" value="Uložit" />
        <a href="index.php" class="btn btn-primary btn-outline w-full mb-20">Zpět</a>
    </form>

    <div class="btm-nav">
        <a href="index.php" onclick="OnLoading()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </a>
        <a href="add.php" class="active">
            <img src="add.svg" class="h-5 w-5" />
        </a>
    </div>
    <script>
        function OnLoading() {
            document.getElementById('loading').classList.remove('hidden');
        }

        const popis_input = document.getElementById('popis_input');
        const popis_value = document.getElementById('popis_value');
        popis_input.addEventListener('change', () => {
            popis_value.value = popis_input.value;
        });

        const typ_input = document.getElementById('typ_input');
        const typ_value = document.getElementById('typ_value');
        typ_input.addEventListener('change', () => {
            typ_value.value = typ_input.value;
        });

        const material_input = document.getElementById('material_input');
        const material_value = document.getElementById('material_value');
        material_input.addEventListener('change', () => {
            material_value.value = material_input.value;
        });

        const poloha = document.getElementById('poloha');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Success: Get the coordinates
                    poloha.value = `${position.coords.latitude},${position.coords.longitude}`;
                },
                (error) => {
                    // Handle error cases
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            alert("Permission to access location was denied.");
                            break;
                        case error.POSITION_UNAVAILABLE:
                            alert("Location information is unavailable.");
                            break;
                        case error.TIMEOUT:
                            alert("The request to get your location timed out.");
                            break;
                        default:
                            alert("An unknown error occurred.");
                    }
                }
            );
        } else {
            // Browser does not support Geolocation API
            alert("Geolocation is not supported by your browser.");
        }

        const datum = document.getElementById('datum');
        datum.value = new Date().toLocaleString();
    </script>
</body>

</html>