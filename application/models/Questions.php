<?php

class Questions extends Zend_Db_Table
{
    // Имя таблицы
    protected $_name = 'questions';

    public function getByPage($page = null)
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'))
                ->where('q.page = ?', $page)
                ->where('q.parent_id is null');
                ;

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return $result;
    }

    public function getByQuestionId($id)
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'))
                ->where('q.id = ?', $id);

        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject();
    }

    public function getByChildPage($id)
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'))
                ->where('q.parent_id = ?', $id);

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return $result;
    }

    public function countTotal()
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'))
                ->where('q.parent_id is null');

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result);
    }

    public function pagesCount()
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'),
                      array('page_max' => 'MAX(page)'))
                ->where('q.parent_id is null');

        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->page_max;
    }

    public function countByPage($page)
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'))
                ->where('q.page = ?', $page)
                ->where('q.parent_id is null');

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result);
    }

    public function sumQuestionsPrevPage($page)
    {
        $select = $this->getAdapter()->select()
                ->from(array('q' => 'questions'))
                ->where('q.page < ?', $page)
                ->where('q.parent_id is null');
        
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result);
    }

    public function getAnsweredQuestionsbyPersion($personId)
    {
        $select = $this->getAdapter()->select()
        ->from(array('a' => 'answers'),
            array('answer_id' => 'a.id', 'another_answer' => 'a.another_Answer'))
        ->join(array('q' => 'questions'),
            'q.id = a.question_id',
            array('question_id' => 'q.id','question_text' => 'q.text'))
        ->join(array('av' => 'answer_variants'),
            'av.id=a.answer_Variant_Id and av.question_Id=q.id',
            array('answer_variant_id'=>'av.id','answer_variant_text'=>'av.text', 'type_action'=>'av.type_action'))
        ->where('a.person_id = ?', $personId);

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
        if(count($result) == 0)
            return null;
        return $result;
    }
}