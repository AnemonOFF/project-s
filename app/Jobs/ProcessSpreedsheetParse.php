<?php

namespace App\Jobs;

use App\Models\Block;
use App\Models\Course;
use App\Models\Mark;
use App\Models\Platform;
use App\Models\Spreedsheet;
use App\Models\Student;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProcessSpreedsheetParse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $spreedsheet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Spreedsheet $spreedsheet)
    {
        $this->spreedsheet = $spreedsheet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $spreedsheet = $this->spreedsheet;
        $file = $this->LoadSpreedsheet($spreedsheet);
        $type = $spreedsheet->type;
        $course = Course::find($spreedsheet->course_id);
        $platform = Platform::find($course->platform_id);
        $this->DeleteOldData($course);

        switch($type){
            case 'ulearn':
                $this->ParseUlearn($file, $course);
                break;
            case 'default':
                $this->ParseDefault($file, $course);
                break;
        }
    }

    protected function LoadSpreedsheet(Spreedsheet $spreedsheet) : Spreadsheet
    {
        ini_set('memory_limit', -1);
        $filePath = storage_path("app/$spreedsheet->path");
        //if(!Storage::exists($filePath))
        //    throw new FileExistsException("$filePath is not exist");
        $fileType = IOFactory::identify($filePath);
        $reader = IOFactory::createReader($fileType);
        $file = $reader->load($filePath);
        return $file;
    }

    protected function DeleteOldData(Course $course): void
    {
        $courseId = $course->id;
        Block::where('course_id', $courseId)->get()->each(function(Block $block){
            $block->delete();
        });
    }

    protected function ParseUlearn(Spreadsheet $file, Course $course): void
    {
        //rows info
        $blocksRow = 0;
        $tasksRow = 1;
        $maximumsRow = 2;
        $marksFirstRow = 3;

        $data = $file->getActiveSheet()->toArray();
        
        //parse blocks names
        $blocks = [];
        $column = -1;
        foreach($data[$blocksRow] as $cell)
        {
            $column++;
            if(empty($cell) || in_array($cell, ['За весь курс', 'Преподавателю о курсе']))
                continue;
            if(count($blocks) > 0)
                $blocks[count($blocks) - 1]['length'] = $column - $blocks[count($blocks) - 1]['column'];
            array_push($blocks, ['name' => $cell, 'column' => $column, 'tasks' => []]);
        }
        $blocks[count($blocks) - 1]['length'] = count($data[0]) - $blocks[count($blocks) - 1]['column'];
        //find custom 'teacher' columns
        $customTaskColumns = [];
        $isFound = False;
        for ($i = 0; $i < $blocks[0]['column']; $i++)
        {
            if($data[$blocksRow][$i] == 'Преподавателю о курсе')
                $isFound = True;
            if(!$isFound)
                continue;
            if(!empty($data[$blocksRow][$i]) && !$data[$blocksRow][$i] == 'Преподавателю о курсе')
                break;
            array_push($customTaskColumns, $data[$tasksRow][$i]);
        }

        //parse tasks names
        $currentBlockNumber = 0;
        for($column = $blocks[0]['column']; $column < $blocks[count($blocks) - 1]['column'] + $blocks[count($blocks) - 1]['length']; $column++)
        {
            $cell = $data[$tasksRow][$column];
            if(strpos($cell, ':') === false && !in_array($cell, $customTaskColumns))
                continue;
            if($column >= $blocks[$currentBlockNumber]['column'] + $blocks[$currentBlockNumber]['length'])
                $currentBlockNumber++;
            array_push($blocks[$currentBlockNumber]['tasks'], ['name' => $cell, 'max' => $data[$maximumsRow][$column], 'column' => $column, 'marks' => []]);
        }

        //parse students marks
        $students = [];
        for($row = $marksFirstRow; $row < count($data); $row++)
        {
            $rowData = $data[$row];
            $student = Student::firstOrCreate([
                'full_name' => $rowData[0],
                'email' => $rowData[1],
            ]);
            array_push($students, $rowData[0]);
            $studentId = $student->id;
            foreach($blocks as &$block)
            {
                foreach($block['tasks'] as &$task)
                {
                    array_push($task['marks'], ['mark' =>$rowData[$task['column']], 'studentId' => $studentId]);
                }
            }
        }
        unset($block);
        unset($task);
        //upload data to database
        $courseId = $course->id;
        $marks = [];
        foreach($blocks as $block)
        {
            $blockId = Block::create([
                'course_id' => $courseId,
                'name' => $block['name'],
            ])->id;
            foreach($block['tasks'] as $task)
            {
                $taskEloquent = Task::create([
                    'block_id' => $blockId,
                    'name' => $task['name'],
                    'points_max' => $task['max'],
                ]);
                //$marks = [];
                foreach($task['marks'] as $mark)
                {
                    array_push($marks, [
                        'student_id' => $mark['studentId'],
                        'task_id' => $taskEloquent->id,
                        'mark' => $mark['mark'],
                    ]);
                }
                //$taskEloquent->marks()->createMany($marks);
            }
        }

        //Write in file
        Storage::disk('local')->put('marks.json', json_encode($marks));
        $fileName = 'temp_'.time().'.txt';
        $filePath = $_SERVER['DOCUMENT_ROOT'].'../../userdata/temp/upload/';
        $loadDataFile = fopen($filePath.$fileName, 'a');
        foreach($marks as $mark)
        {
            fwrite($loadDataFile, implode(';', $mark)."\n");
        }
        fclose($loadDataFile);
        $query = "LOAD DATA INFILE '../../userdata/temp/upload/".$fileName."' INTO TABLE marks FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' (student_id, task_id, mark) SET created_at=NOW(),updated_at=null";
        DB::connection()->getPdo()->exec($query);
        unlink($filePath.$fileName);
    }

    protected function ParseDefault(Spreadsheet $file, Course $course) : void
    {
        
    }
}
