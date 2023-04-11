<?php

namespace security;

use dto\response\TokenResDto;
use entity\User;
use framework\AuthFilter;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use framework\util\JWT;
use framework\util\Time;
use ReflectionClass;
use RuntimeException;

require_once 'security/RequireAuthority.php';

class TokenAuthFilter implements AuthFilter {


    public function do_filter(?string $token, string $class_name, string $func_name): array {
        $require_auths = $this->get_require_authorities($class_name, $func_name);
        if (in_array("PermitAll", $require_auths)) {
            return ['result' => true, 'data' => ['msg' => 'permit all']];
        }

        $ret = $this->token_verify($token);
        return match ($ret['result']) {
            'denied' => ['result' => false, 'data' => ['msg' => 'token error']],
            'expired' => ['result' => false, 'data' => ['msg' => 'token expired']],
            'refresh' => ['result' => false, 'data' => ['msg' => 'token refresh', 'payload' => $ret['payload']]],
            'passed' => in_array($ret['user']->get_authority(), $require_auths) || $ret['user']->get_authority() === "Admin" ?
                ['result' => true, 'data' => ['msg' => 'auth ok', 'user' => $ret['user']]] : ['result' => false, 'data' => ['msg' => 'authority error']]
        };
    }

    private function get_require_authorities(string $class_name, string $func_name): array {
        $reflection = new ReflectionClass($class_name);
        $method = $reflection->getMethod($func_name);

        foreach ($method->getAttributes() as $attribute) {
            if ($attribute->getName() === trim(RequireAuthority::class, '\\')) {
                return $attribute->newInstance()->value;
            }
        }
        return ["PermitAll"];
    }

    /**
     * @param ?string $token
     * @return array    ['result'=>'验证结果','user'=>用户数据, 'payload'=>[...]]
     */
    private function token_verify(?string $token): array {

        if (!isset($token)) {
            return ['result' => 'denied'];
        }
        //提取token负载
        $jwt = new JWT();
        try {
            $payload = (array)$jwt->decode($token);
        } catch (RuntimeException) {
            return ['result' => 'denied'];
        }
        $uid = $payload['uid'];
        $auth_ts = $payload['ts'];

        //验证token有效期
        $exp_ts = Time::ts_after($auth_ts, 7 );
        $current_ts = Time::current_ts();
        if ($exp_ts < $current_ts) {
            return ['result' => 'expired'];
        } else {
            $refresh_ts = Time::ts_after($auth_ts, 0, 1);
            if ($refresh_ts <= $current_ts) {
                return ['result' => 'refresh', 'payload' => $payload];
            }
        }

        $redis = new RedisExecutor();
        $user_cache = $redis->get("uid_{$uid}");
        //redis中没有用户信息则认为用户登录过期
        if ($user_cache === false) {
            return ['result' => 'expired'];
        }
        $user = JSON::unserialize($user_cache, User::class);
        return ['result' => 'passed', 'user' => $user];
    }

    public function do_after_accept(mixed $data): void {
        $msg = $data['msg'];
        if ($msg==='auth ok'){
            $GLOBALS['USER'] = $data['user'];
        }
    }

    public function do_after_denied(mixed $data): ResponseModel {
        $msg = $data['msg'];
        if ($msg==='token refresh'){
            //更新token创建时间,实现token续期
            $payload = $data['payload'];
            $payload['ts'] = Time::current_ts();
            $token = (new JWT())->create($payload);
            return Response::refresh_token(new TokenResDto($token));
        }else{
            return Response::permission_denied();
        }
    }
}