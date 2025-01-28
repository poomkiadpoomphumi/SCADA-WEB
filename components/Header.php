<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historical Operation Data Web Application</title>
    <link rel="shortcut icon" href="../img/favicon.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="../css/Table.css">
    <link rel="stylesheet" href="../vendor/bootstrapTable/bound.css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <?php 
    $curPageName = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
    if($curPageName !== "ShowTag.php"){ ?> 
    <script src="../js/DataTable.js"></script>
    <?php } ?>
    <script src="../vendor/bootstrapTable/bound.js"></script>
    <script src="../js/Main.js"></script>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <script src="../vendor/sweetalert/sweetalert2.js"></script>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'>
</head>