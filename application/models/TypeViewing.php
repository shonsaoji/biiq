<?php

class TypeViewing extends Zend_Db_Table
{
    // Èìÿ òàáëèöû
    protected $_name = 'type_viewing';

    public function getIdByValue($type)
    {
        $select = $this->getAdapter()->select()
        ->from(array('tv' => 'type_viewing'),
            array('id' => 'tv.id'))
        ->where('tv.text like ?', $type);

        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->id;
    }
}