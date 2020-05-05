<?php
namespace App\Entity;

class Presence
{
    private $id_lesson;
    private $id_student;
    private $present;
    private $late;

    public function getIdLesson(){
        return $this->id_lesson;
    }

    public function setIdLesson($id_lesson){
        $this->id_lesson = $id_lesson;
    }

    public function getIdStudent(){
        return $this->id_student;
    }
    
    public function setIdStudent($id_student){
        $this->id_student = $id_student;
    }

    public function getPresent(){
        return $this->present;
    }
    
    public function setPresent($present){
        $this->present = $present;
    }

    public function getLate(){
        return $this->late;
    }
    
    public function setLate($late){
        $this->late = $late;
    }
}