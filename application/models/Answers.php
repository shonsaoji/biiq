<?php

class Answers extends Zend_Db_Table
{
    // Имя таблицы
    protected $_name = 'answers';


    public function countAnsweredByPerson($personId = null)
    {
        $select = $this->getAdapter()->select()
                ->from(array('a' => 'answers'),
                    array('a.id', 'a.person_id','a.question_id'))
                ->join(array('p' => 'persons'),
                    'p.id = a.person_id',
                    array('p.first_name'))
                ->join(array('q' => 'questions'),
                    'q.id = a.question_id',
                    array())
                ->where('p.id = ?', $personId)
                ->where('q.parent_id is null');

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result); 
    }
    public function countAnsweredMainQuestionPage($personId, $numberPage)
    {
        $select = $this->getAdapter()->select()
                ->from(array('a' => 'answers'),
                    array('a.id', 'a.person_id','a.question_id'))
                ->join(array('p' => 'persons'),
                    'p.id = a.person_id',
                    array('p.first_name'))
                ->join(array('q' => 'questions'),
                    'q.id = a.question_id',
                    array())
                ->where('p.id = ?', $personId)
                ->where('q.page = ?', $numberPage)
                ->where('q.parent_id is null');

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        return count($result);
    }

    public function countAnsweredQuestionPage($personId, $numberPage)
    {
        $select = "select count(distinct an.`question_id`) as total from `answers` an
                        left join `questions` q on q.`id` = an.`question_id`
                    where
                        an.`person_id` = ".$personId." and q.`page` = ".$numberPage;
        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->total;
    }

    public function getNotAnsweredQuestionPage($personId, $numberPage)
    {
        $typeActionClass = new TypeAction();
        $typeActionNoId = $typeActionClass->getIdByValue("No");
        $select = "select answer_list.question_id as question from
(select an.`question_id` as question_id, sum(
if(av1.`type_action` = ".$typeActionNoId.", 1,
    if(q.id in (
select qi.parent_id from `questions` qi
left join `questions` qi1 on qi1.`parent_id` = qi.`id`
where qi1.id is null and qi.`parent_id` is not null), 2,1))) as total
from `answers` an
left join `questions` q on q.`id` = an.`question_id`
left join `answer_variants` av1 on av1.`id` = an.`answer_variant_id`
where an.`person_id` = ".$personId." and q.`page` = ".$numberPage." and q.`parent_id` is null
group by an.`question_id`
) as main_question
left join (select an.`question_id` as question_id, if(qi1.id is null, 1, 2) as total
from `answers` an
left join `questions` q on q.`id` = an.`question_id`
left join `questions` qi1 on qi1.`parent_id` = q.`id` and qi1.id in (
select q1.`id`  from `answers` ans
left join `questions` q1 on q1.`id` = ans.`question_id`
where q1.`parent_id` is not null and ans.`person_id` = ".$personId." and q1.`page` = ".$numberPage."
)
where
     an.`person_id` = ".$personId." and q.`page` = ".$numberPage." and q.`parent_id` is null) as answer_list on answer_list.question_id = main_question.question_id
where
     answer_list.total != main_question.total
limit 0,1
UNION ALL
select q.id as question
from `questions` q
left join `answers` an on an.`question_id` = q.`id` and an.`person_id` = ".$personId."
where q.page = ".$numberPage."  and q.`parent_id` is null and an.`id` is null";
        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        if(count($result) > 0)
        foreach($result as $res)
            return $res->question;
        return 0;
    }

    public function countQuestionPage($personId, $numberPage)
    {
        $typeActionClass = new TypeAction();
        $typeActionNoId = $typeActionClass->getIdByValue("No");

        $select = "select distinct an.`question_id`, sum(
if(av1.`type_action` = ".$typeActionNoId.", 1,
    if(q.id in (
select qi.parent_id from `questions` qi
left join `questions` qi1 on qi1.`parent_id` = qi.`id`
where qi1.id is null and qi.`parent_id` is not null), 2,1))) as total
from `answers` an
left join `questions` q on q.`id` = an.`question_id`
left join `answer_variants` av1 on av1.`id` = an.`answer_variant_id`
where an.`person_id` = ".$personId." and q.`page` = ".$numberPage." and q.`parent_id` is null";

        $stmt = $this->getAdapter()->query($select);

        return $stmt->fetchObject()->total;
    }


    public function totalBar()
    {

        $select = "select group_id, name, sum(sum_score)/count_groups as sum_score  from (
select group_id, name, sum(score) as sum_score, count(groups) as count_groups from (
select
gq.id as group_id,
if(gq.`css` = '', 'Other', gq.`text`) as name,
sum(av.`score`) as score,
p.`group_id` as groups
from answers a
left join persons p on p.id = a.person_id
left join answer_variants av on av.id = a.answer_variant_id
left join `group_question` gq on gq.`id` = p.`group_id`
where p.id is not null and gq.`id` is not null and av.score > 0
group by p.id, p.`group_id`) sel
group by name
UNION ALL
select gq.`id` as group_id, if(gq.`css` = '', 'Other', gq.`text`) as name, 0 as sum_score, 1 count_groups from
`group_question` gq
where id not in (
select p.`group_id`
from answers a
left join persons p on p.id = a.person_id
left join answer_variants av on av.id = a.answer_variant_id
left join `group_question` gq on gq.`id` = p.`group_id`
where p.id is not null and gq.`id` is not null and av.score > 0
group by p.`group_id`
)) as sss
group by name
order by sum_score desc";

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
        
        return $result;
    }

    public function saveMain($idPerson, $idQuestion, $idAnswer, $text)
    {
        $data = array
        (
            'person_id' => $idPerson,
            'question_id' => $idQuestion,
            'answer_variant_id' => $idAnswer,
            'another_answer' => htmlspecialchars($text),
        );
        $res = 0;

        $select = $this->getAdapter()->select()
        ->from(array('a' => 'answers'))
        ->where('a.person_id = ?', $idPerson)
        ->where('a.question_id = ?', $idQuestion);

        $stmt = $this->getAdapter()->query($select);
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
        $res = count($result);

        if ($res == 0)
        {
            $this->insert($data);
        } else
        if($res > 0)
        {
            $select = $this->getAdapter()->select()
            ->from(array('a' => 'answers'),
                array('a.id', 'a.person_id','a.question_id'))
            ->join(array('q' => 'questions'),
                'q.id = a.question_id',
                array())
            ->where('q.parent_id = ?', $idQuestion);
            ;
            $stmt = $this->getAdapter()->query($select);
            $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
            if(count($result) > 0)
            {
                foreach($result as $ans)
                {
                    $where = 'id = ' . $ans->id;
                    $this->delete($where);
                }
            }
            $select = $this->getAdapter()->select()
            ->from(array('a' => 'answers'))
            ->join(array('p' => 'persons'),
                'p.id = a.person_id',
                array())
            ->join(array('q' => 'questions'),
                'q.id = a.question_id',
                array())
            ->where('p.id = ?', $idPerson)
            ->where('q.id = ?', $idQuestion);
            $stmt = $this->getAdapter()->query($select);
            $la = $stmt->fetchObject();

            $where = 'id = ' . $la->id;
            $this->update($data, $where);
        }
    }

    public function SaveAnswerCheckBox($personId,$idQuestion,$idAnswer,$flag)
    {
        if($flag == 1)
        {
            $data = array
            (
                'person_id' => $personId,
                'question_id' => $idQuestion,
                'answer_variant_id' => $idAnswer
            );
            
            $select = $this->getAdapter()->select()
            ->from(array('a' => 'answers'))
            ->where('a.person_id = ?', $personId)
            ->where('a.question_id = ?', $idQuestion)
            ->where('a.answer_variant_id = ?', $idAnswer);

            $stmt = $this->getAdapter()->query($select);
            $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);
            $res = count($result);
            if($res > 0)
                $this->deleteAnswerCheckBox($personId,$idQuestion,$idAnswer);
            $this->insert($data);
        }
        else
        {
            $this->deleteAnswerCheckBox($personId,$idQuestion,$idAnswer);
        }
    }

    private function deleteAnswerCheckBox($personId,$idQuestion,$idAnswer)
    {
        $where = 'person_id = ' . $personId.' and question_id = '.$idQuestion.' and answer_variant_id = '.$idAnswer;
        $this->delete($where);
    }
    public function scoreByPerson($personId)
    {
        if($this->countAnsweredByPerson($personId)==0)
            return 0;
        $select = $this->getAdapter()->select()
        ->from(array('a' => 'answers'),
            array())
        ->joinLeft(array('p' => 'persons'),
            'p.id = a.person_id',
            array())
        ->joinLeft(array('av' => 'answer_variants'),
            'av.id = a.answer_variant_id',
            array('sum_score' => 'sum(av.score)/2.1')) //Change this to use the total questions from the database instead of Hard Code
        ->where('p.id = ?', $personId)
        ->group('p.id');

        $stmt = $this->getAdapter()->query($select);
        return $stmt->fetchObject()->sum_score;
    }

    public function industryText($personId)
    {
        if($this->countAnsweredByPerson($personId)==0)
            return 0;
        $select = $this->getAdapter()->select()
        ->from(array('p' => 'persons'),
            array())
        ->joinLeft(array('gq' => 'group_questions'),
            'p.group_id = gq.id',
            array('industry_text'=> 'gq.text'))
        ->where('p.id = ?', $personId);
        
        $stmt = $this->getAdapter()->query($select);
        return $stmt->fetchObject()->industry_text;
    }
    
    
}