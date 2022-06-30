<?php


namespace App\Service\Admin;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidateException;
use App\Library\Base\BaseService;
use App\Models\Admin\UserModel;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

class LoginService extends BaseService
{
    const LOGIN_IDENTITY = 'admin';

    /**
     * @param $username
     * @param $password
     * @return string
     * @throws NotFoundException
     * @throws ValidateException
     */
    public function handleLogin($username, $password)
    {
        $userInfo = UserModel::query()->where('username', $username)->first();
        if (empty($userInfo)) {
            throw new NotFoundException('用户不存在');
        }
        if (! Hash::check($password, $userInfo->password)) {
            throw new ValidateException('用户名或者密码不正确');
        }

        $token = $this->encodeToken($userInfo->id);
        return ['user_id' => $userInfo->id, 'token' => $token];
    }

    private function encodeToken($id)
    {
        $time = time();
        $payload = [
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + config('jwt_auth.exp'),
            'data' => [
                'id' => $id,
                'identity' => self::LOGIN_IDENTITY
            ]
        ];

        $secret = Uuid::uuid4()->getHex();
        $key = uniqid('jwt');
        $alg =  config('jwt_auth.alg');
        $token = JWT::encode($payload,$key,$alg);
        Redis::setex(strval($secret), config('jwt_auth.exp') + 1800, $key);
        return $token . '.' . $secret;
    }

    /**
     * @param $token
     * @throws UnauthorizedException
     */
    public static function validateToken($token)
    {
        $alg = [
            "typ" => "JWT",
            "alg" => config('jwt_auth.alg')
        ];
        $tokenParams = explode('.', $token);
        $key = Redis::get($tokenParams[3]);
        unset($tokenParams[3]);
        $token = implode('.', $tokenParams);
        $data = JWT::decode($token,$key,$alg);
        if ($data->data->identity != self::LOGIN_IDENTITY) {
            throw new UnauthorizedException();
        }
        app()->instance('id', $data->data->id);
    }

    /**
     * 退出登录
     * @param $token
     */
    public function handLogout($token)
    {
        $tokenParams = explode('.', $token);
        Redis::del($tokenParams[3]);
    }

    /**
     * 修改密码
     * @param $params
     * @return int
     */
    public function handleChangePwd($params)
    {
        return UserModel::query()->where('id', $params['user_id'])->update(
            ['password' => bcrypt($params['password'])]
        );
    }
}