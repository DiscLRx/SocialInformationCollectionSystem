<?php

namespace dao;

require_once 'entity/Option.php';
require_once 'entity/User.php';

/**
 * 数据库表choice_answer接口
 */
interface ChoiceAnswerDao {

    /**
     * 根据选项id查询用户
     * @param int $option_id    选项id
     * @return array            包含User对象的array
     */
    public function select_user_by_optionid(int $option_id): array;

    /**
     * 根据用户id查询选项
     * @param int $user_id  用户id
     * @return array        包含Option对象的array
     */
    public function select_option_by_userid(int $user_id): array;

    /**
     * 新增选择题作答记录
     * @param int $option_id    选项id
     * @param int $user_id      用户id
     * @return int              受影响的行数
     */
    public function insert(int $option_id, int $user_id): int;

}