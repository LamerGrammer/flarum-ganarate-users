<?php


namespace Imynely\GenerateUsers\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\User\User;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Illuminate\Support\Str;

class GenerateUsersController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): JsonResponse
    {
        $actor = RequestUtil::getActor($request);

        // Check permissions
        if (!$actor->isAdmin()) {
            return new JsonResponse(['error' => 'Permission denied'], 403);
        }

        $requestBody = json_decode($request->getBody());
        $count = $requestBody->count ?? 1;

        $generatedUsers = [];

        for ($i = 0; $i < $count; $i++) {
            $user = new User();
            $username = 'user_' . Str::random(8);
            $email = $username . '@example.com';
            $password = Str::random(12);

            $user->username = $username;
            $user->email = $email;
            $user->password = $password;
            $user->save();

            $generatedUsers[] = [
                'username' => $username,
                'email' => $email,
                'password' => $password
            ];
        }

        return new JsonResponse(['users' => $generatedUsers]);
    }
}
