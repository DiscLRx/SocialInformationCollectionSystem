<?php

namespace dao;

use entity\Question;

require_once 'entity/Question.php';

/**
 *数据库表Question接口
 */
interface  QuestionDao{

    /**
     * 根据问题id查询问题
     * @param int $id       问题id
     * @return Question     Question对象
     */
    public function select_by_id(int $id): Question;

    /**
     * 根据问卷id查询问题
     * @param int $questionnaire_id 问卷id
     * @return array                包含Question对象的array
     */
    public function select_by_questionnaireid(int $questionnaire_id): array;

    /**
     * 新增问题
     * @param int $questionnaire_id 问卷id
     * @param int $order            问题序号
     * @param string $type          问题类型
     * @param string $content       问题内容
     * @return int                  受影响的行数
     */
    public function insert(int $questionnaire_id, int $order, string $type, string $content): int;

    /**
     * 更新问题
     * @param int $id               问题id
     * @param int $questionnaire_id 问卷id
     * @param int $order            问题序号
     * @param string $type          问题类型
     * @param string $content       问题内容
     * @return int                  受影响的行数
     */
    public function update_by_id(int $id, int $questionnaire_id, int $order, string $type, string $content): int;

}
