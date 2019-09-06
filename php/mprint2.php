<?php
    session_start();
    if (isset($_SESSION['Admin_Name'])) {
        require_once 'includes/database.php';
        $db = new Database();

    if (isset($_POST['admnPrintReport'])) {
        $gtyears = $db->getRows('SELECT * FROM `years`');
        $gtsection = $db->getRows('SELECT * FROM `section`');
        $yearsOptn = '';
        $secTr = '';
        foreach ($gtyears as $val) {
            $yearsOptn .= '<option value="'.$val['id'].'">'.$val['Year'].'</option>';
        }
        foreach ($gtsection as $val) {
            $secTr .= '<option value="'.$val['id'].'">'.$val['Section'].'</option>';
        }
    $out = '
    <div class="col-md-2 maxvh70 pdlft0">
        <div class="box box-primary box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Year/Class</h3>
        </div>
        <div class="box-body bg-info">
                <select class="form-control input-sm marb5" id="printScoreYearId"> 
                <option value="">Select Year</option>
                '.$yearsOptn.'
                </select>
                <select class="form-control input-sm marb5" id="printScoreTermId"> 
                <option value="">Select Term</option>
                </select>
                <select class="form-control input-sm marb5" id="printScoreSectionId"> 
                <option value="">Select Section</option>
                '.$secTr.'
                </select>
                
                <select class="form-control input-sm marb5" id="printScoreClassId"> 
                <option value="">Select Class</option>
                </select>
                <div class="col-md-12 pad0" id="printBtnCont">
                </div>
        </div>
        </div>
        </div>
        <div class="col-md-10 maxvh70">
        <div class="box box-primary box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Report Sheets</h3>
        </div>
        <div class="box-body bg-info" id="admnReportSheetsCont">
                
        </div>
        </div>
    ';
    echo $out;
    } 
    // rpt btn show
    if (isset($_POST['prntRptBtnShow'])) {
        $clsId = $_POST['clsId'];
        $gtClss = $db->getRow('SELECT * FROM `classess`');
        echo '<button class="btn btn-primary btn-xs marb5" id="adminPreviewReportBtn">
        Preview '.$gtClss['Class'].' Report
        </button>
        <button class="btn btn-info btn-xs" id="adminPrintReportBtn">
        Print '.$gtClss['Class'].' Report
        </button>';
    }
   
    function gtSubTotalScr ($ClssId, $YrId, $TrmId, $subjId) {
        global $db;
        $gtSubTtotals = $db->getRow('SELECT COUNT(*) AS NoStuPerCrs, SUM(`Total_Score`) AS TtSubScore 
        FROM `scores` WHERE `Students_Classess_id` = ? AND `Terms_id` = ? AND `Terms_Years_id` = ? 
        AND `Subjects_id` = ?', [$ClssId, $TrmId, $YrId, $subjId]);
        return $gtSubTtotals;
    }

    function gtScors ($stuId, $ClssId, $YrId, $TrmId, $ToStu) {
        global $db;
        $subj  = '';

        $gtScrsss = $db->getRows('SELECT `Subject`, `First_CA`, `Second_CA`, `Exam`, `Total_Score`, `Class`, 
        `Section`, `Term`, `Year`, Sub.id AS SubjectId FROM `scores` AS Scr INNER JOIN `classess` AS Cls ON Scr.Students_id = ? 
        AND Scr.Terms_id = ? AND Scr.Terms_Years_id = ? AND Cls.id = ? 
        INNER JOIN section AS Sec ON Sec.id = Cls.Section_id 
        INNER JOIN subjects AS Sub ON Sub.Section_id = Sec.id AND Sub.id = Scr.Subjects_id
        INNER JOIN terms AS Trm ON Trm.id = Scr.Terms_id 
        INNER JOIN years AS Yrs ON Yrs.id = Trm.Years_id', [$stuId, $TrmId, $YrId, $ClssId]);

       // $ttAllScrs = 0;
       
       $gtTtotals = $db->getRow('SELECT COUNT(*) AS NoSbu, SUM(`Total_Score`) AS TTScore 
        FROM `scores` WHERE `Students_id` = ? AND `Students_Classess_id` = ? 
        AND `Terms_id` = ? AND `Terms_Years_id` = ?', 
        [$stuId, $ClssId, $TrmId, $YrId]);

        foreach ($gtScrsss as $val) {
            // $tts = getTotlScrs($stuId, $ClssId, $TrmId, $YrId);
            // $tts['TTScore'];
            $subTotalScr = gtSubTotalScr ($ClssId, $YrId, $TrmId, $val['SubjectId']);

            $StuClsAver = round($subTotalScr['TtSubScore'] / $gtTtotals['NoSbu']);
            // $StuClsAver = round($gtTtotals['TTScore'] / $gtTtotals['NoSbu']);
            $SubAver = round($subTotalScr['TtSubScore'] / $subTotalScr['NoStuPerCrs']);

            $ttScr = $val['Total_Score'];

            // echo '<pre> No of Sub: ' .$gtTtotals['NoSbu'];
            // echo 'Total Score : ' .$gtTtotals['TTScore'] .'Total Stud: ' .$ToStu .'</pre>';

           if ($ttScr >= 70) {
                $grd = 'A';
                $cmnt = 'Excellent';
           } elseif ($ttScr >= 60) {
                $grd = 'B';
                $cmnt = 'Very Good';
           } elseif ($ttScr >= 50) {
                $grd = 'C'; 
                $cmnt = 'Good';    
           } elseif ($ttScr >= 40) {
                $grd = 'P';
                $cmnt = 'Poor';     
            } elseif ($ttScr <= 44) {
                $grd = 'F';
                $cmnt = 'Fail';     
           }

            $subj .= '       
            <div class="row brd1q" style="margin: 2px;">
                <div class="col-xs-4 brdrg1q padlft10 pad03">
                    '.ucwords(strtolower($val['Subject'])).'
                </div>

                <div class="col-xs-2 brdrg1q pad03">
                    <div class="col-xs-4 padlft10">
                    '.$val['First_CA'].'
                    </div>
                    <div class="col-xs-4 padlft10">
                    '.$val['Second_CA'].'
                    </div>
                    <div class="col-xs-4 padlft10">
                    '.$val['Exam'].'
                    </div>
                </div>

                <div class="col-xs-1 brdrg1q padlft10 pad03">
                '.$val['Total_Score'].'
                </div>
                <div class="col-xs-1 brdrg1q padlft10 pad03">
                    '.$SubAver.'
                </div>
                <div class="col-xs-1 brdrg1q padlft10 pad03">
                    '.$grd.'
                </div>

                <div class="col-xs-3 padlft10" style="padding: 1px;">
                    '.$cmnt.'
                </div>
            </div>';
        }
      return $subj;
    }  

    function getNumberofSubjects ($stuId, $ClsId, $TrmId, $YrId) {
        global $db;
        $gtNoofSub = $db->getRow('SELECT COUNT(*) AS Noofsub FROM scores 
        WHERE Students_id = ? AND Students_Classess_id = ? AND Terms_id = ? 
        AND Terms_Years_id = ?', [$stuId, $ClsId, $TrmId, $YrId]);
        return $gtNoofSub['Noofsub'];
    }

    function generatReport ($YrId, $TrmId, $SecId,  $ClsId) {
        global $db;
        $out = '';
        $gtStu = $db->getRows('SELECT First_Name, Middle_Name, Last_Name, Reg_No, Students_id, 
        SUM(Total_Score) AS TotalScore FROM scores AS scr JOIN students AS stu 
        ON scr.Students_Classess_id = ? AND scr.Terms_id = ? AND scr.Terms_Years_id = ? 
        AND stu.id = scr.Students_id GROUP BY Students_id ORDER BY TotalScore DESC', 
        [$ClsId, $TrmId, $YrId]); 

        $CountStu = $db->getRow('SELECT COUNT(*) AS ToStu FROM `students` WHERE `Classess_id` = ?', [$ClsId]);

        $gtTrm = $db->getRow('SELECT `Year`, `Term`, `Next_Term_Start`, `Next_Term_Ends` 
        FROM `years` AS Yrs INNER JOIN `terms` AS Trms
        WHERE Yrs.id = ? AND Trms.Years_id = Yrs.id AND Trms.id = ?', [$YrId, $TrmId]);
        // echo var_dump($gtStu);
        $pos = 0;
        $ttAllScrs = 0;
        $totalScore = 0;
        $getClass = $db->getRow('SELECT `Class` FROM `classess` WHERE `id` = ?', [$ClsId]);
        //  AND scr.Terms_id = 10 AND scr.Terms_Years_id = 11 AND
        //.round($val['TotalScore'] / $gtNoofSub).
        //.round($finalClsAverage).

        foreach ($gtStu as $valuone) { 
            $ttAllScrs = $ttAllScrs + $valuone['TotalScore'];
        }
        
        foreach ($gtStu as $val) {
            $cls = '';
            $str = substr($getClass['Class'], 0, 1);
            if ($str == 'N' || $str == 'P') {
                $cls = 'Pupils';
            } else {
                 $cls = 'Students';
            }
            if ($totalScore > $val['TotalScore'] || $totalScore == 0) {
                $pos++;
                $totalScore = $val['TotalScore'];
            } elseif ($totalScore == $val['TotalScore']) {
                
            }
            $gtNoofSub = getNumberofSubjects ($val['Students_id'], $ClsId, $TrmId, $YrId);
            $finalClsAverage = $ttAllScrs / $CountStu['ToStu'] /  $gtNoofSub;
            $subj = gtScors($val['Students_id'], $ClsId, $YrId, $TrmId, $CountStu['ToStu']);
                $out .= '
            <section class="sheet padding-10mm">
            <div class="mrg10" style="border: 3Q black solid; ">
                <div>
                <img style="height: 120px; width: 120px; margin-left: 20px; position: absolute;" src="../dist/img/badge128x128.png" alt="">
                    <h2 class="text-center" style="margin-left: 100px;">
                    <img style="height: 120px; width: 500px;" src="../dist/img/krmtext2.png">
                    </h2>
                    <h4 class="text-center pad0 mrg0">
                    <b style="font-family: Times, serif;">ACADEMIC REPORT SHEET FOR '.$gtTrm['Term'].' TERM '.$gtTrm['Year'].'</b></h4>
                    <h3 class="text-center pad0 mrg0">
                    <b style="text-decoration: underline;"> 
                    '.$val['First_Name'].' '.$val['Middle_Name'].' '.$val['Last_Name'].' </b></h3>
                </div>
                <div class="row brd1q mrg10" style="font-weight: bolder;">
                    <div class="col-xs-4 brdright lstnon" style="">
                        <li>Report No: '.$val['Reg_No'].'</li>
                        <li>Class: '.$getClass['Class'].'</li>
                        <li>Class Population: '.$CountStu['ToStu'].'</li>
                    </div>
                    <div class="col-xs-4 brdright lstnon" style="">
                        <li>Position: '.$pos.'</li>
                        <li>Total Score: '.$val['TotalScore'].'</li>
                        <li>'.$cls.' Average: '.round($val['TotalScore'] / $gtNoofSub).'</li>
                    </div>
                    <div class="col-xs-4 lstnon" style="">
                    <li>Subjects Offered: '. $gtNoofSub.'</li>
                    <li>Next Term Begins: '.$gtTrm['Next_Term_Start'].'</li>
                    <li>Next Term Ends: '.$gtTrm['Next_Term_Ends'].'</li>
                </div>
                </div>
                
                <div class="row brd1q mrg10">
                    <div class="col-xs-4 brdrg1q minhg100" style="padding-top: 35px;">
                    Cognitive Domain <br>    
                    Subject(s)
                    </div>
    
                    <div class="col-xs-2 text-center brdrg1q minhg100 pad0 mrg0" style="padding-top: 5px;">
                      <div class="col-xs-12 pad0 padbt1q"> '.$gtTrm['Term'] . " Term" .' </div>
                       <div class="col-xs-4 pad0">
                            <div class="rotte">1ST CA (20%)</div>
                        </div>
                        <div class="col-xs-4 pad0">
                            <div class="rotte">2ND CA (20%)</div>
                        </div>
                        <div class="col-xs-4 pad0">
                            <div class="rotte">Exam (60%)</div>
                        </div>
                    </div>
    
                    <div class="col-xs-1 brdrg1q minhg100 pad10 padtp40">
                        Total
                    </div>
                    <div class="col-xs-1 brdrg1q minhg100 pad10">
                        <div class=" mrgtp10" style="width: 90px; height: 40px; transform: translate(-20px, 10px) rotate(-90deg);">
                            Sub Average
                        </div>
                    </div>
                    <div class="col-xs-1 brdrg1q minhg100 pad10 padtp40">
                        Grade
                    </div>
                    
    
                    <div class="col-xs-3 padtp40 lstnon">
                        Subject Remarks
                    </div>
                </div>
                
                <div class="row brd1q mrg10">
                    '.$subj.'
                </div>
    
            <div class="row brd1q mrg10">
                <div class="col-xs-12 pad0 padbt1q"  style="  margin-top: 2px;">
                    <div class="col-xs-4 pad0 mrlft10" style=""> AFFECTIVE DOMAIN REPORT </div>
                    <div class="col-xs-3 pad0 mrlft10" style=""><b>CLASS AVERAGE: '.round($finalClsAverage).'</b></div>
                    
                    <div class="col-xs-3 pad0" style="width: 120px;  margin-left: 130px;"> KEYS TO RATING </div>
                </div>
                    
                <div class="col-xs-4 brdrg1q lstnon" style="">
                    <li>Relationship with Student <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Sense of Responsibility <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Relationship with Staff <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Spirit of Co-operation <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Attendance of Lesson <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                </div>
                <div class="col-xs-3 brdrg1q lstnon" style="width: 140px;">
                    <li>Attenttiveness <span class="fa fa-square-o fa-4 fa-lg"></span></li>  
                    <li>Self Control <span class="fa fa-square-o fa-4 fa-lg"></span></li>  
                    <li>Punctuality <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Politeness <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Honesty <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                </div>
                <div class="col-xs-3 brdrg1q lstnon" style="width: 140px;">
                    <li>Perseverance <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Leadership <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Neatness <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Initiative <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Respect <span class="fa fa-square-o fa-4 fa-lg fa-lg"></span></li>
                </div>
                <div class="col-xs-2 lstnon" style="padding-right: 0px; ">
                    <li>5 - Excellent</li>
                    <li>4 - Good</li>
                    <li>3 - Fair</li>
                    <li>2 - Poor</li>
                    <li>1 - Very Poor</li>
                </div>
            </div>
    
            <div class="row brd1q mrg10">
                    
                <div class="col-xs-12 pad0 mrlft10 mrgtp10" style=" ">
                    <div class="col-xs-3 pad0">Class Teachers Comment:</div>
                    <div class="col-xs-7 padbt1q mrgtp15"></div>
                </div>

                <div class="col-xs-12 pad0 mrlft10 mrgtp10" style=" ">
                    <div class="col-xs-3 pad0">Principal/Head Comment:</div>
                    <div class="col-xs-7 padbt1q mrgtp15"></div>
                </div>
    
            </div>
               
            </div></section>';
            
           
            }   
            return $out;
    }
    // prnt btn
    if (isset($_POST['printPreviewYrId'])) {

        $YrId = $_POST['printPreviewYrId'];
        $TrmId = $_POST['TrmId'];
        $SecId = $_POST['SecId'];
        $ClsId = $_POST['clsId'];  
            // ttsc x1 / nostu x1 / noof sub
        echo generatReport ($YrId, $TrmId, $SecId,  $ClsId);
    }
    // print rpt a4
    if (isset($_POST['printYrId'])) {
        $YrId = $_POST['printYrId'];
        $TrmId = $_POST['TrmId'];
        $SecId = $_POST['SecId'];
        $ClsId = $_POST['clsId']; 
        require_once 'mprintheader.php';
        $oupt = generatReport ($YrId, $TrmId, $SecId,  $ClsId);
        
        echo $html .= $oupt . '</body></html>';
    }
   
}

//100580595719 Psychomotor 

// <?php
// $numbers = array( 101, 201, 301, 301, 401, 401, 401, 501, 501, 501, 501);
// rsort($numbers);

// $arrlength = count($numbers);
// $rank = 1;
// $prev_rank = $rank;

// for($x = 0; $x < $arrlength; $x++) {

//     if ($x==0) {
//          echo $numbers[$x]."- Rank".($rank);
//     }

//    elseif ($numbers[$x] != $numbers[$x-1]) {
//         $rank++;
//         $prev_rank = $rank;
//         echo $numbers[$x]."- Rank".($rank);
//    }

//    else{
//         $rank++;
//         echo $numbers[$x]."- Rank".($prev_rank);
//     }

//    echo "<br>";
// }
// 

// SELECT  `First_Name`, `Middle_Name`, `Last_Name`, `Reg_No`, Subject, SUM(Total_Score) AS TotalScore, COUNT(First_Name) AS NoofStu FROM `students` AS Stu INNER JOIN scores AS Scr ON  Stu.Classess_id = 12 AND Scr.Students_Classess_id = Stu.Classess_id AND Scr.Students_id = Stu.id INNER JOIN classess as Cls ON Cls.id = Scr.Students_Classess_id INNER JOIN section AS Sec ON Sec.id = Cls.Section_id 
// INNER JOIN subjects AS Sub ON Sub.Section_id = Sec.id GROUP BY First_Name ORDER BY TotalScore ASC 

// SELECT `First_Name`, `Middle_Name`, `Last_Name`, `Reg_No`, `Classess_id`, `Class`, 
//         Stu.id, Section, SUM(`Total_Score`) AS TotalScore, COUNT( $gtNoofSub//         FROM `students` AS Stu JOIN `classess` AS Cls ON Stu.Classess_id = 24 
//         AND Cls.id = Stu.Classess_id JOIN section AS Sec ON Sec.id = Cls.Section_id JOIN scores 
//         AS Scr ON Scr.Students_id = Stu.id AND Scr.Students_Classess_id = Stu.Classess_id 
//         AND Scr.Terms_id = 10 AND Scr.Terms_Years_id = 11 GROUP BY First_Name ORDER BY TotalScore DESC