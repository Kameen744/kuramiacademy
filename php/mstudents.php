<?php
    session_start();
    if (isset($_SESSION['Admin_Name'])) {
        require_once 'includes/database.php';
        $db = new Database();

        function gtStuEmptyForm () {
            return '
            <form class="form-inline" id="regStudentsTble">
            <div class="col-md-12 pad0 marb5">
                <input type="text" class="form-control input-sm" name="FirstName" placeholder="FirstName">
        
                <input type="text" class="form-control input-sm" name="MiddleName" placeholder="MiddleName">
        
                <input type="text" class="form-control input-sm" name="LastName" placeholder="LastName">
            </div>

            <div class="col-md-12 pad0 marb5">
                <input type="text" class="form-control input-sm" name="ParentNo" placeholder="Parent/Gurdian No">
        
                <input type="text" class="form-control input-sm" name="Address" placeholder="Address">
        
                <input type="text" class="form-control input-sm" name="Nationality" placeholder="Nationality">
            </div>
            
            <div class="col-md-12 pad0 marb5">
                <input type="text" class="form-control input-sm" name="StateofOr" placeholder="State of Origin">
        
                <input type="text" class="form-control input-sm" name="LocalGvt" placeholder="Local Govt">
            </div>

            <div class="col-md-12"> 
                <a type="submit" class="btn btn-primary btn-block btn-sm" id="registerStudentBtn">
                    Register Student
                </a>
            </div>
        </form>
            ';
        }

        function gtStuEditForm ($stuId) {
            global $db;
            $gtStu = $db->getRow('SELECT * FROM `students` WHERE `id` = ?', [$stuId]);
            return '
            <form class="form-inline" id="regStudentsTble">
            <div class="col-md-12 pad0 marb5">
                <input type="text" class="form-control input-sm" name="Edit_FirstName" placeholder="FirstName" value="'.$gtStu['First_Name'].'">
        
                <input type="text" class="form-control input-sm" name="Edit_MiddleName" placeholder="MiddleName" value="'.$gtStu['Middle_Name'].'">
        
                <input type="text" class="form-control input-sm" name="Edit_LastName" placeholder="LastName" value="'.$gtStu['Last_Name'].'">
            </div>

            <div class="col-md-12 pad0 marb5">
                <input type="text" class="form-control input-sm" name="Edit_ParentNo" placeholder="Parent/Gurdian No" value="'.$gtStu['Parent_No'].'">
        
                <input type="text" class="form-control input-sm" name="Edit_Address" placeholder="Address" value="'.$gtStu['Stu_Address'].'">
        
                <input type="text" class="form-control input-sm" name="Edit_Nationality" placeholder="Nationality" value="'.$gtStu['Nationality'].'">
            </div>
            
            <div class="col-md-12 pad0 marb5">
                <input type="text" class="form-control input-sm" name="Edit_StateofOr" placeholder="State of Origin" value="'.$gtStu['State_of_Origin'].'">
        
                <input type="text" class="form-control input-sm" name="Edit_LocalGvt" placeholder="Local Govt" value="'.$gtStu['Lg_Area'].'">
            </div>

            <div class="col-md-12"> 
                <button type="submit" class="btn btn-primary btn-block btn-sm" id="saveEditChangesBtn" value="'.$stuId.'">
                    Save Changes
                </button>
            </div>
        </form>
            ';
        }

        function classTblRow ($secId) {
            global $db;
            $gtclass = $db->getRows('SELECT * FROM `classess` WHERE `Section_id` = ?', [$secId]);
            $classPrtr = '';

            foreach ($gtclass as $val) {
                 $classPrtr .= '<tr>
                 <td>
                 <div class="input-group margin" style="padding: 0px; margin: 0px;">
                 <div class="input-group-btn">
                   <button type="button" class="btn btn-primary btn-xs">edit
                 </div>
                 <input type="text" class="mxwd90" value="'.$val['Class'].'" id="edtCls'.$val['id'].'">
                 <div class="input-group-btn">
                 <button class="btn btn-info btn-xs saveEditClassBtn" value="'.$val['id'].'">
                 Save</button>
                 </div>
               </div>
                 </td>
                 <td><button class="btn btn-danger btn-xs deleteClassBtn" value="'.$val['id'].'">
                 Delete</button>
                 </td></tr>
               ';
             }
            return $classPrtr;
        }
    
    function subjectsTblRow ($secId) {
        global $db;
        $gtsubjects = $db->getRows('SELECT * FROM `subjects` WHERE `Section_id` = ?', [$secId]);
        $subjectsPrtr = '';

        foreach ($gtsubjects as $val) {
             $subjectsPrtr .= '<tr>
             <td>'.$val['Subject'].'</td>
             <td>
             <button class="btn btn-danger btn-block btn-xs deleteSubjectBtn" value="'.$val['id'].'">Delete</button>
             </td></tr>
           ';
         }
        return $subjectsPrtr;
    }
    function studentsTblRow ($ClsId) {
        global $db;
        $gtstudents = $db->getRows('SELECT * FROM `students` WHERE `Classess_id` = ?', [$ClsId]);
        $studentsPrtr = '';

        foreach ($gtstudents as $val) {
             $studentsPrtr .= '<tr>
             <td>'.$val['First_Name'].'</td><td>'.$val['Last_Name'].'</td>
             <td>
             <button class="btn btn-info btn-xs editStudentBtn" value="'.$val['id'].'">
             Edit</button>
             <button class="btn btn-danger btn-xs deleteStudentBtn" value="'.$val['id'].'">
             Delete</button>
             </td></tr>';
        }
        return $studentsPrtr;
    }

        if (isset($_POST['admnManageStu'])) {
            $gtsection = $db->getRows('SELECT * FROM `section`');
            $gtclass = $db->getRows('SELECT * FROM `classess`');
            $classTr = '';
            $sectionTr = '';
            foreach ($gtclass as $val) {
                $classTr .= '<option value="'.$val['id'].'">'.$val['Class'].'</option>';
            }
            foreach ($gtsection as $val) {
                $sectionTr .= '<option value="'.$val['id'].'">'.$val['Section'].'</option>';
            }
            $out = '
        <div class="col-md-3 maxvh70 pdlft0">
            <div class="box box-primary box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">Add Classes</h3>
                <div class="box-tools pull-right">
                    <select class="form-control input-sm" id="addClassSectionTx"> 
                    <option value="">Section</option>
                    '. $sectionTr.'
                    </select>
                </div>
              </div>
              <div class="box-body bg-info">
                  <input class="form-control input-sm marb5" type="text" id="addClassTx" placeholder="Class">
                  
                  <button class="btn btn-primary btn-sm col-sm-10 col-sm-offset-1" id="addClassBtn">
                  Add Class
                  </button>
              </div>
            </div>

            <div class="box box-primary box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Classes</h3>
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
                        <th>Class</th><th>Action</th>
                    </tr>
                </thead>
                <tbody id="classesTrContainer">
                </tbody>
                </table>
            </div>
          </div>
        </div>

        <div class="col-md-3 maxvh70 pdlft0">
            <div class="box box-success box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">Add Subjects</h3>
                <div class="box-tools pull-right">
                    <select class="form-control input-sm marb5" id="subjectSectionTx"> 
                        <option value="">Section</option>
                        '.$sectionTr.'
                    </select>
                </div>
              </div>
              <div class="box-body bg-info">
                  <input class="form-control input-sm marb5" type="text" id="addSubjectTx" placeholder="Subject">
                  
                  <button class="btn btn-success btn-sm col-sm-10 col-sm-offset-1" id="addSubjectBtn">
                  Add Subject
                  </button>
              </div>
            </div>

            <div class="box box-success box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Subjects</h3>
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
                        <th>Subject</th><th>Action</th>
                    </tr>
                </thead>
                <tbody id="subjectsTrContainer">
                
                </tbody>
                </table>
            </div>
          </div>
        </div>

        <div class="col-md-6 maxvh70">
        <div class="box box-primary box-solid">
          <div class="box-header with-border">
            <h3 class="box-title col-md-4">Add Students</h3>
            <div class="box-tools pull-right">
            <select class="form-control input-sm" id="selectClassId">
              <option value="">Select Class</option>
              '.$classTr.'
            </select>
          </div>

          </div>
          <div class="box-body bg-info" id="regStuFormCont">  
            '.gtStuEmptyForm().'
          </div>
        </div>

        <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Students</h3>
        </div>
        <div class="box-body bg-info">
            <table class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th>First Name</th><th>Last Name</th><th>Action</th>
                </tr>
            </thead>
            <tbody id="studentsTrContainer">   
            </tbody>
            </table>
                </div>
            </div>
        </div>
        ';
        echo $out;
        } 
        // manage teachers
        if (isset($_POST['admnManageTeach'])) {
            // $out = '
            // <div class="col-md-6 maxvh70">
            // <div class="box box-primary box-solid">
            //   <div class="box-header with-border">
            //     <h3 class="box-title col-md-4">Add Teachers/Users</h3>
            //     <div class="box-tools pull-right">
            //   </div>
    
            //   </div>
            //   <div class="box-body bg-info">  
            //     <form class="form-inline" id="regTeachersTble">
                
            //         <div class="col-md-12 pad0 marb5">
            //             <input type="text" class="form-control input-sm" name="TFirstName" placeholder="FirstName">
                
            //             <input type="text" class="form-control input-sm" name="TLastName" placeholder="LastName">

            //             <input type="text" class="form-control input-sm" name="TParentNo" placeholder="User Name">
            //         </div>
    
            //         <div class="col-md-12 pad0 marb5">
            //             <input type="text" class="form-control input-sm" name="TPassword" placeholder="Password">
            //         </div>
                    
            //         <div class="col-md-12"> 
            //             <a type="submit" class="btn btn-primary btn-block btn-sm" id="registerTeachertBtn">
            //                 Register User
            //             </a>
            //         </div>
            //     </form>
            //   </div>
            // </div>
            // </div>
            // <div class="col-md-6">
            // <div class="box box-primary box-solid">
            // <div class="box-header with-border">
            //   <h3 class="box-title">Teachers / Users</h3>
            // </div>
            // <div class="box-body bg-info">
            //     <table class="table table-condensed table-hover">
            //     <thead>
            //         <tr>
            //             <th>First Name</th><th>Last Name</th><th>User Name</th><th>Action</th>
            //         </tr>
            //     </thead>
            //     <tbody id="teachersTrContainer">   
            //     </tbody>
            //     </table>
            //         </div>
            //     </div>
            // </div>
            // ';
            // echo $out;
        }
        // get classes tr
        if (isset($_POST['gtClassesTr'])) {
            $id = $_POST['gtClassesTr'];
            echo classTblRow($id);
        }
        // Add Class
        if (isset($_POST['classTxt'])) {
            $classTxt = $db->validation($_POST['classTxt']);
            $SectionTx = $_POST['SectionTx']; 

            $insClass = $db->insertRow('INSERT INTO `classess` (`Class`, `Section_id`) 
            VALUES (?,?)', [$classTxt, $SectionTx]);

            if ($insClass) {
                echo classTblRow($SectionTx);
            }
        }
        // save Edit Class
        if (isset($_POST['saveEditClsId'])) {
            $edtClsTxt = $_POST['saveEditClsId'];
            $edtId = $_POST['saveid'];
            $savEdt = $db->updateRow('UPDATE `classess` SET `Class`= ? WHERE `id` = ?', [$edtClsTxt, $edtId]);

            echo classTblRow($_POST['SectionId']);
        }
        // Dell Class
        if (isset($_POST['delClasId'])) {
            $id = $_POST['delClasId'];
            $getClass = $db->getRow('SELECT * FROM `classess` WHERE `id` = ?', [$id]);
            
            $delClass = $db->deleteRow('DELETE FROM `classess` WHERE `id` = ?', [$id]);
            echo classTblRow($getClass['Section_id']);
        }
        // get subjects tr
        if (isset($_POST['slSecClss'])) {
            $id = $_POST['slSecClss'];
            echo subjectsTblRow($id);
        }
        // add subjects  
        if (isset($_POST['subjectsTxt'])) {
            $subjectsTxt = $db->validation($_POST['subjectsTxt']); 
            $subSectionTx = $_POST['subSectionTx']; 

            $inssubjects = $db->insertRow('INSERT INTO `subjects` (`Subject`, `Section_id`) 
            VALUES (?,?)', [$subjectsTxt, $subSectionTx]);

            if ($inssubjects) {
                echo subjectsTblRow($subSectionTx);
            }
        }
        // del subject 
        if (isset($_POST['delSubjecId'])) {
            $id = $_POST['delSubjecId'];
            $gtClsId = $db->getRow('SELECT * FROM `subjects` WHERE `id` = ?', [$id]);
            $secIdd = $gtClsId['Section_id'];
            $delClass = $db->deleteRow('DELETE FROM `subjects` WHERE `id` = ?', [$id]);
            echo subjectsTblRow($secIdd);
        }
        // get studnets class
        if (isset($_POST['getStuClass'])) {
            $clsId = $_POST['getStuClass'];
            echo studentsTblRow($clsId);
        }
        // change students class
        // if(isset($_POST['chngFromCls'])) {
        //     $chngFromCls = $_POST['chngFromCls'];
        //     $chngFromCls = $_POST['chngFromCls'];

        //     $chngClss = $db->updateRow('UPDATE `students` SET `Classess_id` = ? WHERE = `Classess_id`',
        //     []);
        // }
        // reg student
        if (isset($_POST['FirstName'])) {
            $cntStu = $db->getRow('SELECT COUNT(`id`) as TotalStud FROM `students`');
            $Fname = $db->validation($_POST['FirstName']);
            $Mname = $db->validation($_POST['MiddleName']);
            $Lname = $db->validation($_POST['LastName']);
            $regNo = 'KA/' .date('y') .'/' .rand(100, 999) .$cntStu['TotalStud'];
            $ParentNo = $db->validation($_POST['ParentNo']);
            $Address = $db->validation($_POST['Address']);
            $Nationality = $db->validation($_POST['Nationality']);
            $StateofOr = $db->validation($_POST['StateofOr']);
            $LocalGvt = $db->validation($_POST['LocalGvt']);
            $selClassId = $_POST['selectClassId'];
          $regstu = $db->insertRow('INSERT INTO `students`(`First_Name`, `Middle_Name`, `Last_Name`, 
          `Reg_No`, `Parent_No`, `Stu_Address`, `Nationality`, `State_of_Origin`, `Lg_Area`, `Classess_id`) 
          VALUES (?,?,?,?,?,?,?,?,?,?)', [$Fname, $Mname, $Lname, $regNo, $ParentNo, 
          $Address, $Nationality, $StateofOr, $LocalGvt, $selClassId]);
          if ($regstu) {
              echo studentsTblRow($selClassId);
          }
        }
        // edit Student
        if (isset($_POST['editStuId'])) {
            $id = $_POST['editStuId'];
            $gtStu = $db->getRow('SELECT * FROM `students` WHERE `id` = ?', [$id]);
            $clasId = $gtStu['Classess_id'];
            echo gtStuEditForm($id); 
        }
        // edit save chang
        if (isset($_POST['Edit_FirstName'])) { 

            $Fname = $db->validation($_POST['Edit_FirstName']);
            $Mname = $db->validation($_POST['Edit_MiddleName']);
            $Lname = $db->validation($_POST['Edit_LastName']);
        
            $ParentNo = $db->validation($_POST['Edit_ParentNo']);
            $Address = $db->validation($_POST['Edit_Address']);
            $Nationality = $db->validation($_POST['Edit_Nationality']);
            $StateofOr = $db->validation($_POST['Edit_StateofOr']);
            $LocalGvt = $db->validation($_POST['Edit_LocalGvt']);
            $selClassId = $_POST['selectClassId'];
            $stuId = $_POST['studentId'];

          $regstu = $db->insertRow('UPDATE `students` SET `First_Name`= ?,
          `Middle_Name`= ?,`Last_Name`= ?, `Parent_No`= ?,
          `Stu_Address`= ?,`Nationality`= ?,`State_of_Origin`= ?,`Lg_Area`= ? 
          WHERE `id` = ? AND Classess_id = ?', [$Fname, $Mname, $Lname, $ParentNo, 
          $Address, $Nationality, $StateofOr, $LocalGvt, $stuId, $selClassId]);
          if ($regstu) {
              echo studentsTblRow($selClassId);
          }
        }
        //get stud empty form
        if (isset($_POST['getEptyFrm'])) {
            echo gtStuEmptyForm();
        }
        // del Student
        if (isset($_POST['delStuId'])) {
            $id = $_POST['delStuId'];
            $gtStu = $db->getRow('SELECT * FROM `students` WHERE `id` = ?', [$id]);
            $clasId = $gtStu['Classess_id'];
            $delstu = $db->deleteRow('DELETE FROM `students` WHERE `id` = ?', [$id]);
            echo studentsTblRow($clasId);
        }
    } else {

    }
    
    
    
    
    
    