<?php

class AnswerVariants extends Zend_Db_Table
{
    // Имя таблицы
    protected $_name = 'answer_variants';

    public function getByQuestionId($id)
    {
        $select = $this->getAdapter()->select()
                ->from(array('av' => 'answer_variants'))
                ->where('av.question_id = ?', $id);

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return $result;
    }
    
}