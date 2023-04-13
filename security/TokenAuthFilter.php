<?php

namespace security;

use dto\response\RefreshTokenDto;
use entity\User;
use framework\AuthFilter;
use framework\exception\JSONSerializeException;
use framework\RedisExecutor;
use framework\response\Response;
use framework\response\ResponseModel;
use framework\util\JSON;
use framework\util\Time;
use ReflectionClass;
use RuntimeException;

require_once 'security/RequireAuthority.php';
require_once 'dto/response/RefreshTokenDto.php';
require_once 'security/TokenAuthConfigLoader.php';
require_once 'entity/User.php';

class TokenAuthFilter implements AuthFilter {

    private TokenAuthConfigLoader $config_loader;

    public function __construct() {
        $this->config_loader = new TokenAuthConfigLoader();
    }

    public function do_filter(?string $token, string $class_name, string $func_name): array {
        $require_auths = $this->get_require_authorities($class_name, $func_name);
        if (in_array("PermitAll", $require_auths)) {
            return ['result' => true, 'data' => ['code' => -1]];
        }

        $ret = $this->token_verify($token);
        return match ($ret['result']) {
            'not_signin' => ['result' => false, 'data' => ['code' => 11]],
            'refresh' => ['result' => false, 'data' => ['code' => 13, 'payload' => $ret['payload']]],
            'passed' => (function($user, $require_auths){
                $authority = $user->get_authority();
                if (in_array($authority, $require_auths) || $authority === "Admin"){
                    return ['result' => true, 'data' => ['code' => 0, 'user' => $user]];
                } else {
                    return ['result' => false, 'data' => ['code' => 12]];
                }
            })($ret['user'], $require_auths)
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
            return ['result' => 'not_signin'];
        }
        //提取token负载
        try {
            $payload = (array)($this->config_loader->get_jwt()->decode($token));
        } catch (RuntimeException) {
            return ['result' => 'not_signin'];
        }
        $uid = $payload['uid'];
        $auth_ts = $payload['ts'];

        //验证token有效期
        $expcfg = $this->config_loader->get_expcfg();
        $rfrcfg = $this->config_loader->get_rfrcfg();
        $exp_ts = Time::ts_after($auth_ts,
            $expcfg->d, $expcfg->h, $expcfg->m, $expcfg->s, $expcfg->ms);
        $current_ts = Time::current_ts();
        if ($exp_ts < $current_ts) {
            return ['result' => 'not_signin'];
        } else {
            $refresh_ts = Time::ts_after($auth_ts,
                $rfrcfg->d, $rfrcfg->h, $rfrcfg->m, $rfrcfg->s, $rfrcfg->ms);
            if ($refresh_ts <= $current_ts) {
                return ['result' => 'refresh', 'payload' => $payload];
            }
        }

        $redis = new RedisExecutor(0);
        $user_cache = $redis->get("uid_{$uid}");
        //redis中没有用户信息则认为用户登录过期
        if ($user_cache === false) {
            return ['result' => 'not_signin'];
        }
        $user = JSON::unserialize($user_cache, User::class);
        return ['result' => 'passed', 'user' => $user];
    }

    public function do_after_accept(mixed $data): void {
        $code = $data['code'];
        if ($code===0){
            $GLOBALS['USER'] = $data['user'];
        }
    }

    public function do_after_denied(mixed $data): ResponseModel {
        $code = $data['code'];
        return match ($code){
            11 => Response::require_to_signin(),
            12 => Response::permission_denied(),
            //更新token创建时间,实现token续期
            13 => (function($payload){
                $payload['ts'] = Time::current_ts();
                $token = $this->config_loader->get_jwt()->create($payload);
                return Response::refresh_token(new RefreshTokenDto($token));
            })($data['payload'])
        };
    }
}