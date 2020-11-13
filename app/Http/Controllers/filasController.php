<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

// Permite pegar valores de inputs 
use Illuminate\Http\Request;

// Permite o uso do banco de dados
use Illuminate\Support\Facades\DB;

// Permite upload de arquivos 
use Illuminate\Http\UploadedFile;

// Permite redirecionar páginas
use Illuminate\Routing\Redirector;

// Permite deletar imagens do storage
use Illuminate\Support\Facades\Storage;

// Permite verificar se usuário está logado.
use Illuminate\Support\Facades\Auth;


class filasController extends Controller
{
    public function inserirusuarioFila($nomeSala, $id)
    {
        session_start();

        if (!Auth::user())
            return view('auth/login');

        $email = $_SESSION['usuario'];

        if ($_SESSION['santos']) {
            DB::table('users')
                ->where('email', $email)
                ->update(['cd_sala_santos' => $id]);
        } else {
            DB::table('users')
                ->where('email', $email)
                ->update(['cd_sala_sao_paulo' => $id]);
        }

        return filasController::atualizarFila($nomeSala, $id);
    }

    public function atualizarFila($nomeSala, $id)
    {
        $email = $_SESSION['usuario'];

        if (!Auth::user())
            return view('auth/login');

        if ($_SESSION['santos']) {
            $atualizarUsuario = DB::table('users')
                                    ->select('cd_fila_usuario')
                                    ->where('cd_sala_santos', '=', $id)
                                    ->pluck('cd_fila_usuario');
        } else {
            $atualizarUsuario = DB::table('users')
                                    ->select('cd_fila_usuario')
                                    ->where('cd_sala_sao_paulo', '=', $id)
                                    ->pluck('cd_fila_usuario');
        }

        $quantidade = count($atualizarUsuario);
        
        if ($atualizarUsuario) {
            $estanafila = DB::table('users')
                                ->select('cd_fila_usuario')
                                ->where('email', $email)
                                ->value('cd_fila_usuario');
            //Verifica se o usuário ja está na fila.
            
            if(!$estanafila)
            {
                // Faz atualização do campo cd_fila_usuario, para dizer sua posição na fila
                DB::table('users')
                        ->where('email','=', $email)
                        ->update(['cd_fila_usuario' => $quantidade]);
            }
        }

        return filasController::pegadadosusuarioSala($nomeSala, $id);
    }

    public function pegadadosusuarioSala($nomeSala, $id)
    {
        $email = $_SESSION['usuario'];

        $usuario = DB::table('users')
                    ->select('name', 'cd_fila_usuario')
                    ->where('email', $email)
                    ->get();

        return filasController::exibirFila($nomeSala, $id, $usuario);
    }

    public function desistirusuarioFila($nomeSala, $id)
    {
        session_start();
        $email = $_SESSION['usuario'];
        $cd_sala = $id;
  
        // Pega a posição do desistente da fila
        $posicao_usuario = DB::table('users')
                        ->select('cd_fila_usuario')
                        ->where('email', $email)
                        ->pluck('cd_fila_usuario');
        
        if ($_SESSION['santos']) {
            $fila = DB::table('users')
                            ->select('name', 'cd_fila_usuario',  'profile_photo_path')
                            ->where('cd_sala_santos', $id)
                            ->get();

            $pessoas_fila = count($fila);

            for ($i = $posicao_usuario[0]; $i <= $pessoas_fila; $i++)
            {
                $aux = $i + 1;
                DB::table('users')
                    ->where([['cd_fila_usuario', '=', $aux], 
                            ['cd_sala_santos', '=', $cd_sala],
                    ])->update(['cd_fila_usuario'=> $i]);
                    
            }

            // Retira o usuário da fila e volta o código da fila dele para nulo
            DB::table('users')
                    ->where('email', $email)
                    ->update(['cd_sala_santos'=> null, 'cd_fila_usuario' => null]);
        } else {
            $fila = DB::table('users')
                    ->select('name', 'cd_fila_usuario',  'profile_photo_path')
                    ->where('cd_sala_sao_paulo', $id)
                    ->get();

            $pessoas_fila = count($fila);

            for ($i = $posicao_usuario[0]; $i <= $pessoas_fila; $i++)
            {
                $aux = $i + 1;
                DB::table('users')->where([
                    ['cd_fila_usuario', '=', $aux], 
                    ['cd_sala_sao_paulo', '=', $cd_sala],
                    ])->update(['cd_fila_usuario'=> $i]);
            }

            // Retira o usuário da fila e volta o código da fila dele para nulo
            DB::table('users')
                ->where('email', $email)
                ->update(['cd_sala_sao_paulo'=> null, 'cd_fila_usuario' => null]);
        }

        // Retornando condição como false, para não ficar retornando toda hora a mesma função
        $_SESSION['entrou_sala'] = false;

        return redirect()->route('salas');
    }

    public function vouJogarFila($nomeSala, $id)
    {
        session_start();
        $email = $_SESSION['usuario'];
        $cd_sala = $id;
  
        // Pega a posição do desistente da fila
        $posicao_usuario = DB::table('users')
                        ->select('cd_fila_usuario')
                        ->where('email', $email)
                        ->value('cd_fila_usuario');
        
        if ($_SESSION['santos'] && $posicao_usuario == 1) {
            $fila = DB::table('users')
                            ->select('name', 'cd_fila_usuario',  'profile_photo_path')
                            ->where('cd_sala_santos', $id)
                            ->get();

            $pessoas_fila = count($fila);

            for ($i = $posicao_usuario; $i <= $pessoas_fila; $i++)
            {
                $aux = $i + 1;
                DB::table('users')
                    ->where([['cd_fila_usuario', '=', $aux], 
                            ['cd_sala_santos', '=', $cd_sala],
                    ])->update(['cd_fila_usuario'=> $i]);
                    
            }

            // Retira o usuário da fila e volta o código da fila dele para nulo
            DB::table('users')
                    ->where('email', $email)
                    ->update(['cd_sala_santos'=> null, 'cd_fila_usuario' => null]);
        } else {

            if ($posicao_usuario == 1) {

                $fila = DB::table('users')
                        ->select('name', 'cd_fila_usuario',  'profile_photo_path')
                        ->where('cd_sala_sao_paulo', $id)
                        ->get();

                $pessoas_fila = count($fila);

                for ($i = $posicao_usuario; $i <= $pessoas_fila; $i++)
                {
                    $aux = $i + 1;
                    DB::table('users')->where([
                        ['cd_fila_usuario', '=', $aux], 
                        ['cd_sala_sao_paulo', '=', $cd_sala],
                        ])->update(['cd_fila_usuario'=> $i]);
                }

                // Retira o usuário da fila e volta o código da fila dele para nulo
                DB::table('users')
                    ->where('email', $email)
                    ->update(['cd_sala_sao_paulo'=> null, 'cd_fila_usuario' => null]);
            }
        }

         return redirect()->route('salas');
    }

    public function exibirFila($nomeSala, $id, $usuario)
    {
        
        if (!Auth::user())
            return view('auth/login');

        return view('salas/filaSala', ['salaId' => $id]);
    }

    public function filaAssincrona() 
    {
        session_start();
        $sala_id = $_SESSION['cd_sala'];
        $email = $_SESSION['usuario'];

        if ($_SESSION['santos']) {
            $dadosUsuario = DB::table('users')
                                ->join('tb_sala_santos', 'users.cd_sala_santos', '=', 'tb_sala_santos.cd_sala_santos')
                                ->select('users.name', 'tb_sala_santos.nm_sala', 
                                 'users.cd_fila_usuario', 'report','id')
                                ->where('email', $email)
                                ->get();

            $dadosFila =  DB::table('users')
                            ->select('users.*')
                            ->where('cd_sala_santos', $sala_id)
                            ->orderBy('cd_fila_usuario')
                            ->get();
        } else {
            $dadosUsuario = DB::table('users')
                                ->join('tb_sala_sao_paulo', 'users.cd_sala_sao_paulo', '=', 'tb_sala_sao_paulo.cd_sala_sao_paulo')
                                ->select('users.name', 'tb_sala_sao_paulo.nm_sala', 
                                'users.cd_fila_usuario', 'report')
                                ->where('email', $email)
                                ->get();

            $dadosFila =  DB::table('users')
                                ->select('users.*')
                                ->where('cd_sala_sao_paulo', $sala_id)
                                ->orderBy('cd_fila_usuario')
                                ->get();
        }

        return response()->json(["dadosUsuario" => $dadosUsuario, "dadosFila" => $dadosFila]);
    }

    public function reportar($url,$id) 
    {

        // Atualizo a db para usuario que for reportado
        DB::table('users')
            ->where('users.id','=',$id)
            ->update(['report' => true]);

            return back();
    }   

    public function estouaqui($userid)
    {
        DB::table('users')
        ->where('id','=',$userid)
        ->update (['report' => false]);

        return back();
    }

}
