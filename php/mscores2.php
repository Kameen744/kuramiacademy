<?php
    session_start();
    if (isset($_SESSION['Admin_Name'])) {
        require_once 'includes/database.php';
        $db = new Database();
        function gtStudScores ($studId, $ClsId, $TrmId, $YrId, $subjId) {
            global $db;
            $gtVrSc = $db->getRow('SELECT `id` FROM `scores` WHERE `Students_id` = ? 
            AND `Students_Classess_id` = ? AND `Terms_id` = ? AND `Terms_Years_id` = ? 
            AND `Subjects_id` = ?', [$studId, $ClsId, $TrmId, $YrId, $subjId]);
            return $gtVrSc;
        }
        function gtTerms ($yrsId) {
            global $db;
            $gtterms = $db->getRows('SELECT * FROM `terms` WHERE `Years_id` = ?', [$yrsId]);
            $termsTr = '<option value="">Select Term</option>';
            foreach ($gtterms as $val) {
                $termsTr .= '<option value="'.$val['id'].'">'.$val['Term'].' Term</option>';
            }
            return $termsTr;
        }
        function gtClasses ($secId) {
            global $db;
            $gtclass = $db->getRows('SELECT * FROM `classess` WHERE `Section_id` = ?', [$secId]);
            $classTr = '<option value="">Select Class</option>';
            foreach ($gtclass as $val) {
                $classTr .= '<option value="'.$val['id'].'">'.$val['Class'].'</option>';
            }
            return $classTr;
        }
        function gtStuSubjec ($secId) {
            global $db;
            $subjcTr = '<option value="">Select Subject</option>';
            $gtSubj = $db->getRows('SELECT * FROM `subjects` WHERE `Section_id` = ?', [$secId]);
            foreach ($gtSubj as $val) { 
                $subjcTr .= '<option value="'.$val['id'].'">'.$val['Subject'].'</option>';
            }
            return $subjcTr;
        }
        function gtClassStu ($clsId, $subjId, $TrmId, $YrId) {
            global $db;
            $out = '';
            $gtStu = $db->getRows('SELECT * FROM `students` WHERE `Classess_id` = ? ORDER BY `First_Name`', [$clsId]);
            foreach ($gtStu as $val) {
                if (empty(gtStudScores($val['id'], $clsId, $TrmId, $YrId, $subjId))) {
                    $out .= '
                        <button class="btn btn-danger btn-xs btn-block addCrStuScore bgfont" value="'.$val['id'].'">
                            '.$val['First_Name'].' '.$val['Last_Name'].'
                        </button>
                    ';
                } else {
                    $out .= '
                        <button class="btn btn-primary btn-xs btn-block addCrStuScore bgfont" value="'.$val['id'].'">
                            '.$val['First_Name'].' '.$val['Last_Name'].'
                        </button>
                    ';
                }
            }
            return $out;
        }
        function gtStuScores ($clsId) {
            global $db;
            $gtStu = $db->getRows('SELECT * FROM `students` WHERE `Classess_id` = ?', [$clsId]);
            foreach ($gtStu as $val) {
               // $gtStuScr = $db->getRow('clsId');
            }
        }

        if (isset($_POST['admnManageScores'])) {
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
                    <select class="form-control input-sm marb5" id="addScoreYearId"> 
                    <option value="">Select Year</option>
                    '.$yearsOptn.'
                    </select>
                    <select class="form-control input-sm marb5" id="addScoreTermId"> 
                    <option value="">Select Term</option>
                    </select>
                    <select class="form-control input-sm marb5" id="addScoreSectionId"> 
                    <option value="">Select Section</option>
                    '.$secTr.'
                    </select>
                    <select class="form-control input-sm marb5" id="addScoreSubjectId"> 
                    <option value="">Select Subject</option>
                    </select>
                    <select class="form-control input-sm marb5" id="addScoreClassId"> 
                    <option value="">Select Class</option>
                    </select>
              </div>
            </div>

            <div class="box box-primary box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">More Info</h3>
              </div>
                <div class="box-body bg-info">
                    <div class="marb5 col-md-12 btn btn-primary btn-sm" id="addScoreYearInfoTxt">
                    </div>
                    
                    <div class="marb5 col-md-12 btn btn-primary btn-sm" id="addScoreTermInfoTxt"> 
                    </div>

                    <div class="marb5 col-md-12 btn btn-primary btn-sm" id="addScoreSectionInfoTxt"> 
                    </div>

                    <div class="marb5 col-md-12 btn btn-primary btn-sm" id="addScoreClassInfoTxt"> 
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 maxvh70 pdlft0">
        <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <h3 class="box-title" id="classInfoTxt"></h3>
        </div>
        <div class="box-body bg-info" id="classStudTr">

        </div>
        </div>
        </div>

        <div class="col-md-7 maxvh70">
        <div class="box box-primary box-solid">
          <div class="box-header with-border">
            <h3 class="box-title col-md-4" id="addScoresInfoTxt">Add Scores</h3>
          </div>
          <div class="box-body bg-info" id="addStuScoresCont">  
           
          </div>
        </div>

        <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Scores</h3>
        </div>
        <div class="box-body bg-info">
            <table class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th>First_Name</th><th>Last Name</th><th>1ST CA</th><th>2ND CA</th><th>Exam</th><th>Total</th>
                </tr>
            </thead>
            <tbody id="studScoresTrContainer">   
            </tbody>
            </table>
            </div>
        </div>
        </div>
        ';
        echo $out;
        } 
        // gt yrs terms
        if (isset($_POST['gtYrsTrms'])) {
            echo gtTerms($_POST['gtYrsTrms']);
        }
        // gt subjc
        if (isset($_POST['gtSecSubjec'])) {
            echo gtStuSubjec($_POST['gtSecSubjec']);
        }
        // gt sec class
        if (isset($_POST['gtSecClas'])) {
            echo gtClasses($_POST['gtSecClas']);
        }
        // get class stu
        if (isset($_POST['gtClasStud'])) {
            echo gtClassStu($_POST['gtClasStud'], $_POST['subjId'], $_POST['TrmId'], $_POST['ScClsYrId']);
        }
        function gtScrsFrm ($YrId, $TrmId, $SecId, $ClsId, $subjId, $studId) {
            global $db;
            $gtScr = $db->getRow('SELECT * FROM `scores` 
            WHERE  `Students_id` = ? AND  `Students_Classess_id` = ? AND `Terms_id` = ? 
            AND `Terms_Years_id` = ? AND `Subjects_id` = ?', 
            [$studId, $ClsId, $TrmId, $YrId, $subjId]);
            if (empty($gtScr)) {
                $out = '
                <div class="form-inline" id="addScoreFrm">
                <div class="form-group">
                    <input type="text" class="form-control input-sm" id="scFirstCaTxt" placeholder="1ST CA 20%">
                    <input type="text" class="form-control input-sm" id="scSeconCaTxt" placeholder="2ND CA 20%">
                    <input type="text" class="form-control input-sm" id="scExamTxt" placeholder="Exam 60%">
                </div>
            
                <button class="btn btn-primary btn-sm" value="'.$studId.'" id="addScoreBtn">Save</button>
                </div>
                ';
            } else {
                $out = '
                <div class="form-inline" id="addScoreFrm">
                <div class="form-group">
                    <input type="text" class="form-control input-sm" id="scFirstCaTxt" value="'.$gtScr['First_CA'].'" placeholder="1ST CA">
                    <input type="text" class="form-control input-sm" id="scSeconCaTxt" value="'.$gtScr['Second_CA'].'" placeholder="2ND CA">
                    <input type="text" class="form-control input-sm" id="scExamTxt" value="'.$gtScr['Exam'].'" placeholder="Exam">
                </div>
            
                <button class="btn btn-primary btn-sm" value="'.$studId.'" id="addScoreBtn">Save</button>
                </div>
                ';
            }
            return $out;
        }
        // add stu score
        if (isset($_POST['stuScorYrId'])) {
            $YrId = $_POST['stuScorYrId']; 
            $TrmId = $_POST['TrmId']; 
            $SecId = $_POST['SecId']; 
            $ClsId = $_POST['ClsId']; 
            $subjId = $_POST['subjId'];
            $studId = $_POST['studId'];
          echo gtScrsFrm ($YrId, $TrmId, $SecId, $ClsId, $subjId, $studId);
        }

        function gtScoresTr ($YrId, $TrmId, $SecId, $ClsId, $subjId, $studId) {
            global $db;
            $gtscor = $db->getRow('SELECT `First_CA`, `Second_CA`, `Exam`, `Total_Score` 
            FROM `scores` WHERE `Students_id` = ? AND `Students_Classess_id`= ? 
            AND `Terms_id` = ? AND `Terms_Years_id` = ? AND `Subjects_id` = ?', 
            [$studId, $ClsId, $TrmId, $YrId, $subjId]);
            $gtStu = $db->getRow('SELECT `First_Name`, `Last_Name` FROM `students` 
            WHERE `id` = ? AND `Classess_id` = ?', [$studId, $ClsId]);
            $out = '
            <tr>
                <td>'.$gtStu['First_Name'].'</td><td>'.$gtStu['Last_Name'].'</td>
                <td>'.$gtscor['First_CA'].'</td><td>'.$gtscor['Second_CA'].'</td>
                <td>'.$gtscor['Exam'].'</td><td>'.$gtscor['Total_Score'].'</td>
            </tr>
            ';
            return $out;
        }
        // add stu score 
        if (isset($_POST['addStudScoreId'])) {
            $YrId = $_POST['adscrYrId']; 
            $TrmId = $_POST['TrmId']; 
            $SecId = $_POST['SecId']; 
            $ClsId = $_POST['ClsId']; 
            $subjId = $_POST['subjId'];
            $studId = $_POST['addStudScoreId'];

            $scFirstCaTxt = $db->validation($_POST['scFirstCaTxt']);
            $scSeconCaTxt = $db->validation($_POST['scSeconCaTxt']);
            $scExamTxt = $db->validation($_POST['scExamTxt']);
            $totlScr = ($scFirstCaTxt + $scSeconCaTxt) + $scExamTxt;
        
            if (empty(gtStudScores($studId, $ClsId, $TrmId, $YrId, $subjId))) {
                $insScr = $db->insertRow('INSERT INTO `scores`(`First_CA`, `Second_CA`, `Exam`, 
            `Total_Score`, `Students_id`, `Students_Classess_id`, `Terms_id`, `Terms_Years_id`, 
            `Subjects_id`) VALUES (?,?,?,?,?,?,?,?,?)',
            [$scFirstCaTxt, $scSeconCaTxt, $scExamTxt, $totlScr, $studId, $ClsId, $TrmId, $YrId, $subjId]);
            } else {
                $insScr = $db->insertRow('UPDATE `scores` SET `First_CA` = ?, `Second_CA` = ?, `Exam` = ?, 
            `Total_Score` = ? WHERE `Students_id` = ? AND `Students_Classess_id` = ? AND `Terms_id` = ? 
            AND `Terms_Years_id` = ? AND `Subjects_id` = ?', 
            [$scFirstCaTxt, $scSeconCaTxt, $scExamTxt, $totlScr, $studId, $ClsId, $TrmId, $YrId, $subjId]);
            }

            if ($insScr) {
                echo gtScoresTr ($YrId, $TrmId, $SecId, $ClsId, $subjId, $studId);
            }
   
        }
    } else {

    }