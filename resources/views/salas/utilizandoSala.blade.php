<?php 
$url='http://localhost:8080/squad-6/storage/app/public/';
?>
<!DOCTYPE html>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fifo - Utilizando Sala</title>
</head>

<style>
    .container {
        margin-top: 15%;
    }
</style> 

<body>
    <main>
        <section class="container" align=center>
            <h2>Utilizando a Sala</h2>
            <div id="conteudo"></div>
            <br><br>
            <h3>Selecione um botão após terminar</h3>
            <a href="Voltar">Voltar para o final da fila</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="Acabei">Sair da sala</a>
        </section>
    </main>

    <!-- Este script é necessário para fazer a conexão assíncrona com o AJAX -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <script>
        function atualizarFila() {
                
                $.ajax({ 
                    url: "{{ route('filaConteudo') }}",
                    dataType: "json",
                    cache: false,
                }).done(function (dadosFila) {

                    // Descarta todas as informações anteriores para novas informações
                    $('#conteudo').html("");
                    

                    for (i = 0; i < dadosFila.Utilizando.length; i++) 
                    {
                        // Exibe foto do usuário se existir
                        if (dadosFila.Utilizando[i].profile_photo_path) {
                            $('#conteudo').append(
                                "<img src='{{ $url }}" + dadosFila.Utilizando[i].profile_photo_path+"' width='40'> &nbsp;"
                            );
                        }

                        // Exibe usuários que estão utilizando a sala
                        $('#conteudo').append(
                            "Nome: <b>" + dadosFila.Utilizando[i].name+"</b>&nbsp;&nbsp;" +
                            "Status: <b>Em andamento</b>"
                        );
                    }
                
                    setTimeout("atualizarFila()", 3000) // 3 segundos / Tempo de espera de atualização dos dados
                })
            }
            // Quando carregar a página
            $(function () {
                // Faz a primeira atualização
                atualizarFila();
            });
    </script>
</body>

</html>