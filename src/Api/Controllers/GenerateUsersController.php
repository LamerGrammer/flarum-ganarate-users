<?php


namespace Imynely\GenerateUsers\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\User\User;
use Flarum\Group\Group;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Illuminate\Support\Str;
use Flarum\Foundation\Paths;
use Carbon\Carbon;

class GenerateUsersController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): JsonResponse
    {
        ini_set('memory_limit', -1);

        $actor = RequestUtil::getActor($request);

        // Check permissions
        if (!$actor->isAdmin()) {
            return new JsonResponse(['error' => 'Permission denied'], 403);
        }

        $requestBody = json_decode($request->getBody());
        $count = $requestBody->count ?? 1;

        $generatedUsers = [];

        $nickNames = $this->randomNickNames($count);

        for ($i = 0; $i < $count; $i++) {
            $user = new User();
            $username = str_replace(' ', '', $nickNames[$i]);
            $raw_nick = bin2hex(random_bytes(8));
            $email = $raw_nick . '@mail.ru';
            $password = Str::random(12);

            $randomDate = Carbon::createFromTimestamp(rand(Carbon::now()->subMonths(2)->timestamp, Carbon::now()->subMonths(1)->timestamp));

            $user->username = $username;
            $user->email = $email;
            $user->password = $password;
            $user->avatar_url = $this->randomAvatar();
            $user->joined_at = $randomDate->format('Y-m-d H:i:s');
            $user->last_seen_at = $randomDate->addMinutes(rand(5, 15))->format('Y-m-d H:i:s');
            $user->is_email_confirmed = 1;
            $user->save();

            $generatedUsers[] = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'avatar_url' => $user->avatar_url,
                'joined_at' => $user->joined_at,
                'last_seen_at' => $user->last_seen_at,
                'is_email_confirmed' => $user->is_email_confirmed
            ];
        }

        return new JsonResponse(['users' => $generatedUsers]);
    }

    private function randomAvatar()
	{
		$dir = resolve(Paths::class)->base . "/public/assets/avatars/";
		$img_a = array();
		if (is_dir($dir)) {
			if ($od = opendir($dir)) {
				while (($file = readdir($od)) !== false) {
					if (strtolower(strstr($file, ".")) === ".jpg" || strtolower(strstr($file, ".")) === ".gif" || strtolower(strstr($file, ".")) === ".png") {
						array_push($img_a, $file);
					}
				}
				closedir($od);
			}
		}
		$rd = rand(0, count($img_a) - 1);

		if (User::where('avatar_url', '=', $img_a[$rd])->exists()) {
			return $this->randomAvatar();
		} else {
			return $img_a[$rd];
		}
	}

    private function randomNickNames(int $count = 1): array
    {
        if (!file_exists(resolve(Paths::class)->storage .'/app/nicknames.txt')) {
            throw new \Exception("The file with nicknames was not found.");
        }

        $nicknames = file(resolve(Paths::class)->storage .'/app/nicknames.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($nicknames)) {
            throw new \Exception("The list of nicknames is empty.");
        }

        $count = min($count, count($nicknames));

        shuffle($nicknames);

        return array_slice($nicknames, 0, $count);
    }
}
