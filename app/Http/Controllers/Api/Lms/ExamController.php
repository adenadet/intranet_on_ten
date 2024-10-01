<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lms\Course;
use App\Models\Lms\Exam;
use App\Models\Lms\ExamType;
use App\Models\Lms\Option;
use App\Models\Lms\Question;
use App\Models\Lms\QuestionType;

class ExamController extends Controller
{
    
    public function index()
    {
        $exams = Exam::orderBy('name', 'ASC')->with('course')->with('lesson')->with('questions.options')->with('exam_type')->paginate(10);
        $exam_types = ExamType::all();
        $question_types = QuestionType::all(); 
        $results = Exam::orderBy('name', 'ASC')->with('course')->with('lesson')->with('results')->paginate(10);

        return response()->json([
            'courses' => Course::with('lessons')->orderBy('name', 'ASC')->get(),
            'exams' => $exams,
            'exam_types' => $exam_types,
            'question_types' => $question_types,
            'results' => $results 
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'pass_mark' => 'required',
            'question' => 'required',
            'description' => 'sometimes',
        ]);

        $exam = Exam::create([
            'name' => $request['name'],
            'pass_mark' => $request['pass_mark'],
            'course_id' => $request['course_id'],
            'lesson_id' => $request['lesson_id'],
            'question' => $request['question'],
            'description' => $request['description'],
            'status' => 1,
            ]);

        for ($i = 1; $i <= $request->input('question'); $i++){
            $question = Question::create([
                'exam_id' => $exam->id,
                'question' => "This is an autogenerated question ".$i,
                'type_id' => 1,
            ]); 
        }
        
        //Return General
        $exams = Exam::orderBy('name', 'ASC')->with('course')->with('lesson')->with('questions.options')->with('exam_type')->paginate(10);
        $exam_types = ExamType::all();
        $question_types = QuestionType::all();

        return response()->json([
            'courses' => Course::with('lessons')->orderBy('name', 'ASC')->get(),
            'exams' => $exams,
            'exam_types' => $exam_types,
            'question_types' => $question_types,
        ]);
    }

    public function show($id)
    {
        /*$this->validate($request, [
            'id' => 'required|numeric',
        ]);*/

        return response()->json([
            'exam' => Exam::where('id', '=', $id)->with('lesson')->with('course')->with('questions.options')->first(),  
            $exam_types = ExamType::all(), 
        ]);
    }

    public function update(Request $request, $id)
    {
        //Validate request
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
            'description' => 'sometimes',
        ]);

        //Update the Exam
        $exam = Exam::find($id);
        
        $exam->name = $request['name'];
        $exam->status = $request['status'];
        $exam->description = $request['description'];

        $exam->save();

        //Return General
        $exams = Exam::orderBy('name', 'ASC')->with('course')->with('lesson')->with('questions.type')->with('exam_type')->paginate(10);
        $exam_types = ExamType::all();
        $question_types = QuestionType::all();
        
        return response()->json([
            'courses' => Course::with('lessons')->orderBy('name', 'ASC')->get(),
            'exams' => $exams,
            'exam_types' => $exam_types,
            'question_types' => $question_types,
        ]);
    }

    public function destroy($id)
    {
        $exam = exam::where('id', '=', $id)->with('questions.options')->first();

        for ($i = 0; $i < count($exam->questions); $i++){
            for ($j = 1; $j < count($exam->questions[$i]->options); $j++){
                $option = Option::find($exam->questions[$i]->options[$j]->id);
                $option->delete();
            }
            $question = Question::find($exam->questions[$i]->id);
            $question->delete();
        }
        $exam->delete();

        //Return General
        $exams = Exam::orderBy('name', 'ASC')->with('course')->with('lesson')->with('questions.type')->with('exam_type')->paginate(10);
        $exam_types = ExamType::all();
        $question_types = QuestionType::all();
        return response()->json([
            'courses' => Course::with('lessons')->orderBy('name', 'ASC')->get(),
            'exams' => $exams,
            'exam_types' => $exam_types,
            'question_types' => $question_types,
        ]);
    }
}
