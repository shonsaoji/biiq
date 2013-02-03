<?php

class SubGroupQuestion extends Zend_Db_Table
{
    // Имя таблицы
    protected $_name = 'subgroup_question';

    public function getListByGroupID($groupID)
    {
        $select = $this->getAdapter()->select()
                ->from(array('sgq' => 'subgroup_question'))
                ->where('sgq.group_id = ?', $groupID);

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return $result;
    }
}