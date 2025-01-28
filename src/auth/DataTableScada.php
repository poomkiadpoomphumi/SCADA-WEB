<?php
class DataTableScada
{
    private static function CallbackIterator(){
        return new SCADA();
    }
    public static function getTemplate()
    {
        $table = '';
        self::CallbackIterator()->openDB();
        $sqlORA = self::CallbackIterator()->execu('SELECT * FROM TEMPLATES');
        $table .= "<table id='example' class='table-scada table-striped table-bordered' cellspacing='0' width='100%'>
        <thead><tr><th>#</th><th>Template Name</th><th>Template Owner</th</><th>Public</th><th>Action</th></tr></thead><tbody>";
        while ($row = self::CallbackIterator()->fetch_row($sqlORA)) {
            $id = $row['TMPL_ID'];
            $name = json_encode($row['TMPL_DESC']);
            $regx = preg_replace('/^"|(?<=\\\\)?"$/', '', $name);
            $table .= "<tr>
                    <td>" . $row['TMPL_ID'] . "</td>
                    <td>" . $row['TMPL_DESC'] . "</td>
                    <td>" . $row['TMPL_OWNER'] . "</td>
                    <td>" . $row['PUBLIC_FLAG'] . "</td>
                    <td>
                        <a class='btn btn-warning btn-sm' onclick='EditTMP$id()'>
                            <i class='fas fa-edit'></i>
                        </a>
                        <a class='btn btn-danger btn-sm' onclick=" . '"DeleteTemplate(' . $id . ')"' . ">
                            <i class='fas fa-trash-alt'></i>
                        </a>
                    </td>
                </tr>";
            if ($row['PUBLIC_FLAG'] === 'Y') {
                $ch = "<input name='checkflag$id' id='checkflag$id' class='form-check-input' type='checkbox' checked>";
            } else {
                $ch = "<input name='checkflag$id' id='checkflag$id' class='form-check-input' type='checkbox'>";
            }
            echo "
                    <script>
                    const EditTMP$id = () => {
                        Swal.fire({
                        title: '$regx',
                        html: `
                        <div class='form-group'>
                            <h5 class='text-edit'>Template ID : </h5>
                            <input type='text' id='tmp_id$id' class='form-control' value='$id' disabled>
                            <h5 class='text-edit'>Template owner :</h5>
                            <input type='text' id='tmp_owner$id' class='form-control' value='" . $row['TMPL_OWNER'] . "' disabled>
                            <h5 class='text-edit'>Template name :</h5>
                            <input type='text' id='tmp_name$id' class='form-control' value='$regx'><BR>
                            <div class='form-check'>
                                <label class='form-check-label'>Public : </label>
                                $ch
                            </div>
                        </div>`,
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonColor: '#5cb85c',
                        confirmButtonText: 'Save',
                        preConfirm: () => {
                            const tmp_id = Swal.getPopup().querySelector('#tmp_id$id').value
                            const tmp_owner = Swal.getPopup().querySelector('#tmp_owner$id').value
                            const tmp_name = Swal.getPopup().querySelector('#tmp_name$id').value
                            const checkflag = Swal.getPopup().querySelector('#checkflag$id')
                            return { tmp_id: tmp_id, tmp_owner: tmp_owner, tmp_name:tmp_name ,checkflag: checkflag}
                        }
                    }).then((result) => {
                        $.ajax({
                            url: '../src/auth/PostFile.php',
                            type: 'post',
                            data: { tmp_id: result.value.tmp_id, 
                                tmp_owner:result.value.tmp_owner, tmp_name:result.value.tmp_name,
                                checkflag:result.value.checkflag.checked },
                            success: function (result) {
                                console.log(result);
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Template has been update successfully.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000,
                                  }).then((e) => {
                                    window.location.reload(true);
                                  });
                            },
                            error: function (error) {
                              console.error(error);
                            },
                          });
                    })
                }
                    </script>";
        }
        $table .= "</tbody>
        </table>";
        return $table;
    }
    public static function getTagconfig($Period, $Tagname)
    {
        $table = '';
        $n = 1;
        self::CallbackIterator()->openDB();
        $Period === 'Daily' ? $ref = 'REF_DAILY_GMDR_TAG' : $ref = 'REF_HOURLY_GMDR_TAG';
        $sqlORA = self::CallbackIterator()->execu("SELECT * FROM $ref WHERE FC_NAME = '$Tagname' ORDER BY SORT_ORDER");
        $table .= "<table id='example' class='table-scada table-striped table-bordered' cellspacing='0' width='100%'>
        <thead><tr><th>#</th><th>Tag Name</th><th>Tag Header</th</><th>Description</th><th>Precision</th><th>Sort Order</th>
        <th>Check Meter</th><th>Action</th></tr></thead><input type='hidden' id='tbname' name='tbname' value='$ref'><tbody>";
        while ($row = self::CallbackIterator()->fetch_row($sqlORA)) { 
            $row['MONITOR_FLAG'] == 1 ? $monitor_flag = 'Y' : $monitor_flag = 'N';
            $tag = trim($row['TAGNAME']);
            $table .= "<tr>
                    <td>" . $n++ . "</td>
                    <td>" . $tag . "</td>
                    <td>" . $row['TAG_HEADER'] . "</td>
                    <td>" . $row['DESCRIPTION'] . "</td>
                    <td>" . $row['PRECISION'] . "</td>
                    <td>" . $row['SORT_ORDER'] . "</td>
                    <td>" . $monitor_flag . "</td>
                    <td>
                        <a class='btn btn-warning btn-sm' onclick='EditTMP$n()'>
                            <i class='fas fa-edit'></i>
                        </a>
                        <a class='btn btn-danger btn-sm' onclick=" . '"DeleteTag(' . "'$tag'" . ',' . "'$Tagname'" . ',' . "'$ref'" . ')"' . ">
                            <i class='fas fa-trash-alt'></i>
                        </a>
                    </td>
                </tr>";
            if ($row['MONITOR_FLAG'] == 1) {
                $ch = "<input class='form-check-input' name='check_meter$n' id='check_meter$n' type='checkbox' checked>";
            } else {
                $ch = "<input class='form-check-input' name='check_meter$n' id='check_meter$n' type='checkbox'>";
            }

            echo "
                    <script>
                    const EditTMP$n = () => {
                        Swal.fire({
                        title: '" . $Tagname . "',
                        html: `
                        <div class='form-group'>
                            <h5 class='text-edit'>Meter name : </h5>
                            <input type='hidden' id='oldtag$n' name='oldtag$n' value='$tag'>
                            <input type='text' id='meter_name$n' class='form-control' value='$Tagname' disabled>
                            <h5 class='text-edit'>Tag name : </h5>
                            <input type='text' id='tag_name$n' class='form-control' value='$tag'>
                            <h5 class='text-edit'>Tag header :</h5>
                            <input type='text' id='tag_header$n' class='form-control' value='" . $row['TAG_HEADER'] . "'>
                            <h5 class='text-edit'>Desription :</h5>
                            <input type='text' id='description$n' class='form-control' value='" . $row['DESCRIPTION'] . "'>
                            <div class='row'>
                                <div class='col-sm-6'>
                                    <h5 class='text-edit'>Precision :</h5>
                                    <input type='text' class='form-control' id='Precision$n' value='" . $row['PRECISION'] . "'>
                                </div>
                                <div class='col-sm-6'>
                                    <h5 class='text-edit'>Sort order :</h5>
                                    <input type='text' class='form-control' id='Sort_order$n' value='" . $row['SORT_ORDER'] . "'>
                                </div>
                            </div><BR>
                            <div class='form-check'>
                                <label class='form-check-label'>Check meter : </label>
                                $ch
                            </div>
                        </div>
                        </div>`,
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonColor: '#5cb85c',
                        confirmButtonText: 'Save',
                        preConfirm: () => {
                            const meter_name$n = Swal.getPopup().querySelector('#meter_name$n').value
                            const tag_name$n = Swal.getPopup().querySelector('#tag_name$n').value
                            const tag_header$n = Swal.getPopup().querySelector('#tag_header$n').value
                            const description$n = Swal.getPopup().querySelector('#description$n').value
                            const Precision$n = Swal.getPopup().querySelector('#Precision$n').value
                            const Sort_order$n = Swal.getPopup().querySelector('#Sort_order$n').value
                            const check_meter$n = Swal.getPopup().querySelector('#check_meter$n')
                            const oldtag$n = Swal.getPopup().querySelector('#oldtag$n').value
                            return { 
                                meter_name$n: meter_name$n, 
                                tag_name$n: tag_name$n, 
                                tag_header$n: tag_header$n, 
                                description$n: description$n,
                                Precision$n: Precision$n,
                                Sort_order$n: Sort_order$n,
                                check_meter$n: check_meter$n,
                                oldtag$n: oldtag$n
                            }
                        }
                    }).then((result) => {
                        const table = '$ref';
                        $.ajax({
                            url: '../src/auth/PostFile.php',
                            type: 'post',
                            data: { 
                                table: table,
                                meter_name: result.value.meter_name$n, 
                                tag_name: result.value.tag_name$n, 
                                tag_header: result.value.tag_header$n,
                                description: result.value.description$n,
                                Precision: result.value.Precision$n,
                                Sort_order: result.value.Sort_order$n,
                                check_meter: result.value.check_meter$n.checked,
                                oldtag: result.value.oldtag$n
                            },
                            success: function (e) {
                                Swal.fire({
                                    title: 'Success',
                                    text: result.value.tag_name + ' has been update successfully.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000,
                                  }).then((e) => {
                                    window.location.reload();
                                  });
                            },
                            error: function (error) {
                              console.error(error);
                            },
                          });
                    })
                }
            </script>";
        }
        $table .= "</tbody>
        </table>";
        return $table;
    }
    public static function getMeterConfig()
    {
        $n = 0;
        $table = '';
        self::CallbackIterator()->openDB();
        $sqlORA = self::CallbackIterator()->execu('SELECT * FROM REF_FC_NAME');
        $table .= "<table id='example' class='table-scada table-striped table-bordered' cellspacing='0' width='100%'>
        <thead><tr><th>#</th><th>Meter Name</th><th>Desription</th</><th>Maintenance Tag</th><th>Check DB Time</th>
        <th>Check FC Time</th><th>Check RTU Time</th><th>Action</th></tr></thead><tbody>";
        while ($row = self::CallbackIterator()->fetch_row($sqlORA)) {
            $n++;
            if ($row['CHECKTIME_DB'] == 1) {
                $inputDB = "<input class='form-check-input' name='check_db$n' id='check_db$n' type='checkbox' checked>";
                $checkTimeDB = 'Y';
            } else {
                $inputDB = "<input class='form-check-input' name='check_db$n' id='check_db$n' type='checkbox'>";
                $checkTimeDB = 'N';
            }
            if ($row['CHECKTIME_FLAG'] == 1) {
                $inputFlag = "<input class='form-check-input' name='check_flag$n' id='check_flag$n' type='checkbox' checked>";
                $checkTimeFlag = 'Y';
            } else {
                $inputFlag = "<input class='form-check-input' name='check_flag$n' id='check_flag$n' type='checkbox'>";
                $checkTimeFlag = 'N';
            }
            if ($row['CHECKTIME_RTU'] == 1) {
                $inputRTU = "<input class='form-check-input' name='check_rtu$n' id='check_rtu$n' type='checkbox' checked>";
                $checkTimeRTU = 'Y';
            } else {
                $inputRTU = "<input class='form-check-input' name='check_rtu$n' id='check_rtu$n' type='checkbox'>";
                $checkTimeRTU = 'N';
            }
            $fc_name = trim($row['FC_NAME']);
            $table .= "<tr>
                    <td>" . $n . "</td>
                    <td>" . $fc_name . "</td>
                    <td>" . $row['FC_DESC'] . "</td>
                    <td>" . $row['REMARK'] . "</td>
                    <td>" . $checkTimeDB . "</td>
                    <td>" . $checkTimeFlag . "</td>
                    <td>" . $checkTimeRTU . "</td>
                    <td>
                        <a class='btn btn-warning btn-sm' onclick='EditMeter$n()'>
                            <i class='fas fa-edit'></i>
                        </a>
                        <a class='btn btn-danger btn-sm' onclick=" . '"DeleteMeter(' . "'$fc_name'" . ')"' . ">
                            <i class='fas fa-trash-alt'></i>
                        </a>
                    </td>
                </tr>";
            echo "
                    <script>
                        const EditMeter$n = () => {
                            Swal.fire({
                            title: '" . $fc_name . "',
                            html: `
                            <div class='form-group'>
                                <h5 class='text-edit'>Meter name : </h5>
                                <input type='text' id='meter_name$n' class='form-control' value='$fc_name' disabled>
                                <h5 class='text-edit'>Description : </h5>
                                <input type='text' id='desription$n' class='form-control' value='" . $row['FC_DESC'] . "'>
                                <h5 class='text-edit'>Maintenance Tag :</h5>
                                <input type='text' id='maintenance$n' class='form-control' value='" . $row['REMARK'] . "'><BR>
                                <div class='form-check'>
                                    <label class='form-check-label'>DB Time : </label> $inputDB&nbsp&nbsp
                                    <label class='form-check-label'>FC Time : </label> $inputFlag&nbsp&nbsp
                                    <label class='form-check-label'>RTU Time : </label> $inputRTU
                                </div>
                            </div>
                            </div>`,
                            inputAttributes: {
                                autocapitalize: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonColor: '#5cb85c',
                            confirmButtonText: 'Save',
                            preConfirm: () => {
                                const meter_name = Swal.getPopup().querySelector('#meter_name$n').value
                                const desription = Swal.getPopup().querySelector('#desription$n').value
                                const maintenance = Swal.getPopup().querySelector('#maintenance$n').value
                                let check_db = Swal.getPopup().querySelector('#check_db$n');
                                let check_flag = Swal.getPopup().querySelector('#check_flag$n');
                                let check_rtu = Swal.getPopup().querySelector('#check_rtu$n');
                                return { 
                                    meter_name: meter_name,
                                    desription: desription,
                                    maintenance: maintenance,
                                    check_db: check_db,
                                    check_flag: check_flag,
                                    check_rtu: check_rtu
                                }
                            }
                        }).then((result) => {
                            $.ajax({
                                url: '../src/auth/PostFile.php',
                                type: 'post',
                                data: { 
                                    meter_name: result.value.meter_name, 
                                    desription: result.value.desription,
                                    maintenance: result.value.maintenance,
                                    check_db: result.value.check_db.checked,
                                    check_flag: result.value.check_flag.checked,
                                    check_rtu: result.value.check_rtu.checked,
                                },
                                success: function (result) {
                                    Swal.fire({
                                        title: 'Success',
                                        text: '$fc_name has been updated.',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 2500,
                                    }).then((e) => {
                                        window.location.reload();
                                    });
                                },
                                error: function (error) {
                                    console.error(error);
                                },
                            });
                        })
                    }
                </script>";
        }
        $table .= "</tbody>
        </table>";
        return $table;
    }
    public static function getUser($groupID)
    {
        $table = '';
        $n = 1;
        self::CallbackIterator()->openDB();
        $groupID == 1 ? $hidden1 = 'hidden' : $hidden1 = '';
        $groupID == 2 ? $hidden2 = 'hidden' : $hidden2 = '';
        $groupID == 3 ? $hidden3 = 'hidden' : $hidden3 = '';
        $groupID == 4 ? $hidden4 = 'hidden' : $hidden4 = '';
        $groupID == 5 ? $hidden5 = 'hidden' : $hidden5 = '';
        $groupID == 6 ? $hidden6 = 'hidden' : $hidden6 = '';
        $sqlORA = self::CallbackIterator()->execu("SELECT * FROM SYS_USERS WHERE GROUP_ID = $groupID");
        $table .= "<table id='example' class='table-scada table-striped table-bordered' cellspacing='0' width='100%'>
        <thead><tr><th>#</th><th>Users</th><th>Group</th</><th>Action</th</></tr></thead><tbody>";
        while ($row = self::CallbackIterator()->fetch_row($sqlORA)) {
            $Group = '';
            $user = $row['USER_NAME'];
            $Group = self::CallbackIterator()->CheckUserGroupPTT($row['GROUP_ID']);
            $table .= "<tr>
            <td>" . $n++ . "</td>
            <td>" . $user . "</td>
            <td>" . $Group . "</td>
            <td>
                <a class='btn btn-warning btn-sm' onclick='EditUsers$n()'>
                    <i class='fas fa-edit'></i>
                </a>
                <a class='btn btn-danger btn-sm' onclick=" . '"DeleteUsers(' . "'$user'" . ',' . "'$Group'" . ')"' . ">
                    <i class='fas fa-trash-alt'></i>
                </a>
            </td>
        </tr>";

            echo "
            <script>
                const EditUsers$n = () => {
                    Swal.fire({
                    title: 'Edit " . $user . "',
                    html: `
                    <div class='form-group'>
                        <h5 class='text-edit'>User Name : </h5>
                        <input type='hidden' id='keepuser$n' name='keepuser$n' value='$user'>
                        <input type='text' id='Username$n' class='form-control' value='$user'>
                        <h5 class='text-edit'>Group : </h5>
                        <select class='form-control' id='groupid$n'>
                            <option value= '" . $row['GROUP_ID'] . "' selected>$Group</option>
                            <option value='1' $hidden1>PTT Users</option>
                            <option value='2' $hidden2>PTT Admins</option>
                            <option value='3' $hidden3>SCADA History Users</option>
                            <option value='4' $hidden4>SCADA History Admins</option>
                            <option value='5' $hidden5>GMDR Users</option>
                            <option value='6' $hidden6>GMDR Admins</option>
                        </select>
                        
                    </div>
                    </div>`,
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#5cb85c',
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        const Username$n = Swal.getPopup().querySelector('#Username$n').value
                        const groupid$n = Swal.getPopup().querySelector('#groupid$n').value
                        const keepuser$n = Swal.getPopup().querySelector('#keepuser$n').value
                        return { 
                            Username$n: Username$n,
                            groupid$n: groupid$n,
                            keepuser$n: keepuser$n
                        }
                    }
                }).then((result) => {
                 $.ajax({
                      url: '../src/auth/PostFile.php',
                      type: 'post',
                      data: {                             
                        Username: result.value.Username$n,
                        groupid: result.value.groupid$n,
                        keepuser: result.value.keepuser$n, 
                      },
                      success: function (result) {
                          Swal.fire({
                              title: 'Success',
                              text: '$user has been updated.',
                              icon: 'success',
                              showConfirmButton: false,
                              timer: 2000,
                            }).then((e) => {
                              window.location.reload(true);
                            });
                      },
                      error: function (error) {
                                                        Swal.fire({
                              title: 'Success',
                              text: '$user has been updated.',
                              icon: 'success',
                              showConfirmButton: false,
                              timer: 2000,
                            }).then((e) => {
                              window.location.reload(true);
                            });
                      },
                    });
                })
            }
        </script>";
        }
        $table .= "</tbody>
        </table>";
        return $table;
    }
    public static function GetTagRTU($RTU,$sh_tag){
        $table = '';
        $sql = '';
        self::CallbackIterator()->openDB();
        if($sh_tag !== ""){
            $sql = "SELECT * FROM DUMP_UNIT_HIST WHERE DUMP_UNIT_HIST.TAGNAME LIKE '%".strtoupper($sh_tag)."%'";
        }else{
            $sql = "SELECT DUMP_UNIT_HIST.TAGNAME, DUMP_UNIT_HIST.DESCRIPTION 
            FROM DUMP_UNIT_HIST WHERE DUMP_UNIT_HIST.RTU = '".$RTU."' ORDER BY DUMP_UNIT_HIST.TAGNAME";
        }
        $sqlORA = self::CallbackIterator()->execu($sql);
        $table .= "
        <table id='example' class='table-scada table-striped table-bordered' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Tag Name</th>
                <th>Description</th>
                <th>Current &nbsp&nbsp<input class='form-check-input' id='CurrentTag' type='checkbox' /></th>
                <th>Average &nbsp&nbsp<input class='form-check-input' id='AverageTag' type='checkbox' /></th>
                <th>Min &nbsp&nbsp<input class='form-check-input' id='MinTag' type='checkbox' /></th>
                <th>Max &nbsp&nbsp<input class='form-check-input' id='MaxTag' type='checkbox' /></th>
                <th>Diff Index &nbsp&nbsp<input class='form-check-input' id='DiffIndexTag' type='checkbox' /></th>
                <th>Not used &nbsp&nbsp<input class='form-check-input' id='NotusedTag' type='checkbox' /></th>
                <th>Integrated &nbsp&nbsp<input class='form-check-input' id='IntegratedTag' type='checkbox' /></th>
            </tr>
        </thead><tbody>";
        while ($row = self::CallbackIterator()->fetch_row($sqlORA)) {
            $table .= "<tr>
            <td>" . $row['TAGNAME'] . "</td>
            <td>" . $row['DESCRIPTION'] . "</td>
            <td><input class='form-check-input1' name='CurrentTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
            <td><input class='form-check-input2' name='AverageTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
            <td><input class='form-check-input3' name='MinTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
            <td><input class='form-check-input4' name='MaxTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
            <td><input class='form-check-input5' name='DiffIndexTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
            <td><input class='form-check-input6' name='NotusedTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
            <td><input class='form-check-input7' name='IntegratedTag' value=". $row['TAGNAME'] ." type='checkbox'></td>
        </tr>";
        }
        $table .= "</tbody>
        </table>
        <a class='btn btn-danger btn-sm' style='float:right;margin:-42px 48px 0px 0px;' onclick='CancleAddTag()'>Cancle</a>
        <a class='btn btn-primary btn-sm' style='float:right;margin:-42px 0px 0px 0px;' onclick='SubmitAddTag()'>Add</a>";
        return $table;
    }
};
?>