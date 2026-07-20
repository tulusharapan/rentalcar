<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'app'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
        $this->refreshLoggedInUser();
    }

    private function refreshLoggedInUser(): void
    {
        $session = session();

        if (! $session->get('isLoggedIn') || ! $session->get('userId')) {
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $session->get('userId'));

        if (! $user || (int) $user['is_active'] !== 1) {
            $session->destroy();

            return;
        }

        $session->set([
            'userName'  => $user['name'],
            'userEmail' => $user['email'],
            'userRole'  => $user['role'],
            'userPhoto' => $user['photo'] ?? null,
        ]);
    }

    protected function isAdministratorRole(): bool
    {
        return in_array((string) session()->get('userRole'), ['admin', 'administrator'], true);
    }

    protected function requireAdministrator(string $redirectTo, string $message = 'Akses ditolak. Hanya administrator yang dapat melakukan aksi ini.')
    {
        if ($this->isAdministratorRole()) {
            return null;
        }

        return redirect()->to($redirectTo)->with('error', $message);
    }

}
