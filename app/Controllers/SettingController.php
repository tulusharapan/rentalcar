<?php

namespace App\Controllers;

use App\Models\SettingModel;

class SettingController extends BaseController
{
    private SettingModel $settingModel;
    private string $uploadPath;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->uploadPath   = FCPATH . 'uploads/settings';
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Setting aplikasi hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        return view('admin/setting/index', [
            'setting'   => $this->getSetting(),
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function update()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/setting')) {
            return $redirect;
        }

        $rules = [
            'nama_aplikasi'   => 'required|min_length[2]|max_length[100]',
            'tagline'         => 'permit_empty|max_length[150]',
            'nama_perusahaan' => 'permit_empty|max_length[150]',
            'email'           => 'permit_empty|valid_email|max_length[150]',
            'no_whatsapp'     => 'permit_empty|max_length[30]',
            'link_tiktok'     => 'permit_empty|valid_url_strict|max_length[255]',
            'link_instagram'  => 'permit_empty|valid_url_strict|max_length[255]',
            'link_youtube'    => 'permit_empty|valid_url_strict|max_length[255]',
            'link_facebook'   => 'permit_empty|valid_url_strict|max_length[255]',
            'harga_denda_per_hari' => 'required|integer|greater_than_equal_to[0]',
            'favicon'         => 'permit_empty|max_size[favicon,2048]|ext_in[favicon,ico,jpg,jpeg,png,webp]',
            'logo_1'          => 'permit_empty|is_image[logo_1]|max_size[logo_1,2048]|ext_in[logo_1,jpg,jpeg,png,webp]',
            'logo_2'          => 'permit_empty|is_image[logo_2]|max_size[logo_2,2048]|ext_in[logo_2,jpg,jpeg,png,webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $setting = $this->getSetting();
        $data = [
            'nama_aplikasi'   => $this->request->getPost('nama_aplikasi'),
            'tagline'         => $this->request->getPost('tagline'),
            'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
            'email'           => $this->request->getPost('email'),
            'no_whatsapp'     => $this->request->getPost('no_whatsapp'),
            'link_tiktok'     => $this->request->getPost('link_tiktok'),
            'link_instagram'  => $this->request->getPost('link_instagram'),
            'link_youtube'    => $this->request->getPost('link_youtube'),
            'link_facebook'   => $this->request->getPost('link_facebook'),
            'harga_denda_per_hari' => $this->request->getPost('harga_denda_per_hari'),
        ];

        $faviconName = $this->uploadSettingFile('favicon');
        $logo1Name   = $this->uploadSettingFile('logo_1');
        $logo2Name   = $this->uploadSettingFile('logo_2');

        if ($faviconName !== null) {
            $data['favicon'] = $faviconName;
            $this->deleteLogo($setting['favicon'] ?? null);
        }

        if ($logo1Name !== null) {
            $data['logo_1'] = $logo1Name;
            $data['logo']   = $logo1Name;
            $this->deleteLogo($setting['logo_1'] ?? ($setting['logo'] ?? null));
        }

        if ($logo2Name !== null) {
            $data['logo_2'] = $logo2Name;
            $this->deleteLogo($setting['logo_2'] ?? null);
        }

        $this->settingModel->update(1, $data);

        return redirect()->to('/admin/setting')->with('success', 'Setting aplikasi berhasil diperbarui.');
    }

    private function getSetting(): array
    {
        $setting = $this->settingModel->find(1);

        if ($setting) {
            return $setting;
        }

        $this->settingModel->insert([
            'setting_id'      => 1,
            'logo'            => null,
            'favicon'         => null,
            'logo_1'          => null,
            'logo_2'          => null,
            'nama_aplikasi'   => 'Aplikasi',
            'tagline'         => 'Panel administrasi aplikasi',
            'nama_perusahaan' => 'Aplikasi',
            'email'           => '',
            'no_whatsapp'     => '',
            'link_tiktok'     => '',
            'link_instagram'  => '',
            'link_youtube'    => '',
            'link_facebook'   => '',
            'harga_denda_per_hari' => 0,
        ]);

        return $this->settingModel->find(1);
    }

    private function uploadSettingFile(string $field): ?string
    {
        $logo = $this->request->getFile($field);

        if (! $logo || ! $logo->isValid() || $logo->hasMoved()) {
            return null;
        }

        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0775, true);
        }

        $logoName = $logo->getRandomName();
        $logo->move($this->uploadPath, $logoName);

        return $logoName;
    }

    private function deleteLogo(?string $logoName): void
    {
        if (! $logoName) {
            return;
        }

        $logoPath = $this->uploadPath . DIRECTORY_SEPARATOR . $logoName;

        if (is_file($logoPath)) {
            unlink($logoPath);
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }
}
