<?php

namespace dao;

use entity\Questionnaire;

require_once 'entity/Questionnaire.php';

/**
 *数据库表questionnaire接口
 */
interface QuestionnaireDao {

    /**
     * 根据问卷id查询问卷
     * @param int $id           问卷id
     * @return ?Questionnaire   查询到的Questionnaire对象
     */
    public function select_by_id(int $id): ?Questionnaire;

    /**
     * 根据用户id查询问卷
     * @param int $user_id  用户id
     * @return array        包含Questionnaire对象的array
     */
    public function select_by_userid(int $user_id): array;

    /**
     * 新增问卷
     * @param int $user_id      发起问卷的用户id
     * @param string $title     问卷标题
     * @param int $begin_date   开始时间
     * @param int $end_date     结束时间
     * @return int              受影响的行数为0时返回0，否则返回插入值的自增id
     */
    public function insert(int $user_id, string $title, int $begin_date, int $end_date): int;

    /**
     * 更新问卷信息
     * @param int $id           问卷id
     * @param int $user_id      发起问卷的用户id
     * @param string $title     问卷标题
     * @param int $begin_date   开始时间
     * @param int $end_date     结束时间
     * @param bool $enable      可用性
     * @return int              受影响的行数
     */
    public function update_by_id(int $id, int $user_id, string $title, int $begin_date, int $end_date, bool $enable): int;

    /**
     * 更新问卷的可用性
     * @param int $id       问卷id
     * @param bool $enable  可用性
     * @return int          受影响的行数
     */
    public function update_enable_by_id(int $id, bool $enable): int;

    /**
     * 删除问卷
     * @param int $id   问卷id
     * @return int      受影响的行数
     */
    public function delete_by_id(int $id): int;
}