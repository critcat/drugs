<?php

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     description="JWT токен для запросов",
 *     bearerFormat="JWT"
 * )
 * @OA\Post(
 *     path="/api/login_check",
 *     tags={"login"},
 *     summary="Получить JWT токен",
 *     operationId="getJWTToken",
 *     @OA\RequestBody(
 *         description="Получение JWT токена для операций с каталогом лекарств",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"username", "password"},
 *                 @OA\Property(
 *                     property="username",
 *                     description="Имя пользователя",
 *                     type="string",
 *                     example="user",
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     description="Пароль",
 *                     type="string",
 *                     format="password",
 *                     example="password",
 *                 ),
 *	           ),
 *	       ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="JWT токен получен",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             example="{""token"": ""eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Mjg5NzIxMDUsImV4cCI6MTYyODk3NTcwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlciJ9.adXGk3Hq1C1Gue9-cV7WtBSzfsNxugWW7aunE7VKzQe15WE7DR0NfCOtvVR-9OwJM2SwxrghsPzB9HoDRzXuAEByZ6VAQRrw-FmRxn4sT9ujjX79rlB4_CkgZ4NSlDgBJ92dRX7qDn-jVuOQVoQW9D6-LrRW1e4DEhXEJPxvELdadfW9tVy9UbloQ6EHinGsoIvqLRViMk38rqtrqDSRh8nal6BGYF_mvQV43kNHIdfQCGyEaxkhHih5Apna6lun4u8OY3yZkgte_8w-fuFwdD49-kZcQwTY5ifohGqEIM8g07cPfhCjs3SpfR0AEnr4mRE97ZzWlA9-0Nh8ahxm-Q""}"
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */