<?php 
session_start();
$url='http://localhost:8080/squad-6/storage/app/public/';
// Verifica se o usuário já entrou em alguma sala
if (isset($_SESSION['entrou_sala']) && isset($_SESSION['cd_sala']) && $_SESSION['entrou_sala']) {
    // pega o Id da sala que ele deixou pelo botão voltar do browser
    $id_sala_anterior = $_SESSION['cd_sala']; 
    /* 
    * Resolvendo problema do usuário quando ele tenta voltar pelo browser
    * Basicamente eu passo o parâmetro com a sala anterior dele e retiro ele da sala 
    * mandando ele pro controler de desistência da fila
    */
   echo "<script>window.location.href='/salas/sala/nomesala/". $id_sala_anterior ."/desistente'</script>";
}
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.9.6/tailwind.min.css" 
    integrity="sha512-l7qZAq1JcXdHei6h2z8h8sMe3NbMrmowhOl+QkP3UhifPpCW2MC4M0i26Y8wYpbz1xD9t61MLT9L1N773dzlOA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('css/salas.css') }}">
    @livewireStyles


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script>

    @if ($_SESSION['santos'])
    <title>Fifo - Salas Santos</title>
    @else
    <title>Fifo - Salas São Paulo</title>
    @endif

</head>

<body>
@livewire('navigation-dropdown')

                    

<div class="voceEsta w-full items-center justify-center">
                    @if ($_SESSION['santos'])
                    <h3>Você esta conectado a unidade de Santos</h3>
                    @else
                    <h3>Você esta conectado a unidade de São Paulo</h3>
                    @endif
</div>
               


<div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0"> <!--- GRID -->

    <section class="salasContainer" id="salasContainer">
         
      
    </section>
    <a class="voltarUnidade font-bold text-sm text-blue-500 hover:text-orange-800" href="unidade">Voltar a escolha da unidade</a>
        
    
</div>

    <!-- Este script é necessário para fazer a conexão assíncrona com o AJAX -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<script>
        // Função responsável por atualizar as salas
    function atualizarSalas() {
        $.ajax({ 
            url: "{{ route('salasConteudo') }}",
            dataType: "json",
            cache: false,
        }).done(function (dadosSalas) {
            //cria container para inserir as salas
            $('#salasContainer').html("");
            var conteudo_salas = $("<div/>").addClass("conteudoContainer").addClass("grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-4").appendTo("section");
            conteudo_salas.innerHTML = "";
            //cria array das salas
            var wrapperSala = []
            var newSala = 
            $("<div/>")
            .addClass("criarSala")
            .addClass("max-w-sm md:w-1/2 lg:w-1/3 rounded overflow-hidden shadow-lg bg-gray-200 tracking-widest")
            .addClass("bg-gray-500 hover:bg-blue-600 text-white font-bold border border-gray-200 rounded");
              
            newSala.append( "<a href='criarsala'>Criar Sala</a>"  )
            
                    $contador = 0; // contador de usuários para a sala
                    // Exibe informações sobre os usuários cadastrados / online / ausente / offline, sala em que está
                    for (sala of dadosSalas.usuarios){
                            console.log(dadosSalas.usuarios[sala] ); 
                        }
                
                    for (i = 0; i < dadosSalas.sala.length; i++){   
                        //cria sala individual baseada nas salas do DB
                        wrapperSala[i] = $("<div/>").addClass("wrapper").addClass("max-w-sm lg:w-1/3 md:1/2 rounded overflow-hidden shadow-lg bg-gray-200");
                        wrapperSala[i].append(
                            "<img class='w-full' src='{{ $url }}" + dadosSalas.sala[i].img_sala 
                            + "' width=200>"
                        );
                        wrapperSala[i].append("<div class='px-6'>");
                        wrapperSala[i].append("<h3 class='font-bold text-xl mb-2'><b>" + dadosSalas.sala[i].nm_sala + "</b></h3>");
                        // Conta a quantidade de pessoas em uma determinada sala.
                        for(c = 0; c < dadosSalas.qt_usuarios.length; c++){
                            // Faz a verificação se o nome da sala é o mesmo do usuário.
                            if (dadosSalas.sala[i].nm_sala == dadosSalas.qt_usuarios[c].nm_sala) {
                                $contador *= 1 ; //adiciona +1 para cada usuário na                                
                            } 
                        }
                        wrapperSala[i].append(
                            "<p> Usuários na sala: <b>"
                            + $contador 
                            + "</b></p>"
                            + "<p> Demanda da sala: <b>"
                            + dadosSalas.sala[i].demanda 
                            + "</b></p>"
                        );
                        
                        wrapperSala[i].append("</div>");
                        
                        wrapperSala[i].append(
                            //classes do tailwind css
                            "<button class='enterButton inline-flex items-center  bg-gray-600 border" 
                            + "border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest"
                            + "hover:bg-blue-600"
                            //fim tailwind
                            + "class='enterButton'>"//adiciona classe
                            //rota da sala
                            + "<a href='salas/sala/" + dadosSalas.sala[i].nm_sala + "/" +  
                            //logica da rota
                            <?php if ($_SESSION["santos"]){ 
                                echo  "dadosSalas.sala[i].cd_sala_santos";
                                } else { 
                                    echo "dadosSalas.sala[i].cd_sala_sao_paulo"; 
                                    } ?> 
                            + "'>Entrar na Sala</a></button>"
                        );
                            wrapperSala[i].append(
                                "<button class='deleteButton inline-flex items-center bg-gray-400 border" 
                                + "border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest"
                                + "hover:bg-blue-600"                                //fim tailwind
                                + "class='deleteButton'>" //adiciona classe
                                //rota da sala
                                + "<a href='salas/sala/" + dadosSalas.sala[i].nm_sala + "/excluir/" + dadosSalas.sala[i].cd_sala_santos 
                                + "'>Excluir Sala</a></button>"
                            );                 
                        
                    }
                    
                    conteudo_salas.append(wrapperSala);
                  
                    conteudo_salas.append(newSala)

                    setTimeout("atualizarSalas()", 3000) // 3 segundos / Tempo de espera de atualização dos dados

                });
                
                
        }
        // Definindo intervalo que a função será chamada no caso 10 em 10 segundos
        
        // Quando carregar a página
        $(function () {
            // Faz a primeira atualização
            atualizarSalas();
        });
    </script>

</body>

</html>
