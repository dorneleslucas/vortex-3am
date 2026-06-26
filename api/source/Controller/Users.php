<?php

namespace Source\Controller;

use Source\Models\User;

class Users extends Api
{
    public function register (array $data): void
    {
        $typeId = $this->getTypeId($data, 3);

        if(!in_array($typeId, [2, 3])) {
            $this->call(400,
                "bad_request",
                "Tipo de usuário inválido.",
                "error")->back();
            return;
        }

        if(!isset($data['password']) || empty($data['password'])) {
            $this->call(400,
                "bad_request",
                "A senha é obrigatória.",
                "error")->back();
            return;
        }

        if(!$this->validateNameEmail($data)){
            $this->call(400,
                "bad_request",
                "Nome e e-mail são obrigatórios. O e-mail deve ser válido.",
                "error")->back();
            return;
        }

        $user = new User(
            null,
            $typeId,
            $data['name'],
            $data['email'],
            $data['password']
        );

        if(!$user->insert()) {
            $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => $typeId,
            "photo" => $user->getPhoto(),
            "token" => null
        ];

        $this->call(201,"success","Usuário inserido com sucesso","created")->back($response);
    }

    public function auth (array $data): void
    {
        $typeId = $this->getTypeId($data, 2);

        if(!isset($data['email'], $data['password']) ||
            empty($data['email']) || empty($data['password']) ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->call(
                400,
                "bad_request",
                "E-mail e senha são obrigatórios. O e-mail deve ser válido.",
                "error")->back();
            return;
        }

        $user = new User();
        if(!$user->login($data['email'], $data['password'], $typeId)) {
            $this->call(
                401,
                "unauthorized",
                $user->getErrorMessage(),
                "error")->back();
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
            "success")->back($response);
    }

    public function authAdmin (array $data): void
    {
        if(!isset($data['email'], $data['password']) ||
            empty($data['email']) || empty($data['password']) ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->call(
                400,
                "bad_request",
                "E-mail e senha são obrigatórios. O e-mail deve ser válido.",
                "error")->back();
            return;
        }

        $user = new User();
        if(!$user->login($data['email'], $data['password'], 1)) {
            $this->call(
                401,
                "unauthorized",
                $user->getErrorMessage(),
                "error")->back();
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
            "success")->back($response);
    }

    public function registerAdmin (array $data): void
    {
        if(!isset($data['password']) || empty($data['password'])) {
            $this->call(400,
                "bad_request",
                "A senha é obrigatória.",
                "error")->back();
            return;
        }

        if(!$this->validateNameEmail($data)){
            $this->call(400,
                "bad_request",
                "Nome e e-mail são obrigatórios. O e-mail deve ser válido.",
                "error")->back();
            return;
        }

        $user = new User(
            null,
            1,
            $data['name'],
            $data['email'],
            $data['password']
        );

        if(!$user->insert()) {
            $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "type_id" => 1,
            "photo" => $user->getPhoto(),
            "token" => null
        ];

        $this->call(201,"success","Usuário inserido com sucesso","created")->back($response);
    }

    public function update (array $data): void
    {
        if(!$this->authToken (2)){
            $this->call(
                401,
                "unauthorized",
                "Usuário não está autenticado (sem token ou token inválido).",
                "error")->back();
            return;
        }
        var_dump($this->userAuthId);
        // fazer o update do usuário agora autenticado
        $this->call(200,"success","Usuário atualizado com sucesso","success")->back();
    }

    public function updateAdmin (array $data): void
    {
        if(!$this->authToken (1)){
            $this->call(
                401,
                "unauthorized",
                "Usuário não está autenticado (sem token ou token inválido).",
                "error")->back();
            return;
        }
        // validar campos
        // fazer o update do usuário ADMIN agora autenticado
        $this->call(
            200,
            "success",
            "Usuário atualizado com sucesso",
            "success")->back();

    }

    // Valida somente Nome e Email, mas pode ser alterada para validar mais campos
    private function validateNameEmail(array $data): bool
    {
        if(!isset($data["name"],$data["email"]) ||
            empty($data["name"]) || empty($data["email"]) ||
            !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    private function getTypeId(array $data, int $default): int
    {
        $type = $data["type_id"] ?? $data["type"] ?? $data["user_type"] ?? $default;

        if (is_numeric($type)) {
            return (int) $type;
        }

        return match ($type) {
            "admin" => 1,
            "personal" => 2,
            "aluno" => 3,
            default => $default
        };
    }
}
