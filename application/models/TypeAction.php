<?php

class TypeAction extends Zend_Db_Table
{
    // Èìÿ òàáëèöû
    protected $_name = 'type_action';

    public function getIdByValue($type)
    {
        $select = $this->getAdapter()->select()
        ->from(array('ta' => 'type_action'),
            array('id' => 'ta.id'))
       ->where('ta.text like ?', $type);

        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->id;
    }
}