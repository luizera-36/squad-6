<?php
    // Vai redirecionar a pessoa depois de um determinado tempo de acordo com o tempo no (Refresh)
    header("Refresh: 2; url=login");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fifo - Squad 6</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>

<body>
    <main>
        <section class="container" align="center">
            <img src="{{ asset('assets/Logo1.png') }}"alt="Logo">
        </section>
    </main>
</body>
</html>