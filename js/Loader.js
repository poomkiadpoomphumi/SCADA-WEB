$(document).ready(function () {
  let path = window.location.pathname;
  let filePath = path.substring(path.lastIndexOf('/')+1);
  let counter = 0;
  let c = 0;
  let n = 0;
  let i = setInterval(function () {
  if(filePath == 'Index.php' || 
     filePath == 'ManageUser.php' ||
     filePath == 'MeterConfig.php' ||
     filePath == 'Tagconfig.php' ||
     filePath == 'Template.php'){
     n = 11;}else{n = 101;}
    $(".loading-page .counter h1").html(c);
    counter++;
    c++;
    if (counter == n) {
      clearInterval(i);
      document.getElementById("loadpage").style.display = "block";
      document.getElementById("loading-p").style.display = "none";
    }
  }, 10);
});