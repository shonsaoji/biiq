<?php

class GroupQuestion extends Zend_Db_Table
{
    // Имя таблицы
    protected $_name = 'group_question';

    public function getListGroup()
    {
        $select = $this->getAdapter()->select()
                ->from(array('gr' => 'group_question'))
                ->order('gr.text asc');

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return $result;
    }

    public function getIdBySic($sic)
    {
        $select = $this->getAdapter()->select()
        ->from(array('gq' => 'group_question'),
            array('gq' => 'gq.id'))
        ->where('gq.sic like ?', $sic);
        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->gq;
    }

}