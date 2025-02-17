<?php

session_start();

// if (!isset($_SESSION['user'])) {
//     header('Location: login.php');
//     exit();
// }

require_once 'api/db.php';

$id = $_GET['id'];
$nalez = get_nalez_by_id($id);

?>
<!DOCTYPE html>
<html lang="cs" class="bg-base-200">

<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Nálezu</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.23/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-5">
<div class="fixed top-0 left-0 w-full h-full bg-black z-10 bg-opacity-50 flex flex-col gap-2 items-center justify-center hidden" id="loading">
        <span class="loading loading-spinner loading-lg z-20 brightness-[200]"></span>
        <span class="text-white">Načítání...</span>
    </div>
    <div class="text-3xl font-bold">Detaily Nálezu</div>
    <div class="card bg-base-100 w-full shadow-xl mt-5">
        <div class="p-5 flex flex-col">
            <h2 class="card-title">
                <?php echo $nalez["nazev"]; ?>
            </h2>
            <p><b>Popis:</b> <?php echo $nalez["popis"]; ?></p>
            <p><b>Datum:</b> <?php echo $nalez["datum"]; ?></p>
            <?php
                $polohaY = explode(",", $nalez["poloha"])[0];
                $polohaX = explode(",", $nalez["poloha"])[1];
            ?>
            <p><b>Poloha:</b> <a class="link link-primary" href="https://mapy.cz/zakladni?source=coor&id=15.8102428640037%2C50.43248096709127&ds=1&x=<?php echo $polohaX ?>&y=<?php echo $polohaY ?>&z=18"><?php echo $nalez["poloha"]; ?></a></p>
            <p><b>Typ:</b> <?php echo $nalez["typ"]; ?></p>
            <p><b>Materiál:</b> <?php echo $nalez["material"]; ?></p>
        </div>
    </div>  
    <img class="w-full rounded-xl mt-3" src="<?php echo $nalez["foto_url"] ?>">
    <div class="mt-5">
        <a href="index.php" class="btn btn-primary w-full" onclick="OnLoading()">Zpět</a>
        <a onclick="delete_modal.showModal()" class="btn btn-error btn-outline w-full mt-3">Smazat</a>
    </div>
    <dialog id="delete_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Smazat nález</h3>
            <p class="py-4">Jste si jisti, že chcete smazat tento nález?</p>
            <div class="modal-action">
                <a href="delete_nalez.php?id=<?php echo $nalez["id"]; ?>" class="btn btn-error" onclick="OnLoading()">Ano</a>
                <button class="btn" onclick="delete_modal.close()">Ne</button>
            </div>
        </div>
    </dialog>
    <script>
        OnLoading = () => {
            document.getElementById('loading').classList.toggle('hidden');
        }
    </script>
</body>

</html>