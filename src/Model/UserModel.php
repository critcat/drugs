<?php

namespace App\Model;

class UserModel extends BaseModel
{
    public function login($username, $password)
    {
        $response = $this->apiRequester->request(
            'POST',
            '/api/login_check',
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );
        $this->apiRequester->processResponse($response);

        return $response->toArray()['token'];
    }
}