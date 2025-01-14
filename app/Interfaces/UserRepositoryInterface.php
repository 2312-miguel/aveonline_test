<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Devuelve un usuario por su email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Crea un nuevo usuario en la base de datos.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User;
}
