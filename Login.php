<?php 
$isMobile = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));
if ($isMobile) {
    header("Location: ./error.html");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/Login.css" />
    <script src="./js/Main.js"></script>
    <title>Historical Operation Data Web Application</title>
    <link rel="shortcut icon" href="./img/favicon.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://alcdn.msauth.net/browser/2.35.0/js/msal-browser.min.js"></script>
</head>
<script>
const msalConfig = {
  auth: {
    clientId: "9e3b1e86-7296-456a-bb13-69936346d90a", // Replace with your app's Client ID
    authority: "https://login.microsoftonline.com/f2fda5e7-2ea1-450d-9fc1-2af5f8630095", // Or your tenant ID
    redirectUri: "https://pmis.pipeline.pttplc.com/GTMPMIS/SCADAWeb/v2/src/Index.php", // Replace with your redirect URI
  }
};
const msalInstance = new msal.PublicClientApplication(msalConfig);
    
const loginRequest = {
  scopes: ["openid", "profile", "email"]
};

const SSOLOGIN = () => {
  msalInstance.loginRedirect(loginRequest).then(response => {
      const account = response.account;
    }).catch(error => {
      console.error("Login error:", error);
    });
}
// Handle token acquisition after login
msalInstance.handleRedirectPromise().then(response => {
    if (response) {
      const account = response.account;
      const code = account.username.split("@");
      // Send `code[0]` to the backend
      fetch("https://pmis.pipeline.pttplc.com/GTMPMIS/SCADAWeb/v2/src/auth/PostFile.php", {
        method: "POST",headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ user_sso: code[0] })
      }).then(res => {
          if (res.ok) {
            // Redirect after session is set
            window.location.href = "https://pmis.pipeline.pttplc.com/GTMPMIS/SCADAWeb/v2/src/Index.php";
          } else {
            console.error("Failed to set session in PHP.");
          }
        }).catch(error => {
          console.error("Error sending data to backend:", error);
        });
    }
  }).catch(error => {
    console.error("Token acquisition error:", error);
  });
</script>
<body>
  <div class="form-login">
      <div class="login-container">
          <form action="./src/auth/PostFile.php" method="post" name="frmLogin">
              <?php if(isset($_GET['Incor']) && $_GET['Incor'] === 'Incor'){ ?>
              <script>
              setTimeout(function() {
                document.getElementById("alert").style.display = 'none';
                if (typeof window.history.pushState == 'function') {
                  window.history.pushState({}, "Hide", "Login.php");
                }
              }, 1500);
              </script>
                  <div class="alert alert-danger" role="alert" id="alert">
                      Incorrect username or password.
                  </div>
              <?php 
                  }else if(isset($_GET['Insuff']) && $_GET['Insuff'] === 'Insuff'){ ?>
                  <script>
                  setTimeout(function() {
                    document.getElementById("alert").style.display = 'none';
                    if (typeof window.history.pushState == 'function') {
                      window.history.pushState({}, "Hide", "Login.php");
                    }
                  }, 1500);
                  </script>
                  <div class="alert alert-danger" role="alert" align='center' id="alert">
                      Insufficient privileges. Please contact administrator.
                  </div>
              <?php  } ?>
              <label for="email-field" style="color:#8f8f8f;">Username<span style="color: red;">*</span></label>
              <input type="text" placeholder="PTT Employee ID" name="username" required>
              <label for="email-field" style="color:#8f8f8f;">Password<span style="color: red;">*</span></label>
              <input type="password" placeholder="Password" name="password" required>
              <br>
              <button class="login-button" type="submit">Sign in with ESS</button>
          </form>
            <div class="footer">
                <p><a href="javascript:void(0);" style="font-size:14px;color:red;">กรณี Login ด้วยรหัสพนักงาน ให้ใช้ Password เดียวกับ ESS</a></p>
            </div>
            <div class="or-container">
              <hr>
              <span>OR</span>
              <hr>
            </div>
          <div class="microsoft-login-container">
            <button type="button" class="login-button-microsoft" onClick="SSOLOGIN()">
                <i class="fab fa-microsoft"></i>
                Sign in with Microsoft
            </button>
          </div>
      </div>
  <div>
</body>
</html>