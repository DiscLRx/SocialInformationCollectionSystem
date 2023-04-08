<?php

namespace dao;

use entity\TextAnswer;

require_once 'entity/TextAnswer.php';

/**
 * 数据库表text_answer接口
 */
interface TextAnswerDao {

    /**
     * 根据问题id和用户id查询填空题作答记录
     * @param int $question_id  问题id
     * @param int $user_id      用户id
     * @return TextAnswer       TextAnswer对象
     */
    public function select_by_questionid_userid(int $question_id, int $user_id): TextAnswer;

    /**
     * 根据问题id查询填空题作答记录
     * @param int $question_id  问题id
     * @return array            包含TextAnswer对象的array
     */
    public function select_by_questionid(int $question_id): array;

    /**
     * 根据用户id查询填空题作答记录
     * @param int $user_id  用户id
     * @return array        包含TextAnswer对象的array
     */
    public function select_by_userid(int $user_id): array;

    /**
     * 新增填空题作答记录
     * @param int $question_id  问题id
     * @param int $user_id      用户id
     * @param string $text      作答内容
     * @return int              受影响的行数
     */
    public function insert(int $question_id, int $user_id, string $text): int;
}