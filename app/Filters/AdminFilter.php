<?php
// C:\xampp\htdocs\payment-gateway\app\Filters\AdminFilter.php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');
        if (!$user || $user['level'] !== 'admin') {
            return redirect()->to('/'); }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}

