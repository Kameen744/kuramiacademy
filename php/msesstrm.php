<?php
    session_start();
    if (isset($_SESSION['Admin_Name'])) {
        require_once 'includes/database.php';
        $db = new Database();

        function gtYearsTr () {
            global $db;
            $yearsTr = '';
            $gtyears = $db->getRows('SELECT * FROM `years`');
            foreach ($gtyears as $val) {
                $yearsTr .= '<tr>
                <td>'.$val['Year'].'</td><td>
                <button class="btn btn-danger btn-xs" value="'.$val['id'].'" id="delYearBtn">Delete</button></td>
                </tr>';
            }
            return $yearsTr;
        }
        function gtTermsTr ($yId) {
            global $db;
            $termTr = '';
            $gtterm = $db->getRows('SELECT * FROM `terms` WHERE `Years_id` = ?', [$yId]);
            foreach ($gtterm as $val) {
                $termTr .= '<tr>
                <td>'.$val['Term'].'</td><td>
                <button class="btn btn-info btn-xs" value="'.$val['id'].'" id="editTermBtn">Edit</button>
                <button class="btn btn-danger btn-xs" value="'.$val['id'].'" id="delTermBtn">Delete</button>
                </td>
                </tr>';
            }
            return $termTr;
        }
        if (isset($_POST['admnManageSessTrms'])) {
            $gtyears = $db->getRows('SELECT * FROM `years`');
            $gtterms = $db->getRows('SELECT * FROM `terms`');
            $yearsOptn = '';
            $termsOptn = '';
            foreach ($gtyears as $val) {
                $yearsOptn .= '<option value="'.$val['id'].'">'.$val['Year'].'</option>';
            }
            foreach ($gtterms as $val) {
                $termsOptn .= '<option value="'.$val['id'].'">'.$val['Term'].'</option>';
            }
        $out = '
        <div class="col-md-3 maxvh70 pdlft0">
            <div class="box box-primary box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">New Session / Year</h3> 
              </div>
              <div class="box-body bg-info">
                  <input class="form-control input-sm marb5" type="text" id="addYearTx" placeholder="Year e.g 2018">
                  
                  <button class="btn btn-primary btn-sm col-sm-10 col-sm-offset-1" id="addYearBtn">
                  Add New Year / Session
                  </button>
              </div>
            </div>

            <div class="box box-primary box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Sessions</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body bg-info">
                <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Year</th><th>Action</th>
                    </tr>
                </thead>
                <tbody id="yearsTrContainer">
                    '.gtYearsTr().'
                </tbody>
                </table>
            </div>
          </div>
        </div>

        <div class="col-md-3 maxvh70 pdlft0">
        <div class="box box-primary box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">New Term</h3>
            <div class="box-tools pull-right">
            <select class="form-control input-sm marb5" type="text" id="selcYearTx">
                <option value="">Year</option>
                '.$yearsOptn.'
            </select> 
            </div>
          </div>
          <div class="box-body bg-info" id="termsBodyCont">
            <select class="form-control input-sm marb5" id="addTermTxt">
                <option value="">Select Term</option>
                <option value="1ST">First Term</option>
                <option value="2ND">Second Term</option>
                <option value="3RD">Third Term</option>
            </select>  
            <label class="label label-primary">Next Term Start:</label>
            <input type="date" class="form-control input-sm marb5" id="addNextTermStartTxt">

            <label class="label label-primary">Next Term Ends:</label>
            <input type="date" class="form-control input-sm marb5" id="addNextTermEndsTxt">
            <button class="btn btn-primary btn-sm col-sm-10 col-sm-offset-1" id="addTermBtn">
            Add New Term
            </button>
          </div>
        </div>

        <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Terms</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body bg-info">
            <table class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th>Term</th><th>Action</th>
                </tr>
            </thead>
            <tbody id="termsTrContainer">
            </tbody>
            </table>
        </div>
      </div>
    </div>';
        echo $out;
    } 
        // add new year
        if (isset($_POST['addYearTx'])) {
            $YearTx = $db->validation($_POST['addYearTx']);
            $insYrs = $db->insertRow('INSERT INTO `years`(`Year`) VALUES (?)', [$YearTx]);
            if ($insYrs) {
                echo gtYearsTr();
            }
        }
        // del Year
        if (isset($_POST['delYear'])) {
            $db->deleteRow('DELETE FROM `years` WHERE `id` = ?', [$_POST['delYear']]);
            echo gtYearsTr();
        }
        // select term year
        if (isset($_POST['selectTrmYear'])) {
            echo gtTermsTr($_POST['selectTrmYear']);
        }
        // add term
        if (isset($_POST['addTermTxt'])) {
            $yearTxId = $_POST['yearTxId'];
            $NextTermStart = $db->validation($_POST['addNextTermStartTxt']);
            $NextTermEnds = $db->validation($_POST['addNextTermEndsTxt']);
            $TermTxt = $db->validation($_POST['addTermTxt']);
            $insTerm = $db->insertRow('INSERT INTO `terms`(`Term`, `Next_Term_Start`,
            `Next_Term_Ends`, `Years_id`) 
            VALUES (?,?,?,?)', [$TermTxt, $NextTermStart, $NextTermEnds, $yearTxId]);

            if ($insTerm) {
                echo gtTermsTr($yearTxId);
            }
        }
        //edit term
        if (isset($_POST['editTermId'])) {
            $trmid = $_POST['editTermId'];
            $trmyr = $_POST['yearTxId'];
            $gtTrm = $db->getRow('SELECT `Term`, `Next_Term_Start`, `Next_Term_Ends` 
            FROM `terms` WHERE `id` = ?  AND `Years_id` = ?', [$trmid, $trmyr]);
            $out = '
            <label class="label label-primary">Term:</label>
            <input type="text" value="'.$gtTrm['Term'].'" class="form-control input-sm marb5" id="updateTermTxt">

            <label class="label label-primary">Next Term Start:</label>
            <input type="date" value="'.$gtTrm['Next_Term_Start'].'" class="form-control input-sm marb5" id="updateNextTermStartTxt">

            <label class="label label-primary">Next Term Ends:</label>
            <input type="date" value="'.$gtTrm['Next_Term_Ends'].'" class="form-control input-sm marb5" id="updateNextTermEndsTxt">
            <button class="btn btn-primary btn-sm col-sm-10 col-sm-offset-1" value="'.$trmid.'" id="saveChangeTermBtn">
            Save Changes
            </button>            
            ';
            echo $out;
        }
        // update term
        if (isset($_POST['updateTermTxt'])) {
            $termid = $_POST['termid'];
            $yearTxId = $_POST['updateyearTxId'];
            $NextTermStart = $db->validation($_POST['updateNextTermStartTxt']);
            $NextTermEnds = $db->validation($_POST['updateNextTermEndsTxt']);
            $TermTxt = $db->validation($_POST['updateTermTxt']);
            $insTerm = $db->insertRow('UPDATE `terms` SET `Term` = ?, `Next_Term_Start` = ?,
            `Next_Term_Ends` = ? WHERE `id` = ? AND `Years_id` = ?
            ', [$TermTxt, $NextTermStart, $NextTermEnds, $termid, $yearTxId]);

            if ($insTerm) {
                echo 'Successefully saved';
            }
        }
        // del term 
        if (isset($_POST['delTermId'])) {
            $db->deleteRow('DELETE FROM `terms` WHERE `id` = ?', [$_POST['delTermId']]);
            echo gtTermsTr($_POST['yearTxId']);
        }

    } else {

    }
    
    
    
    
    
    