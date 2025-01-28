<?php
if (
    (isset($_GET['err']) && $_GET['err'] == 'et9c') ||
    (isset($_GET['error']) && $_GET['error'] == 'et8c')
) { ?>
    <script>
        $(function () {
            Swal.fire({
                title: "Warning!",
                text: "Please choose an Meter Name.",
                icon: "warning",
                showConfirmButton: false,
                timer: 2500,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
    <?php
}
if (isset($_GET['PermissionError']) && $_GET['PermissionError'] == 403) { ?>
    <script>
    $(function () {
        Swal.fire({
            title: "Warning!",
            text: "Access is denied.",
            icon: "warning",
            showConfirmButton: false,
            timer: 3000,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
<?php
}
if(isset($_GET['Timeopen'])){ 
    $timetext = $_GET['Timeopen']; ?>
    <script>
        const time = "<?php echo isset($timetext) ? $timetext : ''; ?>";
        OpenModalScada(time);
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
<?php }
if (isset($_GET['SetArray']) != '') { ?>
    <script>
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
<?php }
if (isset($_GET['SetArray']) && $_GET['SetArray'] != '' && isset($_GET['Time'])) {
    $setText = $SCADA->SetTextField($_GET['SetArray']);
    $Time = $_GET['Time'];
    ?>
    <script>
        const Label = document.getElementById("Maxdays");
        const Time = "<?php echo $Time; ?>";
        if(Time == 'Archive - 1 Minute'){
          Label.textContent = "Maximum duration 3 days*";
        }else{
          Label.textContent = "Maximum duration 35 days*";
        }
        ChangeTagnodeList(<?php echo '"' . $setText . '",' . '"' . $Time . '"'; ?>);
    </script>
<?php }
if (isset($_GET['missing']) && $_GET['missing'] == 'error') { ?>
    <script>
        $(function () {
            Swal.fire({
                title: "Unidefined Data!",
                text: "define not done before fetch or execute and fetch.",
                icon: "warning",
                showConfirmButton: false,
                timer: 5000,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
    <?php
}
if (isset($_GET['tmpduplicate']) && $_GET['tmpduplicate'] == 'error') { ?>
    <script>
        $(function () {
            Swal.fire({
                title: "Duplicate!",
                text: "Name Template Duplicate.",
                icon: "warning",
                showConfirmButton: false,
                timer: 2500,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
    <?php
}
if (isset($_GET['save']) && $_GET['save'] == 'success' && isset($_GET['nametmp'])) { 
    $tmp = $_GET['nametmp'];
    ?>
    <script>
        $(function () {
            Swal.fire({
                title: "Success!",
                text: "Template <?php echo $tmp;?> added successfully.",
                icon: "success",
                showConfirmButton: false,
                timer: 2500,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
    <?php
} 
if (isset($_GET['Morethan']) && $_GET['Morethan'] == '35days') { 
    ?>
    <script>
        $(function () {
            Swal.fire({
                title: "Maximum!",
                text: "Maximum duration more than 35 days.",
                icon: "error",
                showConfirmButton: false,
                timer: 2500,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
    <?php
} 
if (isset($_GET['Morethan']) && $_GET['Morethan'] == '3days') { 
    ?>
    <script>
        $(function () {
            Swal.fire({
                title: "Maximum 3 days!",
                text: "Choose up to three days..",
                icon: "error",
                showConfirmButton: false,
                timer: 3000,
            })
        });
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", "Index.php");
        }
    </script>
    <?php
} ?>