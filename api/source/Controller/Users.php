<?php

namespace Source\Controller;

use Source\Models\User;

class Users extends Api
{
    public function register(array $data): void
    {
        if (!isset($data['password']) || empty($data['password'])) {
            $this->call(
                400,
                "bad_request",
                "A senha é obrigatória.",
                "error"
            )->back();
            return;
        }

        if (!$this->validateNameEmail($data)) {
            $this->call(
                400,
                "bad_request",
                "Nome e e-mail são obrigatórios. O e-mail deve ser válido.",
                "error"
            )->back();
            return;
        }

        $user = new User(
            null,
            3,
            $data['name'],
            $data['email'],
            $data['password']
        );

        if (!$user->insert()) {
            $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => 3,
            "photo" => $user->getPhoto(),
            "token" => null
        ];

        $this->call(201, "success", "Usuário inserido com sucesso", "created")->back($response);
    }

    public function auth(array $data): void
    {
        if (
            !isset($data['email'], $data['password']) ||
            empty($data['email']) || empty($data['password']) ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $this->call(
                400,
                "bad_request",
                "E-mail e senha são obrigatórios. O e-mail deve ser válido.",
                "error"
            )->back();
            return;
        }

        $user = new User();
        if (!$user->login($data['email'], $data['password'], 3)) {
            $this->call(
                401,
                "unauthorized",
                $user->getErrorMessage(),
                "error"
            )->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => $user->getTypeId(),
            "photo" => $user->getPhoto(),
            "token" => $user->getToken(),
        ];

        $this->call(
            200,
            "success",
            "Usuário logado com sucesso",
            "success"
        )->back($response);
    }

    public function authAdmin(array $data): void
    {
        if (
            !isset($data['email'], $data['password']) ||
            empty($data['email']) || empty($data['password']) ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $this->call(
                400,
                "bad_request",
                "E-mail e senha são obrigatórios. O e-mail deve ser válido.",
                "error"
            )->back();
            return;
        }

        $user = new User();
        if (!$user->login($data['email'], $data['password'], 1)) {
            $this->call(
                401,
                "unauthorized",
                $user->getErrorMessage(),
                "error"
            )->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => $user->getTypeId(),
            "photo" => $user->getPhoto(),
            "token" => $user->getToken(),
        ];

        $this->call(
            200,
            "success",
            "Usuário logado com sucesso",
            "success"
        )->back($response);
    }

   public function update(array $data): void
{
    // Verifica autenticação e permissão (tipo 3)
    if (!$this->authToken(3)) {
        $this->call(401, "unauthorized", "Usuário não autenticado ou sem permissão.", "error")->back();
        return;
    }

    // Valida nome e email
    if (!$this->validateNameEmail($data)) {
        $this->call(400, "bad_request", "Nome e e-mail são obrigatórios. O e-mail deve ser válido.", "error")->back();
        return;
    }

    $userId = $this->userAuthId;
    $user = new User();

    // Carrega o usuário atual
    if (!$user->selectById($userId)) {
        $this->call(404, "not_found", "Usuário não encontrado.", "error")->back();
        return;
    }

    // Atualiza os dados
    $user->setName($data['name']);
    $user->setEmail($data['email']);
    if (isset($data['password']) && !empty($data['password'])) {
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
    }

    // Persiste
    if (!$user->updateById($userId)) {
        $error = $user->getErrorMessage();
        // Se a mensagem for "sem alterações", consideramos sucesso
        if ($error !== 'Registro não encontrado ou sem alterações.') {
            $this->call(500, "internal_server_error", "Falha ao atualizar: " . $error, "error")->back();
            return;
        }
    }

    // Resposta
    $response = [
        "id" => $user->getId(),
        "name" => $user->getName(),
        "email" => $user->getEmail(),
        "type_id" => $user->getTypeId(),
        "photo" => $user->getPhoto(),
    ];

    $this->call(200, "success", "Usuário atualizado com sucesso", "success")->back($response);
}
public function updateAdmin(array $data): void
{
    // Verifica autenticação e permissão (tipo 1)
    if (!$this->authToken(1)) {
        $this->call(401, "unauthorized", "Usuário não autenticado ou sem permissão.", "error")->back();
        return;
    }

    // Valida nome e email
    if (!$this->validateNameEmail($data)) {
        $this->call(400, "bad_request", "Nome e e-mail são obrigatórios. O e-mail deve ser válido.", "error")->back();
        return;
    }

    $userId = $this->userAuthId;
    $user = new User();

    if (!$user->selectById($userId)) {
        $this->call(404, "not_found", "Usuário não encontrado.", "error")->back();
        return;
    }

    $user->setName($data['name']);
    $user->setEmail($data['email']);
    if (isset($data['password']) && !empty($data['password'])) {
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
    }

    if (!$user->updateById($userId)) {
        $error = $user->getErrorMessage();
        if ($error !== 'Registro não encontrado ou sem alterações.') {
            $this->call(500, "internal_server_error", "Falha ao atualizar admin: " . $error, "error")->back();
            return;
        }
    }

    $response = [
        "id" => $user->getId(),
        "name" => $user->getName(),
        "email" => $user->getEmail(),
        "type_id" => $user->getTypeId(),
        "photo" => $user->getPhoto(),
    ];

    $this->call(200, "success", "Admin atualizado com sucesso", "success")->back($response);
}


    public function registerPersonal(array $data): void
    {
        if (!isset($data['password']) || empty($data['password'])) {
            $this->call(400, "bad_request", "A senha é obrigatória.", "error")->back();
            return;
        }

        if (!$this->validateNameEmail($data)) {
            $this->call(400, "bad_request", "Nome e e-mail são obrigatórios. O e-mail deve ser válido.", "error")->back();
            return;
        }

        $user = new User(
            null,
            2, // type_id = 2 (Personal)
            $data['name'],
            $data['email'],
            $data['password']
        );

        if (!$user->insert()) {
            $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => 2,
            "photo" => $user->getPhoto(),
            "token" => null
        ];

        $this->call(201, "success", "Personal cadastrado com sucesso", "created")->back($response);
    }

    public function authPersonal(array $data): void
    {
        if (
            !isset($data['email'], $data['password']) ||
            empty($data['email']) || empty($data['password']) ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $this->call(400, "bad_request", "E-mail e senha são obrigatórios. O e-mail deve ser válido.", "error")->back();
            return;
        }

        $user = new User();
        if (!$user->login($data['email'], $data['password'], 2)) { // type_id = 2
            $this->call(401, "unauthorized", $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => $user->getTypeId(),
            "photo" => $user->getPhoto(),
            "token" => $user->getToken(),
        ];

        $this->call(200, "success", "Personal logado com sucesso", "success")->back($response);
    }

    public function updatePersonal(array $data): void
    {
        // 1. Verifica token e permissão (type_id = 2)
        if (!$this->authToken(2)) {
            $this->call(
                401,
                "unauthorized",
                "Usuário não autenticado ou sem permissão.",
                "error"
            )->back();
            return;
        }

        // 2. Valida campos obrigatórios (nome e email)
        if (!$this->validateNameEmail($data)) {
            $this->call(
                400,
                "bad_request",
                "Nome e e-mail são obrigatórios. O e-mail deve ser válido.",
                "error"
            )->back();
            return;
        }

        // 3. Obtém o ID do usuário autenticado (definido pelo authToken)
        $userId = $this->userAuthId;

        // 4. Carrega o usuário atual
        $user = new User();
        if (!$user->selectById($userId)) {
            $this->call(404, "not_found", "Usuário não encontrado.", "error")->back();
            return;
        }

        // 5. Atualiza os dados
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        // Se a senha foi enviada, atualiza (com hash)
        if (isset($data['password']) && !empty($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        }

        // 6. Persiste as alterações
        if (!$user->updateById($userId)) {
            $this->call(500, "internal_server_error", "Falha ao atualizar usuário.", "error")->back();
            return;
        }

        // 7. Resposta de sucesso
        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => $user->getTypeId(),
            "photo" => $user->getPhoto(),
        ];

        $this->call(200, "success", "Personal atualizado com sucesso", "success")->back($response);
    }

    public function delete(array $data): void
{
    // Verifica token de admin (ou personal, conforme regra)
    if (!$this->authToken(1)) {
        $this->call(401, "unauthorized", "Apenas admin pode deletar.", "error")->back();
        return;
    }
    // Pega o ID da URL (seu router deve passar)
    $id = $data['id'] ?? null;
    if (!$id) {
        $this->call(400, "bad_request", "ID obrigatório.", "error")->back();
        return;
    }
    $user = new User();
    if ($user->softDeleteById($id)) {
        $this->call(200, "success", "Usuário desativado com sucesso.", "success")->back();
    } else {
        $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
    }
}
    // Valida somente Nome e Email, mas pode ser alterada para validar mais campos
    private function validateNameEmail(array $data): bool
    {
        if (
            !isset($data["name"], $data["email"]) ||
            empty($data["name"]) || empty($data["email"]) ||
            !filter_var($data["email"], FILTER_VALIDATE_EMAIL)
        ) {
            return false;
        }
        return true;
    }
}
