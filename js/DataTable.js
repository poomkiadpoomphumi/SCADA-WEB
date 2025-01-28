$(document).ready(function () {
  try {
    var table = $("#example").DataTable({
      lengthMenu: [
        [10, 25, 50, 100],
        [10, 25, 50, 100],
      ],
      searching: true,
      dom: '<"dt-buttons"Bf><"clear">lirtp',
      paging: true,
      autoWidth: true,
      buttons: ["csvHtml5", "excelHtml5"],
      initComplete: function (settings, json) {
        var footer = $("#example tfoot tr");
        $("#example thead").append(footer);
      },
    });
    } catch (error) {
      console.error(error);
    }
    // Apply the search
    $("#example thead").on("keyup", "input", function () {
      table.column($(this).parent().index()).search(this.value).draw();
    });
  });
  