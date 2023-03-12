<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\ReturnMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public $returnMsg;

    public function __construct()
    {
        $this->returnMsg = new ReturnMessage;
    }

    public function login(Request $req)
    {
        try {
            $credentials = $req->only('email', 'password');

            if (Auth::attempt($credentials, $req->filled('remember'))) {
                $user = DB::table('users')->where(['email' => $req->input('email')])->get(['id', 'username', 'email','first_login']);
                return $this->returnMsg->getReturn($user);
            }
            return $this->returnMsg->getReturn([], 200, false);
        } catch (ModelNotFoundException $e) {
            throw new Exception($e->getMessage());
            return response()->json(['error' => 'Usuário não encontrado!'], 404);
        } catch (QueryException $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return $this->returnMsg->getErrorReturn('Ocorreu um erro interno! Tente novamente.');
        } catch (Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return $this->returnMsg->getErrorReturn('Ocorreu um erro interno! Tente novamente.');
        }
    }

    public function register(Request $req)
    {
        try {
            $user = new User();
            $user->username = $req->input('username');
            $user->email = $req->input('email');
            $user->password = bcrypt($req->input('password'));
            $user->first_login = 1;
            $user->save();
            return $this->returnMsg->getReturn([]);
        } catch (QueryException $e) {
            Log::error('Erro ao registrar usuario: ' . $e->getMessage());
            return $this->returnMsg->getErrorReturn('Ocorreu um erro interno! Tente novamente.');
        } catch (Exception $e) {
            Log::error('Erro ao registrar usuario: ' . $e->getMessage());
            return $this->returnMsg->getErrorReturn('Ocorreu um erro interno! Tente novamente.');
        }
    }

    public function findUserBy(Request $req)
    {

        $where = json_decode($req->getContent(), true);

        try {
            $user = DB::table('users')->where($where)->get();
            return $user;
        } catch (QueryException $e) {
            Log::error('Erro ao encontrar usuario: ' . $e->getMessage());
            return $this->returnMsg->getErrorReturn('Ocorreu um erro interno! Tente novamente.');
        } catch (Exception $e) {
            Log::error('Erro ao encontrar usuario: ' . $e->getMessage());
            return $this->returnMsg->getErrorReturn('Ocorreu um erro interno! Tente novamente.');
        }
    }
}
