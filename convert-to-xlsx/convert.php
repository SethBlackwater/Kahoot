<?php
$the_link = rtrim($_GET['link'],"/");
$the_link = substr($the_link, strrpos($the_link, '/') + 1);
if(strpos($the_link,"=") !== false){
    $the_link = substr($the_link, strrpos($the_link, '=') + 1);
}
$the_ID = preg_replace("/[^a-zA-Z0-9-]+/", "", $the_link);
$the_file = file_get_contents("https://create.kahoot.it/rest/kahoots/$the_ID/card/?includeKahoot=true");
$the_final = json_decode($the_file, true);
foreach($the_final['kahoot']['questions'] as $question) {
    $the_questions[] = $question['question'];
    $the_answer_edit='';
    $the_answer_1[] = $question['choices'][0]['answer'];
    $the_answer_2[] = $question['choices'][1]['answer'];
    $the_answer_3[] = $question['choices'][2]['answer'];
    $the_answer_4[] = $question['choices'][3]['answer'];
    $the_correct_1[] = $question['choices'][0]['correct'];
    $the_correct_2[] = $question['choices'][1]['correct'];
    $the_correct_3[] = $question['choices'][2]['correct'];
    $the_correct_4[] = $question['choices'][3]['correct'];
    if(is_null($the_answer_3)){
        $the_answer_3 = '';
    }
    if(is_null($the_answer_4)){
        $the_answer_4 = '';
    }
    if(is_null($the_correct_3)){
        $the_correct_3 = '';
    }
    if(is_null($the_correct_4)){
        $the_correct_4 = '';
    }
    if($question['choices'][0]['correct']){
        $the_answer_edit = '1,';
    }
    if($question['choices'][1]['correct']){
        $the_answer_edit .= '2,';
    }
    if($question['choices'][2]['correct']){
        $the_answer_edit .= '3,';
    }
    if($question['choices'][3]['correct']){
        $the_answer_edit .= '4,';
    }
    $the_correct_answer[] = rtrim($the_answer_edit, ',');
    $the_times[] = ($question['time']) / 1000;
}
$the_title = preg_replace("/[^a-zA-Z0-9 ]+/", "", $the_final['card']['title']);
include_once("xlsxwriter.class.php");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=$the_title.xlsx");
header('Cache-Control: max-age=0');
$writer = new XLSXWriter();
$writer->writeSheetRow('Sheet 1', array('','','','','','','',''));
$writer->writeSheetRow('Sheet 1', array('','Quiz template','','','','','',));
$writer->writeSheetRow('Sheet 1', array('',"Add questions, at least two answer alternatives, time limit and choose correct answers (at least one). Have fun creating your awesome quiz!        ",'','','','','',''));
$writer->writeSheetRow('Sheet 1', array('',"Remember: questions have a limit of 120 characters and answers can have 75 characters max. Text will turn red in Excel or Google Docs if you exceed this limit. If several answers are correct, separate them with a comma.  ",'','','','','',''));
$writer->writeSheetRow('Sheet 1', array('',"And remember,  if you're not using Excel you need to export to .xlsx format before you upload to Kahoot!",'','','','','',''));
$writer->writeSheetRow('Sheet 1', array('','','','','','','',''));
$writer->writeSheetRow('Sheet 1', array('','Question - max 120 characters','Answer 1 - max 75 characters','Answer 2 - max 75 characters','Answer 3 - max 75 characters','Answer 4 - max 75 characters',"Time limit (sec) – 5, 10, 20, 30, 60, 90, 120, or 240 secs",'Correct answer(s) - choose at least one'));
$x=0;
foreach($the_questions as $question){
    $writer->writeSheetRow('Sheet 1', array(($x + 1),$the_questions[$x],$the_answer_1[$x],$the_answer_2[$x],$the_answer_3[$x],$the_answer_4[$x],$the_times[$x],$the_correct_answer[$x],' '));
    $x++;
}
$writer->writeToStdOut();
?>
