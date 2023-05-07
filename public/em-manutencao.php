<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//seminovos.com.br/css/app.css?<?=rand(0,10)?>" media="screen,print" rel="stylesheet" type="text/css">
    <title>Em manutenção | Seminovos</title>

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <img src="//seminovos.com.br/img/logo.png" alt="Logo" class="img-fluid">
            </div>
            <div class="col-12 text-center">
                <h1>Em manutenção</h1>
                <p>Estamos trabalhando para melhorar a sua experiência.</p>
                <p>Voltaremos às 5 horas da manhã de hoje</p>
                <p>Volte <span class="minutos"></span></p>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/pt-br.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        var s = moment('2023-05-07 05:00:00').fromNow();

        $('.minutos').text(s );

    </script>
</body>
</html>
