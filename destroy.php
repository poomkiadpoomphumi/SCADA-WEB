<script src="https://alcdn.msauth.net/browser/2.35.0/js/msal-browser.min.js"></script>
<?php
session_start();
if($_SESSION["LOGIN_SESSION"] === "LOGIN_SSO"){
    session_destroy();
    echo "<script>
        localStorage.clear();
        const msalInstance = new msal.PublicClientApplication({
            auth: {
                clientId: '9e3b1e86-7296-456a-bb13-69936346d90a', 
                authority: 'https://login.microsoftonline.com/f2fda5e7-2ea1-450d-9fc1-2af5f8630095',
                redirectUri: 'https://pmis.pipeline.pttplc.com/GTMPMIS/SCADAWeb/v2/src/Index.php'
            }
        });
        msalInstance.logoutRedirect({
            postLogoutRedirectUri: 'https://pmis.pipeline.pttplc.com/GTMPMIS/SCADAWeb/v2/Login.php'
        });
    </script>";
    exit();
}
if ($_SESSION["LOGIN_SESSION"] === "LOGIN_ESS") {
    session_destroy();
    echo "<script>
        localStorage.clear();
         window.location.href = './Login.php';
        </script>";
    exit();
}
?>
