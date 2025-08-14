<?php
namespace App\Controllers;
use App\Models\AuthModel;
use CodeIgniter\Controller;
class Auth extends Controller
{
    public function index()
    {   return view('auth/login');  }
    public function login()
    {   $session = session();
        $model = model(AuthModel::class);
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user = $model->where('email', $email)->first();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $session->set(['user' => $user]);
            if ($user['level'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($user['level'] === 'walimurid') {
                return redirect()->to('/walimurid/dashboard');  }
        } else { return redirect()->to('/')->with('error', 'Email atau password salah.');
        }
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}

