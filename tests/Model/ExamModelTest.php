<?php

use Egzaminer\Model\ExamModel;
use Egzaminer\Tests\Model\EgzaminerArrayDataSet;
use Egzaminer\Tests\Model\EgzaminerTestsDatabaseTestCase;

class ExamModelTest extends EgzaminerTestsDatabaseTestCase
{
    public function getDataSet()
    {
        return new EgzaminerArrayDataSet([
            'exams' => [
                [
                    'id'        => '1',
                    'title'     => 'ExamModelTest title',
                    'questions' => '128',
                    'threshold' => '64',
                    'group_id'  => '5',
                ],
            ],
        ]);
    }

    public function testGetInfo()
    {
        $dataset = [
            'id'                   => '1',
            'title'                => 'ExamModelTest title',
            'questions'            => '128',
            'threshold'            => '64',
            'group_id'             => '5',
            'thresholdPercentages' => '50.0',
        ];

        $model = new ExamModel(self::$pdo);
        $info = $model->getInfo(1);

        $this->assertEquals(
            $dataset,
            $info
        );
    }
}
