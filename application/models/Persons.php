<?php

class Persons extends Zend_Db_Table
{
    // Имя таблицы
    protected $_name = 'persons';
    private $m_id = null;

    public function SetId($id)
    {
        $this->m_id = $id;
    }
    public function GetId()
    {
        return $this->m_id;
    }	

    public function InsertPerson($data)
    {
        $newRow = $this->createRow($data);
        $newRow->save();
        $this->SetId($newRow['id']);
    }
    public function FetchPersonId($id)
    {
        $row = $this->fetchRow('id='.$id);
        $this->SetId($row->id);
        return $row; 
    }

    public function getPersonByID($id)
    {
        $select = $this->getAdapter()->select()
                ->from(array('p' => 'persons'))
                ->where('p.id = ?', $id);                
        $stmt = $this->getAdapter()->query($select);        

        return $stmt->fetchObject();
    }

    public function getCountPersonByGroupID($id)
    {
        $select = $this->getAdapter()->select()
                ->from(array('p' => 'persons'))
                ->where('p.group_id = ?', $id);
        
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result);
    }
    
    public function getCountPersonByGroupOther()
    {
        $select = $this->getAdapter()->select()
                ->from(array('p' => 'persons'))
                ->join(array('gq' => 'group_question'),
                    'gq.id = p.group_id',
                    array('gq.css'))
                ->where("gq.css = ''");
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result);
    }
    
        public function getPersonScore($personId)
    {
        $select = "select min(ps.score) as score from `persons_score` ps where ps.`id` = ".$personId;
        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->score;
    }
    
    
}