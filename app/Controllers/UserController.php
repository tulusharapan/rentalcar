<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    private UserModel $userModel;
    private string $uploadPath;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->uploadPath = FCPATH . 'uploads/users';
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Data user hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        return view('admin/users/index', [
            'users'     => $this->userModel->orderBy('id', 'DESC')->findAll(),
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/users')) {
            return $redirect;
        }

        return view('admin/users/create', [
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/users')) {
            return $redirect;
        }

        $rules = [
            'name'      => 'required|min_length[3]',
            'email'     => 'required|valid_email|is_unique[user.email]',
            'password'  => 'required|min_length[6]',
            'role'      => 'required|in_list[administrator,staff]',
            'is_active' => 'required|in_list[0,1]',
            'photo'     => 'permit_empty|is_image[photo]|max_size[photo,2048]|ext_in[photo,jpg,jpeg,png,webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'photo'     => $this->uploadPhoto(),
            'password'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active'),
        ]);

        return redirect()->to('/admin/users')->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/users')) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Data user tidak ditemukan.');
        }

        return view('admin/users/edit', [
            'user'      => $user,
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/users')) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Data user tidak ditemukan.');
        }

        $rules = [
            'name'      => 'required|min_length[3]',
            'email'     => 'required|valid_email|is_unique[user.email,id,' . $id . ']',
            'password'  => 'permit_empty|min_length[6]',
            'role'      => 'required|in_list[administrator,staff]',
            'is_active' => 'required|in_list[0,1]',
            'photo'     => 'permit_empty|is_image[photo]|max_size[photo,2048]|ext_in[photo,jpg,jpeg,png,webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active'),
        ];

        $photoName = $this->uploadPhoto();

        if ($photoName !== null) {
            $data['photo'] = $photoName;
            $this->deletePhoto($user['photo'] ?? null);
        }

        $newPassword = (string) $this->request->getPost('password');

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/users')) {
            return $redirect;
        }

        if ((int) session()->get('userId') === $id) {
            return redirect()->to('/admin/users')->with('error', 'Akun yang sedang login tidak boleh dihapus.');
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Data user tidak ditemukan.');
        }

        $this->deletePhoto($user['photo'] ?? null);
        $this->userModel->delete($id);

        return redirect()->to('/admin/users')->with('success', 'Data user berhasil dihapus.');
    }

    public function profile()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = $this->userModel->find((int) session()->get('userId'));

        if (! $user) {
            session()->destroy();

            return redirect()->to('/login')->with('error', 'Data user tidak ditemukan. Silakan login ulang.');
        }

        return view('admin/users/profile', [
            'user'      => $user,
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function updateProfile()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = (int) session()->get('userId');
        $user   = $this->userModel->find($userId);

        if (! $user) {
            session()->destroy();

            return redirect()->to('/login')->with('error', 'Data user tidak ditemukan. Silakan login ulang.');
        }

        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[user.email,id,' . $userId . ']',
            'photo' => 'permit_empty|is_image[photo]|max_size[photo,2048]|ext_in[photo,jpg,jpeg,png,webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        $photoName = $this->uploadPhoto();

        if ($photoName !== null) {
            $data['photo'] = $photoName;
            $this->deletePhoto($user['photo'] ?? null);
        }

        $this->userModel->update($userId, $data);

        session()->set([
            'userName'  => $data['name'],
            'userEmail' => $data['email'],
            'userPhoto' => $data['photo'] ?? ($user['photo'] ?? null),
        ]);

        return redirect()->to('/admin/profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('admin/users/change_password', [
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function updatePassword()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = [
            'old_password'         => 'required',
            'new_password'         => 'required|min_length[6]',
            'confirm_new_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userId = (int) session()->get('userId');
        $user   = $this->userModel->find($userId);

        if (! $user) {
            return redirect()->to('/login')->with('error', 'Data user tidak ditemukan. Silakan login ulang.');
        }

        if (! password_verify((string) $this->request->getPost('old_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }

        $this->userModel->update($userId, [
            'password' => password_hash((string) $this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/admin/change-password')->with('success', 'Password berhasil diganti.');
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function uploadPhoto(): ?string
    {
        $photo = $this->request->getFile('photo');

        if (! $photo || ! $photo->isValid() || $photo->hasMoved()) {
            return null;
        }

        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0775, true);
        }

        $photoName = $photo->getRandomName();
        $photo->move($this->uploadPath, $photoName);

        return $photoName;
    }

    private function deletePhoto(?string $photoName): void
    {
        if (! $photoName) {
            return;
        }

        $photoPath = $this->uploadPath . DIRECTORY_SEPARATOR . $photoName;

        if (is_file($photoPath)) {
            unlink($photoPath);
        }
    }
}
