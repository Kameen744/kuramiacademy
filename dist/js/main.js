
function formData(elm) {
    var data = {};
    $(elm)
      .find("[name]")
      .each(function(index, value) {
        var name = $(this).attr("name");
        value = $(this).val();
        data[name] = value;
      });
    return data;
  }
  //  function to handle jquery ajax request 
  function ajxobj (url, method, dataType, data) {
    this.jxhr = $.ajax({
      url: url,
      method: method,
      dataType: dataType,
      data: data
    });
  }
// function check if input is empty
function validate (elm) {
    if ($(elm).val() == '') {
        return false;
    } else {
        return $(elm).val();
    }
}
// Manage Students Btn
$(document).on('click', '#adminManageStudentsBtn', function (evnt) {
    evnt.preventDefault();
    $('#adminManageTeachersBtn, #adminManageScores, #adminManageSessionsBtn').removeClass('active');
    $(this).addClass('active');
    $('#aminPageHdTxt').text('Manage Students');
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {admnManageStu: 'admnManageStu'});
    post.jxhr.done(function (res) {
        $('#adminPageContainer').html(res);
    });
});
// add class select section 
$(document).on('change', '#addClassSectionTx', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {gtClassesTr: id});
        post.jxhr.done(function (res) {
        $('#classesTrContainer').html(res);
    });
});
// add Class Btn
$(document).on('click', '#addClassBtn', function () {
    var classTxt = validate('#addClassTx');
    var SectionTx = $('#addClassSectionTx').val();
    if (classTxt === false) {
        alert('Cannot accept empty form');
    } else {
        var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {classTxt: classTxt, SectionTx: SectionTx});
        post.jxhr.done(function (res) {
           $('#classesTrContainer').html(res);
        });
    }
});
// edit save class btn
$(document).on('click', '.saveEditClassBtn', function () {
    var id = $(this).val();
    var saveEditClsId = $('#edtCls' +id).val();
    var SectionId = $('#addClassSectionTx').val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', 
    {saveEditClsId: saveEditClsId, saveid: id, SectionId: SectionId});
    post.jxhr.done(function (res) {
       $('#classesTrContainer').html(res);
    });
});
// del class btn   
$(document).on('dblclick', '.deleteClassBtn', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {delClasId: id});
    post.jxhr.done(function (res) {
       $('#classesTrContainer').html(res);
    });
});
// // delete class btn 
// $(document).on('click', '.addCourseBtn', function () {
//     var id = $(this).val();
//     $('#saveSubChanges').val(id);
//     var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {addCourseModId: id});
//     post.jxhr.done(function (res) {
//        $('#addCoursesModalBody').html(res);
//     });
// });
// save class courses changes
$(document).on('click', '#saveSubChanges', function () {
    var id = $(this).val(); 
});
// select section classes
$(document).on('change', '#subjectSectionTx', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {slSecClss: id});
    post.jxhr.done(function (res) {
        $('#subjectsTrContainer').html(res);
    });
});
// add subjects Btn
$(document).on('click', '#addSubjectBtn', function () {
    var subjectsTxt = validate('#addSubjectTx');
    var subSectionTx = $('#subjectSectionTx').val();

    if (subjectsTxt === false) {
        alert('Cannot accept empty form');
    } else {
        var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', 
        {subjectsTxt: subjectsTxt, subSectionTx: subSectionTx});
        post.jxhr.done(function (res) {
           $('#subjectsTrContainer').html(res);
        });
    }
});
// del subject btn
$(document).on('dblclick', '.deleteSubjectBtn', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {delSubjecId: id});
    post.jxhr.done(function (res) {
       $('#subjectsTrContainer').html(res);
    });
});
// add students select class
$(document).on('change', '#selectClassId', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {getStuClass: id});
    post.jxhr.done(function (res) {
        $('#studentsTrContainer').html(res);
    }); 
});
// register students
$(document).on('click', '#registerStudentBtn', function () {
    var data = new formData('#regStudentsTble');
    data.selectClassId = $('#selectClassId').val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', data);
    post.jxhr.done(function (res) {
        $('#studentsTrContainer').html(res);
    });
});
// edit Stud
$(document).on('click', '.editStudentBtn', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {editStuId: id});
    post.jxhr.done(function (res) {
        $('#regStuFormCont').html(res);
    });
});
// edit save chang
$(document).on('click', '#saveEditChangesBtn', function (e) {
    e.preventDefault();
    var data = new formData('#regStudentsTble');
    data.selectClassId = $('#selectClassId').val();
    data.studentId = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', data);
    post.jxhr.done(function (res) {
        $('#studentsTrContainer').html(res);
        var postnd = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {getEptyFrm: 'getEptyFrm'});
        postnd.jxhr.done(function (res) {
            $('#regStuFormCont').html(res);
        })
    });
});
// del student 
$(document).on('dblclick', '.deleteStudentBtn', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {delStuId: id});
    post.jxhr.done(function (res) {
        $('#studentsTrContainer').html(res);
    });
});
// change students clsaa
// $(document).on('click', '#changeClassBtn', function () {
//     var chngFromCls = validate($('#selectClassId').val());
//     var chngToCls = validate($('#changeToSelectClassId').val());
//     var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', 
//     {chngFromCls: chngFromCls, chngToCls: chngToCls});
//     post.jxhr.done(function (res) {
//         $('#studentsTrContainer').html(res);
//     });
// });
// Manage Teachers Btn
$(document).on('click', '#adminManageTeachersBtn', function (evnt) {
    evnt.preventDefault();
    $('#adminManageScores, #adminManageStudentsBtn, #adminManageSessionsBtn').removeClass('active');
    $(this).addClass('active');
    $('#aminPageHdTxt').text('Manage Teachers');
    var post = new ajxobj('/kuramiacademy/php/mstudents.php', 'POST', 'HTML', {admnManageTeach: 'admnManageTeach'});
    post.jxhr.done(function (res) {
        $('#adminPageContainer').html(res);
    });
});
// manage sessions/terms
$(document).on('click', '#adminManageSessionsBtn', function (evnt) {
    evnt.preventDefault();
    $('#adminManageScores, #adminManageStudentsBtn, #adminManageTeachersBtn').removeClass('active');
    $(this).addClass('active');
    $('#aminPageHdTxt').text('Manage Sessions & Terms');
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', {admnManageSessTrms: 'admnManageSessTrms'});
    post.jxhr.done(function (res) {
        $('#adminPageContainer').html(res);
    });
});
// add years
$(document).on('click', '#addYearBtn', function () {
    var addYearTx = validate('#addYearTx');
    if (addYearTx == false) {
        alert('Cannot accept empty form');
    } else {
        var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', {addYearTx: addYearTx});
        post.jxhr.done(function (res) {
            $('#yearsTrContainer').html(res);
        });
    }
});
// del year
$(document).on('click', '#delYearBtn', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', {delYear: id});
    post.jxhr.done(function (res) {
        $('#yearsTrContainer').html(res);
    });
});
// select year
$(document).on('change', '#selcYearTx', function () {
    var id = $(this).val();
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', {selectTrmYear: id});
    post.jxhr.done(function (res) {
        $('#termsTrContainer').html(res);
    });
});
// add Term
$(document).on('click', '#addTermBtn', function () {
    var yearTxId = $('#selcYearTx').val();
    var addTermTxt = $('#addTermTxt').val(); 
    var addNextTermStartTxt = $('#addNextTermStartTxt').val(); 
    var addNextTermEndsTxt = $('#addNextTermEndsTxt').val();
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', 
    {addTermTxt: addTermTxt, yearTxId: yearTxId, 
    addNextTermStartTxt: addNextTermStartTxt, addNextTermEndsTxt: addNextTermEndsTxt});
    post.jxhr.done(function (res) {
        $('#termsTrContainer').html(res);
    });
});
// update term
$(document).on('click', '#saveChangeTermBtn', function () {
    var termid = $(this).val();
    var yearTxId = $('#selcYearTx').val();
    var updateTermTxt = $('#updateTermTxt').val(); 
    var updateNextTermStartTxt = $('#updateNextTermStartTxt').val(); 
    var updateNextTermEndsTxt = $('#updateNextTermEndsTxt').val();
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', 
    {updateTermTxt: updateTermTxt, updateyearTxId: yearTxId, termid: termid, 
    updateNextTermStartTxt: updateNextTermStartTxt, updateNextTermEndsTxt: updateNextTermEndsTxt});
    post.jxhr.done(function (res) {
       alert(res);
    });
});
//edit Term
$(document).on('click', '#editTermBtn', function () {
    var id = $(this).val();
    var yearTxId = $('#selcYearTx').val();
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', {editTermId: id, yearTxId: yearTxId});
    post.jxhr.done(function (res) {
        $('#termsBodyCont').html(res);
    });
})
// del term
$(document).on('click', '#delTermBtn', function () {
    var id = $(this).val();
    var yearTxId = $('#selcYearTx').val();
    var post = new ajxobj('/kuramiacademy/php/msesstrm.php', 'POST', 'HTML', {delTermId: id, yearTxId: yearTxId});
    post.jxhr.done(function (res) {
        $('#termsTrContainer').html(res);
    });
})
// manage Scores Btn
$(document).on('click', '#adminManageScores', function (evnt) {
    evnt.preventDefault();
    $('#adminManageTeachersBtn, #adminManageStudentsBtn, #adminManageSessionsBtn').removeClass('active');
    $(this).addClass('active');
    $('#aminPageHdTxt').text('Manage Scores');
    var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {admnManageScores: 'admnManageScores'});
    post.jxhr.done(function (res) {
        $('#adminPageContainer').html(res);
    });
});
// sel yrs term
$(document).on('change', '#addScoreYearId', function () {
    var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {gtYrsTrms: $(this).val()});
    post.jxhr.done(function (res) {
        $('#addScoreTermId').html(res);
        $('#addScoreYearInfoTxt').text($('#addScoreYearId option:selected').text() + ' - Session');
    });
});
// sel term 
$(document).on('change', '#addScoreTermId', function () {
    $('#addScoreTermInfoTxt').text($('#addScoreTermId option:selected').text() + ' - Term');
});
// sel sec classes
$(document).on('change', '#addScoreSectionId', function () {
    var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {gtSecClas: $(this).val()});
    post.jxhr.done(function (res) {
        $('#addScoreClassId').html(res);
        $('#addScoreSectionInfoTxt').text($('#addScoreSectionId option:selected').text() + ' - Section');
    });
    var postSub = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {gtSecSubjec: $(this).val()});
    postSub.jxhr.done(function (res) {
        $('#addScoreSubjectId').html(res);
    });
});
//  add scr sub id
$(document).on('change', '#addScoreSubjectId', function () {
    var YrId = validate('#addScoreYearId');
    var TrmId = validate('#addScoreTermId');
    var SecId = validate('#addScoreSectionId');
    var ClsId = validate('#addScoreClassId');
    var subjId = $(this).val();
    if (YrId == false | TrmId == false | SecId == false | ClsId == false) {

    } else {
        var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', 
        {gtClasStud: ClsId, subjId: subjId, TrmId: TrmId,  ScClsYrId: YrId});
        post.jxhr.done(function (res) {
            $('#classStudTr').html(res);
        });
    }
});
// sel class
$(document).on('change', '#addScoreClassId', function () {
    var YrId = validate('#addScoreYearId');
    var TrmId = validate('#addScoreTermId');
    var SecId = validate('#addScoreSectionId');
    var ClsId = validate('#addScoreClassId');
    var subjId = validate('#addScoreSubjectId');
    if (YrId == false | TrmId == false | SecId == false | ClsId == false) {

    } else {
        var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', 
        {gtClasStud: $(this).val(), subjId: subjId, TrmId: TrmId,  ScClsYrId: YrId});
        post.jxhr.done(function (res) {
            $('#classStudTr').html(res);
        });
        $('#addScoreClassInfoTxt').text($('#addScoreClassId option:selected').text() + ' - Class');
    }
});
// add Stu Score
$(document).on('click', '.addCrStuScore', function () {
    $('#addScoresInfoTxt').text($(this).text());
    var YrId = validate('#addScoreYearId');
    var TrmId = validate('#addScoreTermId');
    var SecId = validate('#addScoreSectionId');
    var ClsId = validate('#addScoreClassId');
    var subjId = validate('#addScoreSubjectId');
    var id = $(this).val();
    if (YrId == false | TrmId == false | SecId == false | ClsId == false | subjId == false) {
        alert('Error: Dettected an empty field');
    } else {
        var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', 
        {stuScorYrId: YrId, TrmId: TrmId, SecId: SecId, ClsId: ClsId, subjId: subjId, studId: id});
        post.jxhr.done(function (res) {
            $('#addStuScoresCont').html(res);
        }); 
    }
});
function validateNumberLimit (no, limit) {
    if (isNaN(no) === true | no > limit) {
        return false
    } else {
        return no;
    }
}
// add score btn
$(document).on('click', '#addScoreBtn', function () {
    var YrId = validate('#addScoreYearId');
    var TrmId = validate('#addScoreTermId');
    var SecId = validate('#addScoreSectionId');
    var ClsId = validate('#addScoreClassId');
    var subjId = validate('#addScoreSubjectId');

    var scFirstCaTxt = validateNumberLimit(validate('#scFirstCaTxt'), 20);
    var scSeconCaTxt = validateNumberLimit(validate('#scSeconCaTxt'), 20);
    var scExamTxt = validateNumberLimit(validate('#scExamTxt'), 60);
    var id = $(this).val();
    
    if (YrId == false | TrmId == false | SecId == false | ClsId == false 
        | subjId == false | scFirstCaTxt == false | scSeconCaTxt == false | scExamTxt == false) {
        alert('Dettected an empty or incorrect field');
    } else {
        
        var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', 
        {adscrYrId: YrId, TrmId: TrmId, SecId: SecId, 
        ClsId: ClsId, subjId: subjId, addStudScoreId: id, 
        scFirstCaTxt: scFirstCaTxt, scSeconCaTxt: scSeconCaTxt,
        scExamTxt: scExamTxt});

        post.jxhr.done(function (res) {
            $('#studScoresTrContainer').html(res);
            var postSec = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', 
            {gtClasStud: ClsId, subjId: subjId, TrmId: TrmId,  ScClsYrId: YrId});
            postSec.jxhr.done(function (res) {
                $('#classStudTr').html(res);
            });
        }); 
        
    }
});
// admin print report
$(document).on('click', '#adminPrintReport', function (evnt) {
    evnt.preventDefault();
    var post = new ajxobj('/kuramiacademy/php/mprint.php', 'POST', 'HTML', {admnPrintReport: 'admnPrintReport'});
    post.jxhr.done(function (res) {
        $('#adminPageContainer').html(res);
    });
});
// class sel
// $(document).on('change', '#printScoreClassId', function () {
//     var yr = $('#addScoreYearId').val(), trm = $('#addScoreTermId').val(), Sec = $('#addScoreSectionId').val();
//     if (yr == '' | trm == '' | Sec == '') {

//     } else {
//         var post = new ajxobj('/kuramiacademy/php/mprint.php', 'POST', 'HTML', {prntRptBtnShow: 'prntRptBtnShow', clsId: $(this).val()});
//         post.jxhr.done(function (res) {
//         $('#printBtnCont').html(res);
//         });
//     }
// });
// sel yrs term
$(document).on('change', '#printScoreYearId', function () {
    var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {gtYrsTrms: $(this).val()});
    post.jxhr.done(function (res) {
        $('#printScoreTermId').html(res);
        $('#printScoreYearInfoTxt').text($('#printScoreYearId option:selected').text() + ' - Session');
    });
});
// sel term 
$(document).on('change', '#printScoreTermId', function () {
    $('#printScoreTermInfoTxt').text($('#printScoreTermId option:selected').text() + ' - Term');
});
// sel sec classes
$(document).on('change', '#printScoreSectionId', function () {
    var post = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {gtSecClas: $(this).val()});
    post.jxhr.done(function (res) {
        $('#printScoreClassId').html(res);
        $('#printScoreSectionInfoTxt').text($('#printScoreSectionId option:selected').text() + ' - Section');
    });
    var postSub = new ajxobj('/kuramiacademy/php/mscores.php', 'POST', 'HTML', {gtSecSubjec: $(this).val()});
    postSub.jxhr.done(function (res) {
        $('#printScoreSubjectId').html(res);
    });
});
// sel class
$(document).on('change', '#printScoreClassId', function () {
    var YrId = validate('#printScoreYearId');
    var TrmId = validate('#printScoreTermId');
    var SecId = validate('#printScoreSectionId');
   // var ClsId = validate('#printScoreClassId');
    if (YrId == false | TrmId == false | SecId == false) {
        
    } else {
        var post = new ajxobj('/kuramiacademy/php/mprint.php', 'POST', 'HTML', {prntRptBtnShow: 'prntRptBtnShow', clsId: $(this).val()});
        post.jxhr.done(function (res) {
        $('#printBtnCont').html(res);
        });
    }
});
// admn prnt preview btn
$(document).on('click', '#adminPreviewReportBtn', function () {
    var YrId = validate('#printScoreYearId');
    var TrmId = validate('#printScoreTermId');
    var SecId = validate('#printScoreSectionId');
    var clsId = $('#printScoreClassId').val();

    var post = new ajxobj('/kuramiacademy/php/mprint.php', 'POST', 'HTML', 
    {printPreviewYrId: YrId, TrmId: TrmId, SecId: SecId, clsId: clsId});
    post.jxhr.done(function (res) {
        $('#admnReportSheetsCont').html(res);
    });
});
// admin prnt rpt btn
$(document).on('click', '#adminPrintReportBtn', function () {
    var YrId = validate('#printScoreYearId');
    var TrmId = validate('#printScoreTermId');
    var SecId = validate('#printScoreSectionId');
    var clsId = $('#printScoreClassId').val();

    var post = new ajxobj('/kuramiacademy/php/mprint.php', 'POST', 'HTML', 
    {printYrId: YrId, TrmId: TrmId, SecId: SecId, clsId: clsId});
    post.jxhr.done(function (res) {
        var w = window.open("", "Reports", "scrollbars=yes,width=900,height=600");
        w.document.write(res);
        w.document.close();
        setTimeout(function() {
          w.print();
        }, 1000);
    });

    // $.ajax({
    //     method: "POST",
    //     dataType: "html",
    //     url: '/kuramiacademy/php/printreport.php',
    //     data: {printYrId: YrId, TrmId: TrmId, SecId: SecId, clsId: clsId},
    //     success: function(data) {
    //       var w = window.open("", "CV", "scrollbars=yes,width=900,height=600");
    //       w.document.write(data);
    //       w.document.close();
    //       setTimeout(function() {
    //         w.print();
    //       }, 1000);
    //     }
    //   });
});
