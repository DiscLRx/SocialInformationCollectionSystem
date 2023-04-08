<?php

namespace dao;

use entity\Option;

require_once 'entity/Option.php';

/**
 *数据库表Option接口
 */
interface OptionDao {

    /**
     * 根据选项id查询选项
     * @param int $id   选项id
     * @return Option   Option对象
     */
    public function select_by_id(int $id): Option;

    /**
     * 根据问题id查询选项
     * @param int $question_id  问题id
     * @return array            包含Option对象的array
     */
    public function select_by_questionid(int $question_id): array;

    /**
     * 新增选项
     * @param int $question_id  问题id
     * @param int $order        选项序号
     * @param string $content   选项内容
     * @return int              受影响的行数
     */
    public function insert(int $question_id, int $order, string $content): int;

    /**
     * 更新选项
     * @param int $id           选项id
     * @param int $question_id  问题id
     * @param int $order        选项序号
     * @param string $content   选项内容
     * @return int              受影响的行数
     */
    public function update_by_id(int $id, int $question_id, int $order, string $content): int;

}