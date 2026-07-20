<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->createUserIfMissing([
            'name'     => 'Administrator',
            'email'    => 'admin@duitku.test',
            'password' => 'admin123',
            'role'     => 'administrator',
        ]);

        $this->createUserIfMissing([
            'name'     => 'Staff',
            'email'    => 'staff@duitku.test',
            'password' => 'staff123',
            'role'     => 'staff',
        ]);
    }

    private function createUserIfMissing(array $data): void
    {
        $user = $this->db->table('user')
            ->where('email', $data['email'])
            ->get()
            ->getRowArray();

        if ($user) {
            return;
        }

        $this->db->table('user')->insert([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'       => $data['role'],
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
