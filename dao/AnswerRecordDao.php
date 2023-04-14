<?php

namespace dao;

/**
 * 数据库表answer_record接口
 */
interface AnswerRecordDao {
    /**
     * 根据用户id查询用户填写过的问卷的id
     * @param int $user_id  用户id
     * @return array        包含问卷id的array
     */
    public function select_questionnaireid_by_userid(int $user_id): array;

    /**
     * 根据问卷id查询填写过该问卷的用户的id
     * @param int $questionnaire_id 问卷id
     * @return array                包含用户id的array
     */
    public function select_userid_by_questionnaireid(int $questionnaire_id): array;

    /**
     * 根据用户id统计该用户填写过的问卷数量
     * @param int $user_id  用户id
     * @return int          查询到的记录数
     */
    public function count_by_userid(int $user_id): int;

    /**
     * 根据问卷id查询填写过该问卷的用户数量
     * @param int $questionnaire_id 问卷id
     * @return int                  查询到的记录数
     */
    public function count_by_questionnaireid(int $questionnaire_id): int;

    /**
     * 根据用户id和问卷id查询符合要求用户填写记录条数
     * @param int $user_id          用户id
     * @param int $questionnaire_id 问卷id
     * @return int                  查询到的记录数
     */
    public function count_by_userid_questionnaireid(int $user_id, int $questionnaire_id): int;

    /**
     * 新增用户作答记录
     * @param int $user_id          用户id
     * @param int $questionnaire_id 问卷id
     * @return int                  受影响的行数
     */
    public function insert(int $user_id, int $questionnaire_id): int;
}