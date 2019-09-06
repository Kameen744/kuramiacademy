<?php
    session_start();
    if (isset($_SESSION['Admin_Name'])) {
        require_once 'includes/database.php';
        $db = new Database();

        function gtScors ($stuId, $ClssId, $YrId, $TrmId, $ToStu) {
            global $db;
            $subj  = '';
    
            $gtScrsss = $db->getRows('SELECT `Subject`, `First_CA`, `Second_CA`, `Exam`, `Total_Score`, `Class`, 
            `Section`, `Term`, `Year` FROM `scores` AS Scr INNER JOIN `classess` AS Cls ON Scr.Students_id = ? 
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
    
                $ClsAver = round($gtTtotals['TTScore'] / $ToStu);
                $SubAver = ceil($gtTtotals['TTScore'] / $gtTtotals['NoSbu']);
    
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
                    <div class="row" style="border: 1Q black solid; margin: 2px; padding: 0px;">
                    <div class="col-xs-3" style="border-right: 1Q black solid; padding: 1px; padding-left: 15px;">
                        '.$val['Subject'].'
                    </div>
    
                    <div class="col-xs-2" style="border-right: 1Q black solid; padding: 1px;">
                        <div class="col-xs-4" style="margin-left: 0px; padding-left: 4px;">
                        '.$val['First_CA'].'
                        </div>
                        <div class="col-xs-4" style="margin-left: 0px; padding-left: 8px;">
                        '.$val['Second_CA'].'
                        </div>
                        <div class="col-xs-4" style="margin-left: 0px; padding-left: 13px;">
                        '.$val['Exam'].'
                        </div>
                    </div>
    
                    <div class="col-xs-1" style="border-right: 1Q black solid; padding: 1px; padding-left: 15px;">
                    '.$val['Total_Score'].'
                    </div>
                    <div class="col-xs-1" style="border-right: 1Q black solid; padding: 1px; padding-left: 15px;">
                        '.$SubAver.'
                    </div>
                    <div class="col-xs-1" style="border-right: 1Q black solid; padding: 1px; padding-left: 15px;">
                        '.$grd.'
                    </div>
                    <div class="col-xs-1" style="border-right: 1Q black solid; padding: 1px; padding-left: 15px;">
                        '.$ClsAver.'
                    </div>
    
                    <div class="col-xs-3" style="padding: 1px; padding-left: 15px;">
                        '.$cmnt.'
                    </div>
                </div>';
            }
          return $subj;
        }   

      // function generate report
      function generatReport ($YrId, $TrmId, $SecId,  $ClsId) {
        global $db;
        $out = '';

        $gtStu = $db->getRows('SELECT `First_Name`, `Middle_Name`, `Last_Name`, `Reg_No`, `Classess_id`, `Class`, 
        Stu.id, Section, SUM(`Total_Score`) AS TotalScore, COUNT(*) AS NoofSubjects 
        FROM `students` AS Stu JOIN `classess` AS Cls ON Stu.Classess_id = ? 
        AND Cls.id = Stu.Classess_id JOIN section AS Sec ON Sec.id = Cls.Section_id JOIN scores 
        AS Scr ON Scr.Students_id = Stu.id AND Scr.Students_Classess_id = Stu.Classess_id 
        AND Scr.Terms_id = ? AND Scr.Terms_Years_id = ? GROUP BY First_Name ORDER BY TotalScore DESC', 
        [$ClsId, $TrmId, $YrId]); 

        $CountStu = $db->getRow('SELECT COUNT(*) AS ToStu FROM `students` WHERE `Classess_id` = ?', [$ClsId]);

        $gtTrm = $db->getRow('SELECT `Year`, `Term` FROM `years` AS Yrs INNER JOIN `terms` AS Trms
        WHERE Yrs.id = ? AND Trms.Years_id = Yrs.id AND Trms.id = ?', [$YrId, $TrmId]);
        // echo var_dump($gtStu);
        $pos = 1;
        $ttAllScrs = 0;

        foreach ($gtStu as $valuone) {
            $ttAllScrs = $ttAllScrs + $valuone['TotalScore'];
        }

        foreach ($gtStu as $val) {
            $subj = gtScors($val['id'], $val['Classess_id'], $YrId, $TrmId, $CountStu['ToStu']);
                $out .= '
            <section class="sheet padding-10mm">
            <div style="border: 3Q black solid; margin: 10px;">
                <div>
                <img style="height: 120px; width: 120px; margin-left: 20px; position: absolute;" src="../dist/img/badge128x128.png" alt="">
                    <h2 class="text-center" style="margin-left: 100px;">
                    <img style="height: 120px; width: 500px;" src="../dist/img/krmtext2.png" alt="">
                    </h2>
                    <h3 class="text-center" style="margin: 0px; padding:0px;">Academic Report Sheet For '.$gtTrm['Term'].' Term '.$gtTrm['Year'].'</h3>
                    <h3 class="text-center" style="margin: 0px; padding:0px;"><b style="text-decoration: underline;"> 
                    '.$val['First_Name'].' '.$val['Middle_Name'].' '.$val['Last_Name'].' </b></h3>
                </div>
                <div class="row" style="border: 1Q black solid; margin: 10px; padding: 0px;">
                    <div class="col-xs-6" style="border-right: 1Q black solid; min-height:inherit; list-style: none;">
                        <li>Admission No: '.$val['Reg_No'].'</li>
                        <li>Class: '.$val['Class'].'</li>
                        <li>Class Population: '.$CountStu['ToStu'].'</li>
                    </div>
                    <div class="col-xs-6" style="list-style: none;">
                        <li>Position: '.$pos.'</li>
                        <li>Next Term Begins:</li>
                        <li>Next Term Ends:</li>
                    </div>
                   
                </div>
                
                <div class="row" style="border: 1Q black solid; margin: 10px; padding: 0px; margin-bottom: 0px;">
                    <div class="col-xs-3" style="border-right: 1Q black solid; min-height: 100px; padding-left: 20px; padding-top: 40px;">
                        Subject(s)
                    </div>
    
                    <div class="col-xs-2 text-center" style="border-right: 1Q black solid; min-height: 100px; margin: 0px; padding:0px; padding-top: 5px;">
                      <div class="col-xs-12" style="padding:0px; border-bottom: 1Q black solid;"> '.$gtTrm['Term'] . " Term" .' </div>
                       <div class="col-xs-4" style="padding:0px; margin:0px;">
                            <div style="transform: translate(-24px, 30px) rotate(-90deg); font-size: 11px; width:80px;">CAT 1 (20%)</div>
                        </div>
                        <div class="col-xs-4" style="padding:0px; margin:0px;">
                            <div style="transform: translate(-20px, 30px) rotate(-90deg); font-size: 11px; width:80px;">CAT 2 (20%)</div>
                        </div>
                        <div class="col-xs-4" style="padding:0px; margin:0px;">
                            <div style="transform: translate(-17px, 30px) rotate(-90deg); font-size: 11px; width:80px;">Exam (60%)</div>
                        </div>
                    </div>
    
                    <div class="col-xs-1" style="border-right: 1Q black solid; min-height: 100px; padding: 10px; padding-top: 40px;">
                        Total
                    </div>
                    <div class="col-xs-1" style="border-right: 1Q black solid; min-height: 100px; padding: 10px;">
                        <div style="width: 90px; height: 40px; transform: translate(-20px, 10px) rotate(-90deg); margin-top: 10px;">
                            Sub Average
                        </div>
                    </div>
                    <div class="col-xs-1" style="border-right: 1Q black solid; min-height: 100px; padding: 10px; padding-top: 40px;">
                        Grade
                    </div>
                    <div class="col-xs-1" style="border-right: 1Q black solid; min-height: 100px; padding: 10px;">
                        <div style="width: 100px; height: 40px; transform: translate(-20px, 10px) rotate(-90deg); margin-top: 6px;">
                            Class Average
                        </div>
                    </div>
    
                    <div class="col-xs-3" style="min-height:inherit; padding-left: 20px; padding-top: 40px; list-style: none;">
                        Subject Remarks
                    </div>
                </div>
                
                <div class="row" style="border: 1Q black solid; margin: 10px; padding: 0px; margin-top: 0px;">
                    '.$subj.'
                </div>
    
            <div class="row" style="border: 1Q black solid; margin: 10px; padding: 0px;">
                <div class="col-xs-12" style="border-bottom: 1Q black solid; padding:0px; margin-top: 2px;">
                    <div class="col-xs-4" style="padding:0px; margin:0px; margin-left: 10px;"> EFFECTIVE DOMAIN REPORT </div>
                    <div class="col-xs-3" style="padding:0px; width: 120px; margin:0px; margin-left: 290px;"> KEYS TO RATING </div>
                </div>
                    
                <div class="col-xs-4" style="border-right: 1Q black solid; min-height:inherit; list-style: none;">
                    <li>Relationship with Student <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Sense of Responsibility   <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Relationship with Staff  <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Spirit of Co-operation    <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Attendance of Lesson    <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                </div>
                <div class="col-xs-3" style="border-right: 1Q black solid; width: 140px; min-height:inherit; list-style: none;">
                    <li>Attenttiveness  <span class="fa fa-square-o fa-4 fa-lg"></span></li>  
                    <li>Self Control <span class="fa fa-square-o fa-4 fa-lg"></span></li>  
                    <li>Punctuality   <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Politeness    <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Honesty       <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                </div>
                <div class="col-xs-3" style="border-right: 1Q black solid; width: 140px; min-height:inherit; list-style: none;">
                    <li>Perseverance  <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Leadership  <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Neatness  <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Initiative <span class="fa fa-square-o fa-4 fa-lg"></span></li>
                    <li>Respect  <span class="fa fa-square-o fa-4 fa-lg fa-lg"></span></li>
                </div>
                <div class="col-xs-2" style="min-height:inherit; padding-right: 0px; list-style: none;">
                    <li>5 - Excellent</li>
                    <li>4 - Good</li>
                    <li>3 - Fair</li>
                    <li>2 - Poor</li>
                    <li>1 - Very Poor</li>
                </div>
            </div>
    
            <div class="row" style="border: 1Q black solid; margin: 10px; padding: 0px;">
                <div class="col-xs-12" style="border-bottom: 1Q black solid; padding:0px; margin-top: 2px;">
                    <div class="col-xs-4" style="padding:0px; margin:0px; margin-left: 10px;">Subjects Offered: </div>
                    <div class="col-xs-4" style="padding:0px; margin:0px; margin-left: 10px;">Student Average: </div>
                </div>
                    
                <div class="col-xs-12" style="padding:0px; margin-left: 10px; margin-top: 10px;">
                    <div class="col-xs-3" style="padding:0px; margin:0px;">Class Teachers Comment:</div>
                    <div style="border-bottom: 1Q black solid; margin-left: 0px; padding: 0px; margin-top: 15px;" class="col-xs-7"></div>
                </div>

                <div class="col-xs-12" style="padding:0px; margin-left: 10px; margin-top: 10px;">
                    <div class="col-xs-3" style="padding:0px; margin:0px;">Principal/Head Comment:</div>
                    <div style="border-bottom: 1Q black solid; margin-left: 0px; padding: 0px; margin-top: 15px;" class="col-xs-7"></div>
                </div>
    
            </div>
    
            </div></section>';
            $pos++;
            }   
            return $out;
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