<?php

class TypeQuestion extends Zend_Db_Table
{
    // Èìÿ òàáëèöû
    protected $_name = 'type_question';

    public function getIdByValue($type)
    {
        $select = $this->getAdapter()->select()
        ->from(array('tq' => 'type_question'),
            array('id' => 'tq.id'))
        ->where('tq.text like ?', $type);

        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->id;
    }
}