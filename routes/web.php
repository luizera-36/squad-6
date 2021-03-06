<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\salasController;

use App\Http\Controllers\filasController;

use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

//------------------------------------------------------------------------------

// Unidades

Route::get('/unidade', function(){
	if (!Auth::user())
            return view('auth/login');

	return view('salas/unidade');
});

Route::get('/unidade/santos', [salasController::class, 'salaSantos']);

Route::get('/unidade/saopaulo', [salasController::class, 'salasaoPaulo']);
//------------------------------------------------------------------------------

// Salas 

Route::get('/criarsala', function(){
	if (!Auth::user())
            return view('auth/login');

	return view('salas/criarSala');
})->name('criarsala');

Route::post('/cadastrandosala', [salasController::class, 'cadastrarSala']);

Route::get('/salas', [salasController::class, 'exibirSalas'])->name('salas');

// Rota necessária para fazer assincronismo das salas com AJAX
Route::get('/salas-conteudo', [salasController::class, 'salasAssincronas'])->name('salasConteudo');

Route::get('/salas/sala/{nomeSala}/excluir-sala', [salasController::class, 'excluirSala']);
//------------------------------------------------------------------------------

// FILAS 

Route::get('/salas/sala/{nomeSala}/{id}', [filasController::class, 'inserirusuarioFila'])->name('inserirFila');

Route::get('/fila-conteudo', [filasController::class, 'filaAssincrona'])->name('filaConteudo');

Route::get('/salas/sala/{nomeSala}/{id}/desistente', [filasController::class, 'desistirusuarioFila'])->name('desistir');

Route::get('/salas/sala/{nomeSala}/{id}/excluirjogando', [filasController::class, 'excluirusuariojogando'])->name('excluirjogando');

Route::get('/salas/sala/{nomeSala}/{id}/voujogar', [filasController::class, 'vouJogarFila'])->name('voujogar');

// SALA / FILA / Utilizando serviço da sala
Route::get('/salas/sala/{nomeSala}/{id}/Acabei', [filasController::class, 'sairdoServico'])->name('sairServico');

Route::get('/salas/sala/{nomeSala}/{id}/Voltar', [filasController::class, 'voltarFila'])->name('voltarFila');

//Funções para reportar

Route::get('/reportar/{url}/{id}', [filasController::class, 'reportar']);

Route::get('/estouaqui/{userid}', [filasController::class, 'estouaqui']);
//------------------------------------------------------------------------------



/*
* LOG OFF usuário
*/

Route::get('/sair', [UserController::class, 'logoff'])->name('sair');