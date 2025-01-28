function custom_dropdowns_SCADA() {
  $("#cboTemplate1").each(function (i, select) {
    if (!$(this).next().hasClass("dropdown-select-scada")) {
      $(this).after(
        '<div class="dropdown-select-scada wide ' +
          ($(this).attr("class") || "") +
          '" tabindex="0"><span class="current"></span><div class="list-scada"><ul class="list-ul-scada"></ul></div></div>'
      );
      var dropdown = $(this).next();
      var options = $(select).find("option");
      var selected = $(this).find("option:selected");
      var storedValue = localStorage.getItem("tmp_id");
      // Set the initial selection
      if (storedValue) {
        var storedOption = options.filter(`[value="${storedValue}"]`);
        if (storedOption.length) {
          selected = storedOption;
          $(this).val(storedValue); // Update the original select
        }
      }
      
      dropdown
        .find(".current")
        .html(selected.data("display-text") || selected.text());
      options.each(function (j, o) {
        var display = $(o).data("display-text") || "";
        dropdown
          .find(".list-ul-scada")
          .append(
            '<li class="option-scada ' +
              ($(o).is(":selected") ? "selected" : "") +
              '" data-value="' +
              $(o).val() +
              '" data-display-text="' +
              display +
              '">' +
              $(o).text() +
              "</li>"
          );
      });
    }
  });
  $(".dropdown-select-scada .list-ul-scada").before(
    '<div class="dd-search-scada"><input id="txtSearchValue1" placeholder="Search" autocomplete="off" onkeyup="filterScada()" class="dd-searchbox-scada" type="text"></div>'
  );
}
// Option click
$(document).on(
  "click",
  ".dropdown-select-scada .option-scada",
  function (event) {
    $(this).closest(".list-scada").find(".selected").removeClass("selected");
    $(this).addClass("selected");
    var text = $(this).data("display-text") || $(this).text();
    $(this).closest(".dropdown-select-scada").find(".current").text(text);
    $(this)
      .closest(".dropdown-select-scada")
      .prev("#cboTemplate1")
      .val($(this).data("value"))
      .trigger("change");
    document.getElementById("tagnamescada").value = text;
    document.getElementById("addtag").value = '';
    localStorage.setItem('tmp_id', $(this).data("value")); // Fixed
  }
);

// Event listeners

// Open/close
$(document).on("click", ".dropdown-select-scada", function (event) {
  if ($(event.target).hasClass("dd-searchbox-scada")) {
    return;
  }
  $(".dropdown-select-scada").not($(this)).removeClass("open");
  $(this).toggleClass("open");
  if ($(this).hasClass("open")) {
    $(this).find(".option-scada").attr("tabindex", 0);
    $(this).find(".selected").focus();
  } else {
    $(this).find(".option-scada").removeAttr("tabindex");
    $(this).focus();
  }
});

// Close when clicking outside
$(document).on("click", function (event) {
  if ($(event.target).closest(".dropdown-select-scada").length === 0) {
    $(".dropdown-select-scada").removeClass("open");
    $(".dropdown-select-scada .option-scada").removeAttr("tabindex");
  }
  event.stopPropagation();
});

function filterScada() {
  var valThis = $("#txtSearchValue1").val();
  $(".dropdown-select-scada .list-ul-scada > li").each(function () {
    var text = $(this).text();
    text.toLowerCase().indexOf(valThis.toLowerCase()) > -1
      ? $(this).show()
      : $(this).hide();
  });
}


// Keyboard events
$(document).on("keydown", ".dropdown-select-scada", function (event) {
  var focused_option = $(
    $(this).find(".list-scada .option-scada:focus")[0] ||
      $(this).find(".list-scada .option-scada.selected")[0]
  );
  // Space or Enter
  if (event.keyCode == 32 || event.keyCode == 13) {
    //if (event.keyCode == 13) {
    if ($(this).hasClass("open")) {
      focused_option.trigger("click");
    } else {
      $(this).trigger("click");
    }
    return false;
    // Down
  } else if (event.keyCode == 40) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      focused_option.next().focus();
    }
    return false;
    // Up
  } else if (event.keyCode == 38) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      var focused_option = $(
        $(this).find(".list-scada .option-scada:focus")[0] ||
          $(this).find(".list-scada .option-scada.selected")[0]
      );
      focused_option.prev().focus();
    }
    return false;
    // Esc
  } else if (event.keyCode == 27) {
    if ($(this).hasClass("open")) {
      $(this).trigger("click");
    }
    return false;
  }
});

$(document).ready(function () {
  custom_dropdowns_SCADA();
});

function custom_dropdowns_GMDR() {
  $("#cboTemplate2").each(function (i, select) {
    if (!$(this).next().hasClass("dropdown-select-gmdr")) {
      $(this).after(
        '<div class="dropdown-select-gmdr wide ' +
          ($(this).attr("class") || "") +
          '" tabindex="0"><span class="current"></span><div class="list-gmdr"><ul class="list-ul-gmdr"></ul></div></div>'
      );
      var dropdown = $(this).next();
      var options = $(select).find("option");
      var selected = $(this).find("option:selected");
      dropdown
        .find(".current")
        .html(selected.data("display-text") || selected.text());
      options.each(function (j, o) {
        var display = $(o).data("display-text") || "";
        dropdown
          .find(".list-ul-gmdr")
          .append(
            '<li class="option-gmdr ' +
              ($(o).is(":selected") ? "selected" : "") +
              '" data-value="' +
              $(o).val() +
              '" data-display-text="' +
              display +
              '">' +
              $(o).text() +
              "</li>"
          );
      });
    }
  });

  $(".dropdown-select-gmdr .list-ul-gmdr").before(
    '<div class="dd-search-gmdr"><input id="txtSearchValue2" placeholder="Search" autocomplete="off" onkeyup="filterGmdr()" class="dd-searchbox-gmdr" type="text"></div>'
  );
}

// Event listeners

// Open/close
$(document).on("click", ".dropdown-select-gmdr", function (event) {
  if ($(event.target).hasClass("dd-searchbox-gmdr")) {
    return;
  }
  $(".dropdown-select-gmdr").not($(this)).removeClass("open");
  $(this).toggleClass("open");
  if ($(this).hasClass("open")) {
    $(this).find(".option-gmdr").attr("tabindex", 0);
    $(this).find(".selected").focus();
  } else {
    $(this).find(".option-gmdr").removeAttr("tabindex");
    $(this).focus();
  }
});

// Close when clicking outside
$(document).on("click", function (event) {
  if ($(event.target).closest(".dropdown-select-gmdr").length === 0) {
    $(".dropdown-select-gmdr").removeClass("open");
    $(".dropdown-select-gmdr .option-gmdr").removeAttr("tabindex");
  }
  event.stopPropagation();
});

function filterGmdr() {
  var valThis = $("#txtSearchValue2").val();
  $(".dropdown-select-gmdr .list-ul-gmdr > li").each(function () {
    var text = $(this).text();
    text.toLowerCase().indexOf(valThis.toLowerCase()) > -1
      ? $(this).show()
      : $(this).hide();
  });
}
// Search

// Option click
$(document).on("click", ".dropdown-select-gmdr .option-gmdr", function (event) {
  $(this).closest(".list-gmdr").find(".selected").removeClass("selected");
  $(this).addClass("selected");
  var text = $(this).data("display-text") || $(this).text();
  $(this).closest(".dropdown-select-gmdr").find(".current").text(text);
  $(this)
    .closest(".dropdown-select-gmdr")
    .prev("#cboTemplate2")
    .val($(this).data("value"))
    .trigger("change");
});

// Keyboard events
$(document).on("keydown", ".dropdown-select-gmdr", function (event) {
  var focused_option = $(
    $(this).find(".list-gmdr .option-gmdr:focus")[0] ||
      $(this).find(".list-gmdr .option-gmdr.selected")[0]
  );
  // Space or Enter
  if (event.keyCode == 32 || event.keyCode == 13) {
    //if (event.keyCode == 13) {
    if ($(this).hasClass("open")) {
      focused_option.trigger("click");
    } else {
      $(this).trigger("click");
    }
    return false;
    // Down
  } else if (event.keyCode == 40) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      focused_option.next().focus();
    }
    return false;
    // Up
  } else if (event.keyCode == 38) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      var focused_option = $(
        $(this).find(".list-gmdr .option-gmdr:focus")[0] ||
          $(this).find(".list-gmdr .option-gmdr.selected")[0]
      );
      focused_option.prev().focus();
    }
    return false;
    // Esc
  } else if (event.keyCode == 27) {
    if ($(this).hasClass("open")) {
      $(this).trigger("click");
    }
    return false;
  }
});

$(document).ready(function () {
  custom_dropdowns_GMDR();
});

function custom_dropdowns_tagconfig() {
  $("#cboTemplate3").each(function (i, select) {
    if (!$(this).next().hasClass("dropdown-select-tagconfig")) {
      $(this).after(
        '<div class="dropdown-select-tagconfig wide ' +
          ($(this).attr("class") || "") +
          '" tabindex="0"><span class="current"></span><div class="list-tagconfig"><ul class="list-ul-tagconfig"></ul></div></div>'
      );
      var dropdown = $(this).next();
      var options = $(select).find("option");
      var selected = $(this).find("option:selected");
      dropdown
        .find(".current")
        .html(selected.data("display-text") || selected.text());
      options.each(function (j, o) {
        var display = $(o).data("display-text") || "";
        dropdown
          .find(".list-ul-tagconfig")
          .append(
            '<li class="option-tagconfig ' +
              ($(o).is(":selected") ? "selected" : "") +
              '" data-value="' +
              $(o).val() +
              '" data-display-text="' +
              display +
              '">' +
              $(o).text() +
              "</li>"
          );
      });
    }
  });

  $(".dropdown-select-tagconfig .list-ul-tagconfig").before(
    '<div class="dd-search-tagconfig"><input id="txtSearchValue3" placeholder="Search" autocomplete="off" onkeyup="filterTagconfig()" class="dd-searchbox-tagconfig" type="text"></div>'
  );
}

// Event listeners

// Open/close
$(document).on("click", ".dropdown-select-tagconfig", function (event) {
  if ($(event.target).hasClass("dd-searchbox-tagconfig")) {
    return;
  }
  $(".dropdown-select-tagconfig").not($(this)).removeClass("open");
  $(this).toggleClass("open");
  if ($(this).hasClass("open")) {
    $(this).find(".option-tagconfig").attr("tabindex", 0);
    $(this).find(".selected").focus();
  } else {
    $(this).find(".option-tagconfig").removeAttr("tabindex");
    $(this).focus();
  }
});

// Close when clicking outside
$(document).on("click", function (event) {
  if ($(event.target).closest(".dropdown-select-tagconfig").length === 0) {
    $(".dropdown-select-tagconfig").removeClass("open");
    $(".dropdown-select-tagconfig .option-tagconfig").removeAttr("tabindex");
  }
  event.stopPropagation();
});

function filterTagconfig() {
  var valThis = $("#txtSearchValue3").val();
  $(".dropdown-select-tagconfig .list-ul-tagconfig > li").each(function () {
    var text = $(this).text();
    text.toLowerCase().indexOf(valThis.toLowerCase()) > -1
      ? $(this).show()
      : $(this).hide();
  });
}
// Search

// Option click
$(document).on(
  "click",
  ".dropdown-select-tagconfig .option-tagconfig",
  function (event) {
    $(this)
      .closest(".list-tagconfig")
      .find(".selected")
      .removeClass("selected");
    $(this).addClass("selected");
    var text = $(this).data("display-text") || $(this).text();
    $(this).closest(".dropdown-select-tagconfig").find(".current").text(text);
    $(this)
      .closest(".dropdown-select-tagconfig")
      .prev("#cboTemplate3")
      .val($(this).data("value"))
      .trigger("change");
  }
);

// Keyboard events
$(document).on("keydown", ".dropdown-select-tagconfig", function (event) {
  var focused_option = $(
    $(this).find(".list-tagconfig .option-tagconfig:focus")[0] ||
      $(this).find(".list-tagconfig .option-tagconfig.selected")[0]
  );
  // Space or Enter
  if (event.keyCode == 32 || event.keyCode == 13) {
    //if (event.keyCode == 13) {
    if ($(this).hasClass("open")) {
      focused_option.trigger("click");
    } else {
      $(this).trigger("click");
    }
    return false;
    // Down
  } else if (event.keyCode == 40) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      focused_option.next().focus();
    }
    return false;
    // Up
  } else if (event.keyCode == 38) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      var focused_option = $(
        $(this).find(".list-tagconfig .option-tagconfig:focus")[0] ||
          $(this).find(".list-tagconfig .option-tagconfig.selected")[0]
      );
      focused_option.prev().focus();
    }
    return false;
    // Esc
  } else if (event.keyCode == 27) {
    if ($(this).hasClass("open")) {
      $(this).trigger("click");
    }
    return false;
  }
});

$(document).ready(function () {
  custom_dropdowns_tagconfig();
});

function custom_dropdowns_RTU() {
  $("#cboTemplate_RTU").each(function (i, select) {
    if (!$(this).next().hasClass("dropdown-select-RTU")) {
      $(this).after(
        '<div class="dropdown-select-RTU wide ' +
          ($(this).attr("class") || "") +
          '" tabindex="0"><span class="current_shtag"></span><div class="list-RTU"><ul class="list-ul-RTU"></ul></div></div>'
      );
      var dropdown = $(this).next();
      var options = $(select).find("option");
      var selected = $(this).find("option:selected");
      dropdown
        .find(".current_shtag")
        .html(selected.data("display-text") || selected.text());
      options.each(function (j, o) {
        var display = $(o).data("display-text") || "";
        dropdown
          .find(".list-ul-RTU")
          .append(
            '<li class="option-RTU ' +
              ($(o).is(":selected") ? "selected" : "") +
              '" data-value="' +
              $(o).val() +
              '" data-display-text="' +
              display +
              '">' +
              $(o).text() +
              "</li>"
          );
      });
    }
  });

  $(".dropdown-select-RTU .list-ul-RTU").before(
    '<div class="dd-search-RTU"><input id="txtSearchValue_RTU" placeholder="Search" autocomplete="off" onkeyup="filterRTU()" class="dd-searchbox-RTU" type="text"></div>'
  );
}

// Event listeners

// Open/close
$(document).on("click", ".dropdown-select-RTU", function (event) {
  if ($(event.target).hasClass("dd-searchbox-RTU")) {
    return;
  }
  $(".dropdown-select-RTU").not($(this)).removeClass("open");
  $(this).toggleClass("open");
  if ($(this).hasClass("open")) {
    $(this).find(".option-RTU").attr("tabindex", 0);
    $(this).find(".selected").focus();
  } else {
    $(this).find(".option-RTU").removeAttr("tabindex");
    $(this).focus();
  }
});

// Close when clicking outside
$(document).on("click", function (event) {
  if ($(event.target).closest(".dropdown-select-RTU").length === 0) {
    $(".dropdown-select-RTU").removeClass("open");
    $(".dropdown-select-RTU .option-RTU").removeAttr("tabindex");
  }
  event.stopPropagation();
});

function filterRTU() {
  var valThis = $("#txtSearchValue_RTU").val();
  $(".dropdown-select-RTU .list-ul-RTU > li").each(function () {
    var text = $(this).text();
    text.toLowerCase().indexOf(valThis.toLowerCase()) > -1
      ? $(this).show()
      : $(this).hide();
  });
}
const setDefaultselect = async () => {
 let i = 0;
 const elements = document.getElementsByClassName("current_shtag");
 elements[0].innerHTML = "  -- Please choose RTU--";
 const option = document.querySelectorAll('li.option-RTU');
   option.forEach(function(element) {
      i == 1 ? element.classList.add("selected") : element.classList.remove('selected');
      i += 1;
  });
}
//change color dismiss
$(document).on("click", "#sh_tag", function (event) {
 const input = document.getElementById("sh_tag");
 input.style.backgroundColor = "#fff";
 const div = document.getElementsByClassName("dropdown-select-RTU wide");
 div[0].style.backgroundColor = "#ddd";
 setDefaultselect();
});
// Option click
$(document).on("click", ".dropdown-select-RTU .option-RTU", function (event) {
  document.getElementById("sh_tag").value = "";
  document.getElementById("sh_tag").style.backgroundColor = "#ddd";
  const div = document.getElementsByClassName("dropdown-select-RTU wide");
  div[0].style.backgroundColor = "#fff";
  $(this).closest(".list-RTU").find(".selected").removeClass("selected");
  $(this).addClass("selected");
  var text = $(this).data("display-text") || $(this).text();
  $(this).closest(".dropdown-select-RTU").find(".current_shtag").text(text);
  $(this).closest(".dropdown-select-RTU").prev("#cboTemplate_RTU").val($(this).data("value")).trigger("change");
});

// Keyboard events
$(document).on("keydown", ".dropdown-select-RTU", function (event) {
  var focused_option = $(
    $(this).find(".list-RTU .option-RTU:focus")[0] ||
      $(this).find(".list-RTU .option-RTU.selected")[0]
  );
  // Space or Enter
  if (event.keyCode == 32 || event.keyCode == 13) {
    //if (event.keyCode == 13) {
    if ($(this).hasClass("open")) {
      focused_option.trigger("click");
    } else {
      $(this).trigger("click");
    }
    return false;
    // Down
  } else if (event.keyCode == 40) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      focused_option.next().focus();
    }
    return false;
    // Up
  } else if (event.keyCode == 38) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      var focused_option = $(
        $(this).find(".list-RTU .option-RTU:focus")[0] ||
          $(this).find(".list-RTU .option-RTU.selected")[0]
      );
      focused_option.prev().focus();
    }
    return false;
    // Esc
  } else if (event.keyCode == 27) {
    if ($(this).hasClass("open")) {
      $(this).trigger("click");
    }
    return false;
  }
});

$(document).ready(function () {
  custom_dropdowns_RTU();
});



const ChangeTimeSelectScada = async (ch) => {
  const today = document.getElementById("today");
  const l60min = document.getElementById("l60min");
  const l24hr = document.getElementById("l24hr");
  const Yes = document.getElementById("Yes");
  const day = document.getElementById("day");
  switch (ch) {
    case "1 Minute":
      today.style.display = "block";
      l60min.style.display = "block";
      l24hr.style.display = "none";
      Yes.style.display = "none";
      day.style.display = "none";
      break;
    case "10 Minute":
      today.style.display = "block";
      l60min.style.display = "block";
      l24hr.style.display = "block";
      Yes.style.display = "none";
      day.style.display = "none";
      break;
    case "Hour":
      today.style.display = "block";
      l60min.style.display = "none";
      l24hr.style.display = "block";
      Yes.style.display = "block";
      day.style.display = "none";
      break;
    case "Day":
      today.style.display = "none";
      l60min.style.display = "none";
      l24hr.style.display = "none";
      Yes.style.display = "none";
      day.style.display = "block";
      break;
  }
};
const ChangeTimeSelectGMDR = (ck) => {
  const GMDRHOUR = document.getElementById("GMDRHOUR");
  const GMDRDAY = document.getElementById("GMDRDAY");
  switch (ck) {
    case "Hour":
      GMDRHOUR.style.display = "block";
      GMDRDAY.style.display = "none";
      break;
    case "Day":
      GMDRHOUR.style.display = "none";
      GMDRDAY.style.display = "block";
      break;
  }
};
const OpenModalGMDR = (e) => {
  document.getElementById("GMDRModalLabel").innerHTML = "GMDR - " + e;
  document.getElementById("GMDRSETTABLE").value = e;
  ChangeTimeSelectGMDR(e);
  $("#GMDRmyModal").modal("show");
};
const OpenModalTagConfig = () => {
  $("#tagconfig").modal("show");
};
const DisplayList = () => {
  const DivTemp = document.getElementById("displayTemp");
  const DivCondition = document.getElementById("displayCondition");
  const DivFromdate = document.getElementById("displayFromdate");
  const DivTodate = document.getElementById("displayTodate");
  const DivAddTag = document.getElementById("AddTagName");
  const BtnAdd = document.getElementById("FootAdd");
  const BtnStart = document.getElementById("Start");
  const template_select = document.getElementById("template_select");
  const sh_tag_div = document.getElementById("sh_tag_div");
  return [
    DivTemp,
    DivCondition,
    DivFromdate,
    DivTodate,
    DivAddTag,
    BtnAdd,
    BtnStart,
    template_select,
    sh_tag_div
  ];
};
const ClearTag = async () => {
  const textarea = document.getElementById("message-text");
  if(textarea != ''){
    textarea.value = "";
    textarea.classList.remove('my-hover');
  }
  document.querySelectorAll('.line-item').forEach(item => { item.remove(); });
  localStorage.removeItem('tag_name');
  localStorage.removeItem('item');
  localStorage.removeItem('tmp_id');
  
    const currentElement = document.querySelector('span.current');
    currentElement.textContent = '-- Choose a Template --';
    const selectElement = document.getElementById('cboTemplate1');
    selectElement.value = '';
    const optionExists = [...selectElement.options].some(option => option.value === '');
    const optionListItems = document.querySelectorAll('.option-scada');
    optionListItems.forEach(li => {
        // Remove "selected" class from all <li> elements
        li.classList.remove('selected');
        // Add "selected" class to the matching <li>
        if (li.getAttribute('data-value') === '') {
            li.classList.add('selected');
        }
    });
    document.getElementById("period").value = '';
    document.getElementById("tagnamescada").value = '';
    document.getElementById("addtag").value = '';
    // Remove each line-item from the DOM
    document.querySelectorAll('.line-item').forEach(item => { item.remove(); });
};
const AddTag = async () => {
  CallbackEventDisplay();
};
const Cancle = async () => {
  CallbackEventDisplay();
};
const CallbackEventDisplay = async () => {
  DisplayList().map((e) => {
    ChangeEventDisplay(e);
  });
};
const ChangeEventDisplay = async (event) => {
  return event.style.display == "block"
    ? (event.style.display = "none")
    : (event.style.display = "block");
};

// Function to render spans based on current localStorage contents
function renderItems(currentItems) {
  const tagname = document.getElementById("tagnamescada");
  let tag_name = localStorage.getItem('tag_name');
  localStorage.setItem("tag_name",tagname.value || tag_name);
  if (currentItems) {
    const spans = currentItems.replace(/<br\s*\/?>/g, '<br/>')
      .split("<br/>")
      .filter(line => line.trim() !== '') 
      .map(line => `
        <div class="line-item" style="display: flex; align-items: center; padding-left: 10px">
          <span data-value="${line}" style="flex: 1; cursor: pointer;">${line}</span>
          <button class="btn btn-danger btn-sm" style="padding: 0rem 0.5rem;">X</button>
        </div>
      `)
      .join("");

    // Update the content of the message-text container
    $("#message-text").html(spans);
    $("#message-text-hidden").val(currentItems);
    // Re-attach event listeners for click and hover
    $(".line-item span")
      .on("mouseover", function () {
        $(this).css("background-color", "#f0f0f0");
      })
      .on("mouseout", function () {
        $(this).css("background-color", "transparent");
      });

    // Re-attach click event for buttons
    $(".line-item button").on("click", function (event) {
      // Prevent the span click event from triggering
      event.stopPropagation();
      // Get the value from the corresponding span element
      const value = $(this).siblings("span").data("value");
      // Remove the value from localStorage
      let currentItems = localStorage.getItem('item');
      if (currentItems) {
        // Remove the value and handle line breaks
        const newItems = currentItems.split("<br/>")
          .filter(item => item.trim() !== value.trim())
          .join("<br/>");
        // Update localStorage with new items
        localStorage.setItem('item', newItems);
        
        // Re-render the items to reflect changes in localStorage
        renderItems(newItems); // Pass the updated items to renderItems
      }
    });
  }
}

$(function () {
  $("#cboTemplate1").on("change", function () {
    const tmplID = $(this).val();
    $.ajax({
      url: "../src/auth/PostFile.php",
      type: "POST",
      data: { tmplID },
      success: function (result) {
        if (result) {
          // Set the selected template ID
          $("#id_tmp").val(tmplID);
          // Process the result and create span elements with buttons
          const formattedResult = result.split("\n").map(line => line.trim()).join("<br/>");
          localStorage.setItem('item', formattedResult);
          // Initial rendering of items
          const currentItems = localStorage.getItem('item');
          renderItems(currentItems);
        }
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  });
});

const SetTimeBLYAAAAAAAAAAT = async (event) => {
  let Fromdate = document.getElementById("Fromdate");
  let Fromtime = document.getElementById("Fromtime");
  let Todate = document.getElementById("Todate");
  let Totime = document.getElementById("Totime");

  const getTwoDigits = (value) => (value < 10 ? `0${value}` : value);
  const date = new Date();
  setTimeout(() => {
    Fromdate.value = Fromdateformat(date, event);
    Todate.value = Todateformat(date, event);
    Fromtime.value = fromTimeformat(date, event);
    Totime.value = ToTimeformat(date, event);
  }, 10);
  const lastDay = (date, day) => {
    return new Date(new Date(date.getTime() - day * 24 * 60 * 60 * 1000)).getDate().toString().padStart(2, "0");
  };
  const lastMonth = (date, day) => {
    return (
      new Date(
        new Date(date.getTime() - day * 24 * 60 * 60 * 1000)
      ).getMonth() + 1
    )
      .toString()
      .padStart(2, "0");
  };
  const Fromdateformat = (date, event) => {
    const datenow = getTwoDigits(date.getDate());
    const dateremove = getTwoDigits(date.getDate() - 1);
    const dayF = event.value == "curDate" 
        ? datenow
        : event.value == "last24Hour"
        ? dateremove
        : event.value == "prevDate"
        ? dateremove
        : event.value == "curMonth"
        ? getTwoDigits(1)
        : event.value == "last7Day"
        ? lastDay(date, 7)
        : event.value == "last30Day"
        ? lastDay(date, 30)
        : datenow;
    const monthF = event.value == "last7Day"
        ? lastMonth(date, 7)
        : event.value == "last30Day"
        ? lastMonth(date, 30)
        : getTwoDigits(date.getMonth() + 1);
    const yearF = monthF == 12 ? date.getFullYear() - 1 : date.getFullYear();
    return `${yearF}-${monthF}-${dayF}`;
  };
  const Todateformat = (date, event) => {
    const datenow = getTwoDigits(date.getDate());
    const dateremove = getTwoDigits(date.getDate() - 1);
    const dayT =
      event.value == "curDate" || event.value == "last24Hour"
        ? datenow
        : event.value == "prevDate"
        ? dateremove
        : datenow;
    const monthT = getTwoDigits(date.getMonth() + 1);
    const yearT = date.getFullYear();
    return `${yearT}-${monthT}-${dayT}`;
  };
  const fromTimeformat = (date, event) => {
    const HoursnowF = getTwoDigits(date.getHours());
    const HoursremoveF = getTwoDigits(date.getHours() - 1);
    const hoursF =
      event.value == "curDate" ||
      event.value == "prevDate" ||
      event.value == "curMonth" ||
      event.value == "last7Day"
        ? "00"
        : event.value == "last60Min"
        ? HoursremoveF
        : event.value == "last24Hour"
        ? HoursnowF
        : HoursnowF;
    const minsF =
      event.value == "curDate" ||
      event.value == "prevDate" ||
      event.value == "curMonth" ||
      event.value == "last7Day"
        ? "00"
        : getTwoDigits(date.getMinutes());
    return `${hoursF}:${minsF}`;
  };
  const ToTimeformat = (date, event) => {
    const HoursnowT = getTwoDigits(date.getHours());
    const hoursT =
      event.value == "curDate" ||
      event.value == "last60Min" ||
      event.value == "curMonth"
        ? HoursnowT
        : event.value == "prevDate"
        ? "23"
        : HoursnowT;
    const minsT =
      event.value == "prevDate" ? "00" : getTwoDigits(date.getMinutes());
    return `${hoursT}:${minsT}`;
  };
};
const SetTimeBLYAAAAAAAAAATGMDR = async (event) => {
  let Fromdate = document.getElementById("Fromdate-gmdr");
  let Fromtime = document.getElementById("Fromtime-gmdr");
  let Todate = document.getElementById("Todate-gmdr");
  let Totime = document.getElementById("Totime-gmdr");
  const getTwoDigits = (value) => (value < 10 ? `0${value}` : value);
  const date = new Date();
  setTimeout(() => {
    Fromdate.value = Fromdateformat(date, event);
    Todate.value = Todateformat(date, event);
    Fromtime.value = fromTimeformat(date, event);
    Totime.value = ToTimeformat(date, event);
  }, 10);
  const lastDay = (date, day) => {
    return new Date(new Date(date.getTime() - day * 24 * 60 * 60 * 1000))
      .getDate()
      .toString()
      .padStart(2, "0");
  };
  const lastMonth = (date, day) => {
    return (
      new Date(
        new Date(date.getTime() - day * 24 * 60 * 60 * 1000)
      ).getMonth() + 1
    )
      .toString()
      .padStart(2, "0");
  };
  const Fromdateformat = (date, event) => {
    const datenow = getTwoDigits(date.getDate());
    const dateremove = getTwoDigits(date.getDate() - 1);
    const dayF =
      event.value == "curDate"
        ? datenow
        : event.value == "last24Hour"
        ? dateremove
        : event.value == "prevDate"
        ? dateremove
        : event.value == "curMonth"
        ? getTwoDigits(1)
        : event.value == "last7Day"
        ? lastDay(date, 7)
        : event.value == "last30Day"
        ? lastDay(date, 30)
        : datenow;
    const monthF =
      event.value == "last7Day"
        ? lastMonth(date, 7)
        : event.value == "last30Day"
        ? lastMonth(date, 30)
        : getTwoDigits(date.getMonth() + 1);
    const yearF = monthF == 12 ? date.getFullYear() - 1 : date.getFullYear();
    return `${yearF}-${monthF}-${dayF}`;
  };
  const Todateformat = (date, event) => {
    const datenow = getTwoDigits(date.getDate());
    const dateremove = getTwoDigits(date.getDate() - 1);
    const dayT =
      event.value == "curDate" || event.value == "last24Hour"
        ? datenow
        : event.value == "prevDate"
        ? dateremove
        : datenow;
    const monthT = getTwoDigits(date.getMonth() + 1);
    const yearT = date.getFullYear();
    return `${yearT}-${monthT}-${dayT}`;
  };
  const fromTimeformat = (date, event) => {
    const HoursnowF = getTwoDigits(date.getHours());
    const HoursremoveF = getTwoDigits(date.getHours() - 1);
    const hoursF =
      event.value == "curDate" ||
      event.value == "prevDate" ||
      event.value == "curMonth" ||
      event.value == "last7Day"
        ? "00"
        : event.value == "last60Min"
        ? HoursremoveF
        : event.value == "last24Hour"
        ? HoursnowF
        : HoursnowF;
    const minsF =
      event.value == "curDate" ||
      event.value == "prevDate" ||
      event.value == "curMonth" ||
      event.value == "last7Day"
        ? "00"
        : getTwoDigits(date.getMinutes());
    return `${hoursF}:${minsF}`;
  };
  const ToTimeformat = (date, event) => {
    const HoursnowT = getTwoDigits(date.getHours());
    const hoursT =
      event.value == "curDate" ||
      event.value == "last60Min" ||
      event.value == "curMonth"
        ? HoursnowT
        : event.value == "prevDate"
        ? "23"
        : HoursnowT;
    const minsT =
      event.value == "prevDate" ? "00" : getTwoDigits(date.getMinutes());
    return `${hoursT}:${minsT}`;
  };
};
const loader = () => {
  const element = document.getElementById("loader-text");
  const changeText2 = () => {
    element.innerHTML = "PLEASE WAIT...";
  }
  const changeText1 = () => {
    element.innerHTML = "LOADING...";
  }
  $('#ScadamyModal').modal('hide');
  $('#GMDRmyModal').modal('hide');
  setTimeout(changeText1, 7000);
  setTimeout(changeText2, 13000);
  element.textContent = "FETCHING DATA...";
  document.getElementById("loading-p").style.display = "block";
} 
$(document).on("click", "#submitScada", function (event) {
    const Fromdate = document.getElementById("Fromdate");
    const Fromtime = document.getElementById("Fromtime");
    const Todate = document.getElementById("Todate");
    const Totime = document.getElementById("Totime");
    if(Fromdate.value || Fromtime.value || Todate.value || Totime.value !== '' ){loader();}
});
$(document).on("click", "#submitGmdr", function (event) {
    const Fromdate_gmdr = document.getElementById("Fromdate-gmdr");
    const Fromtime_gmdr = document.getElementById("Fromtime-gmdr");
    const Todate_gmdr = document.getElementById("Todate-gmdr");
    const Totime_gmdr = document.getElementById("Totime-gmdr");
    if(Fromdate_gmdr.value || Fromtime_gmdr.value || Todate_gmdr.value || Totime_gmdr.value !== '' ){loader();}
});
const loadtmp = () => {
  let timerInterval;
  Swal.fire({
    title: "Fetching Data..",
    html: "Please wait.. ",
    timer: 100000,
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading();
      const b = Swal.getHtmlContainer().querySelector("b");
      timerInterval = setInterval(() => {
        b.textContent = Swal.getTimerLeft();
      }, 500);
    },
    willClose: () => {
      clearInterval(timerInterval);
    },
  });
};
const DeleteTemplate = (e) => {
  Swal.fire({
    title: "Are you sure?",
    text: "Won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: { id: e },
        success: function (result) {
          Swal.fire({
            title: "Deleted!",
            text: "Template has been deleted.",
            icon: "success",
            showConfirmButton: false,
            timer: 2500,
          }).then((e) => {
            window.location.reload(true);
          });
        },
        error: function (error) {
          console.error(error);
        },
      });
    }
  });
};
const DeleteTag = (tagname, fc_name,table) => {
  Swal.fire({
    title: "Are you sure?",
    text: tagname + " won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: { tagname: tagname,fc_name:fc_name,table:table },
        success: function (result) {
          Swal.fire({
            title: "Deleted!",
            text: "Tag " + tagname + " has been deleted.",
            icon: "success",
            showConfirmButton: false,
            timer: 2500,
          }).then((e) => {
            window.location.reload(true);
          });
        },
        error: function (error) {
          console.error(error);
        },
      });
    }
  });
};
const DeleteMeter = (fc_name) => {
  Swal.fire({
    title: "Are you sure?",
    text: fc_name + " won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: { fc_name: fc_name },
        success: function (result) {
          Swal.fire({
            title: "Deleted!",
            text: "Meter " + fc_name + " has been deleted.",
            icon: "success",
            showConfirmButton: false,
            timer: 2500,
          }).then((e) => {
            window.location.reload(true);
          });
        },
        error: function (error) {
          console.error(error);
        },
      });
    }
  });
};
const DeleteUsers = (user, group) => {
  Swal.fire({
    title: "Are you sure?",
    text: "User " + user + " won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: { user: user,group:group },
        success: function (result) {
          Swal.fire({
            title: "Deleted!",
            text: "User " + user + " has been deleted.",
            icon: "success",
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
    }
  });
};

const AddTagGMDR = (Meter) => {
  const table = document.getElementById("tbname").value;
  Swal.fire({
    title: "Add TAG",
    html: `
          <div class='form-group'>
              <h5 class='text-edit'>Meter name : </h5>
              <input type='text' id='meter_name' class='form-control' value=`+ Meter +` disabled> 
              <h5 class='text-edit'>Tag name : </h5>
              <input type='text' id='tag_name' class='form-control'>
              <h5 class='text-edit'>Tag header :</h5>
              <input type='text' id='tag_header' class='form-control'>
              <h5 class='text-edit'>Desription :</h5>
              <input type='text' id='desription' class='form-control'>
              <div class='row'>
                  <div class='col-sm-6'>
                      <h5 class='text-edit'>Precision :</h5>
                      <input type='number' class='form-control' id='Precision'>
                  </div>
                  <div class='col-sm-6'>
                      <h5 class='text-edit'>Sort order :</h5>
                      <input type='number' class='form-control' id='Sort_order'>
                  </div>
              </div><BR>
              <div class='form-check'>
                  <label class='form-check-label'>Check meter : </label>
                  <input class='form-check-input' type='checkbox' id='check_meter' name='check_meter'>
              </div>
          </div>
          </div>`,
    inputAttributes: {
      autocapitalize: "off",
    },
    showCancelButton: true,
    confirmButtonColor: "#5cb85c",
    confirmButtonText: "Save",
    preConfirm: () => {
      const meter_name = Swal.getPopup().querySelector("#meter_name").value;
      const tag_name = Swal.getPopup().querySelector("#tag_name").value;
      const tag_header = Swal.getPopup().querySelector("#tag_header").value;
      const desription = Swal.getPopup().querySelector("#desription").value;
      const Precision = Swal.getPopup().querySelector("#Precision").value;
      const Sort_order = Swal.getPopup().querySelector("#Sort_order").value;
      const check_meter = Swal.getPopup().querySelector("#check_meter");
      return {
        meter_name: meter_name,
        tag_name: tag_name,
        tag_header: tag_header,
        desription: desription,
        Precision: Precision,
        Sort_order: Sort_order,
        check_meter: check_meter
      };
    },
  }).then((result) => {
    const Tag = result.value.tag_name;
    if (
      result.value.tag_name == "" ||
      result.value.tag_header == "" ||
      result.value.desription == "" ||
      result.value.Precision == "" ||
      result.value.Sort_order == ""
    ) {
      Swal.fire({
        title: "Warning!",
        text: "Please fill in complete information.",
        icon: "warning",
        showConfirmButton: false,
        timer: 2500,
      });
    } else {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: {
          meter_name: result.value.meter_name,
          tag_name: result.value.tag_name,
          tag_header: result.value.tag_header,
          desription: result.value.desription,
          Precision: result.value.Precision,
          Sort_order: result.value.Sort_order,
          check_meter: result.value.check_meter.checked,
          table:table
        },
        success: function (result) {
          Swal.fire({
            title: "Success",
            text: "Tag " + Tag + " has been added successfully.",
            icon: "success",
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
    }
  });
};

const AddMeter = () => {
  Swal.fire({
    title: "Add Meter",
    html: `
    <div class='form-group'>
        <h5 class='text-edit'>Meter name : </h5>
        <input type='text' id='meter_name' class='form-control'>
        <h5 class='text-edit'>Description : </h5>
        <input type='text' id='desription' class='form-control'>
        <h5 class='text-edit'>Maintenance Tag :</h5>
        <input type='text' id='maintenance' class='form-control' ><BR>
        <div class='form-check'>
            <label class='form-check-label'>DB Time : </label> 
            <input class='form-check-input' name='check_db' id='check_db' type='checkbox'>&nbsp&nbsp
            <label class='form-check-label'>FC Time : </label> 
            <input class='form-check-input' name='ckeck_flag' id='ckeck_flag' type='checkbox'>&nbsp&nbsp
            <label class='form-check-label'>RTU Time : </label> 
            <input class='form-check-input' name='check_rtu' id='check_rtu' type='checkbox'>
        </div>
    </div>
    </div>`,
    inputAttributes: {
      autocapitalize: "off",
    },
    showCancelButton: true,
    confirmButtonColor: "#5cb85c",
    confirmButtonText: "Save",
    preConfirm: () => {
      const meter_name = Swal.getPopup().querySelector("#meter_name").value;
      const description = Swal.getPopup().querySelector("#desription").value;
      const maintenance = Swal.getPopup().querySelector("#maintenance").value;
      const check_db = Swal.getPopup().querySelector("#check_db");
      const check_flag = Swal.getPopup().querySelector("#ckeck_flag");
      const check_rtu = Swal.getPopup().querySelector("#check_rtu");
      return {
        meter_name: meter_name,
        description: description,
        maintenance: maintenance,
        check_db: check_db,
        check_flag: check_flag,
        check_rtu: check_rtu
      };
    },
  }).then((result) => {
    if (
      result.value.meter_name == "" ||
      result.value.description == "" 
    ) {
      Swal.fire({
        title: "Warning!",
        text: "Please fill in complete information.",
        icon: "warning",
        showConfirmButton: false,
        timer: 2500,
      });
    } else {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: {
          meter_name: result.value.meter_name,
          description: result.value.description,
          maintenance: result.value.maintenance,
          check_db: result.value.check_db.checked,
          check_flag: result.value.check_flag.checked,
          check_rtu: result.value.check_rtu.checked
        },
        success: function (result) {
          Swal.fire({
            title: "Success",
            text: "Meter has been added successfully.",
            icon: "success",
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
    }
  });
};

const Addusers = () => {
  Swal.fire({
    title: "Add Users",
    html: `
    <div class='form-group'>
        <h5 class='text-edit'>User Name : </h5>
        <input type='text' id='Username' name='Username' class='form-control'>
        <h5 class='text-edit'>Group : </h5>
        <select class='form-control' id='groupid' requied>
            <option value= '' selected>--Select Group--</option>
            <option value='1'>PTT Users</option>
            <option value='2'>PTT Admins</option>
            <option value='3'>SCADA History Users</option>
            <option value='4'>SCADA History Admins</option>
            <option value='5'>GMDR Users</option>
            <option value='6'>GMDR Admins</option>
        </select>
        
    </div>
    </div>`,
    inputAttributes: {
      autocapitalize: "off",
    },
    showCancelButton: true,
    confirmButtonColor: "#5cb85c",
    confirmButtonText: "Save",
    preConfirm: () => {
      const Username = Swal.getPopup().querySelector("#Username").value;
      const groupid = Swal.getPopup().querySelector("#groupid").value;
      return {
        Username: Username,
        groupid:groupid
      };
    },
  }).then((result) => {
    if (result.value.Username == "" || result.value.groupid == "") {
      Swal.fire({
        title: "Warning!",
        text: "Please fill in complete information.",
        icon: "warning",
        showConfirmButton: false,
        timer: 2500,
      });
    } else {
      $.ajax({
        url: "../src/auth/PostFile.php",
        type: "post",
        data: {
          Username: result.value.Username,
          groupid: result.value.groupid
        },     
        success: function (result) {
          Swal.fire({
            title: "Success",
            text: "Users has been added successfully.",
            icon: "success",
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
    }
  });
};

const SearchTAG = () => {
  const RTU = document.getElementById("cboTemplate_RTU").value;
  const sh_tag = document.getElementsByName("sh_tag");
  const Time = document.getElementById("TimeSelect");
  if(sh_tag[0].value.trim().length === 1){
  Swal.fire({
      title: "Warning?",
      text: "Minimum two characters!",
      icon: "warning",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Ok"
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "";
      }
    });
  }else{
    window.location.href = "ShowTag.php?RTU=" + RTU + "&Time=" + Time.textContent + "&shtag=" + sh_tag[0].value;
  }
 }

//check select tag
$(document).ready(function() {
    $('#CurrentTag').mouseup(function(event) { 
        const Cur = document.getElementById("CurrentTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input1').each(function() {this.checked = true;}) : 
          $('.form-check-input1').each(function() {this.checked = false;});
        }else{
          $('.form-check-input1').each(function() {this.checked = false;});
        }
    });
    $('#AverageTag').mouseup(function(event) { 
        const Cur = document.getElementById("AverageTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input2').each(function() {this.checked = true;}) : 
          $('.form-check-input2').each(function() {this.checked = false;});
        }else{
          $('.form-check-input2').each(function() {this.checked = false;});
        }
    });
    $('#MinTag').mouseup(function(event) { 
        const Cur = document.getElementById("MinTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input3').each(function() {this.checked = true;}) : 
          $('.form-check-input3').each(function() {this.checked = false;});
        }else{
          $('.form-check-input3').each(function() {this.checked = false;});
        }
    });
    $('#MaxTag').mouseup(function(event) { 
        const Cur = document.getElementById("MaxTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input4').each(function() {this.checked = true;}) : 
          $('.form-check-input4').each(function() {this.checked = false;});
        }else{
          $('.form-check-input4').each(function() {this.checked = false;});
        }
    });
    $('#DiffIndexTag').mouseup(function(event) { 
        const Cur = document.getElementById("DiffIndexTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input5').each(function() {this.checked = true;}) : 
          $('.form-check-input5').each(function() {this.checked = false;});
        }else{
          $('.form-check-input5').each(function() {this.checked = false;});
        }
    });
    $('#NotusedTag').mouseup(function(event) { 
        const Cur = document.getElementById("NotusedTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input6').each(function() {this.checked = true;}) : 
          $('.form-check-input6').each(function() {this.checked = false;});
        }else{
          $('.form-check-input6').each(function() {this.checked = false;});
        }
    });
    $('#IntegratedTag').mouseup(function(event) { 
        const Cur = document.getElementById("IntegratedTag");
        if(Cur.checked === false){
        document.activeElement.tagName === 'INPUT' ? 
          $('.form-check-input7').each(function() {this.checked = true;}) : 
          $('.form-check-input7').each(function() {this.checked = false;});
        }else{
          $('.form-check-input7').each(function() {this.checked = false;});
        }
    });
});

$(document).ready(function() {
    let currentItem = localStorage.getItem('item');
    let tmp_id = localStorage.getItem('tmp_id');
    if(currentItem && tmp_id){
        const tmpElement = document.getElementById("id_tmp");
        if (tmpElement) {
            tmpElement.value = tmp_id;
            renderItems(currentItem);
        } else {
            console.warn("Element with id 'id_tmp' not found.");
        }
      
    }
});

const ChangeTagnodeList = (arr,Time) => {
  const nl2br = (str) => {
    return str.replace(/<br\s*\/?>/gi, "\n");
  }
  displayTimeSelect(Time);
  const T = Time.split("-");
  document.getElementById("period").value = T[1];
  const text = document.getElementById('message-text');
    let currentItem = localStorage.getItem('item');
    // If there's no value yet, initialize it as "1". Otherwise, append the next value.
    if (!currentItem) {
      currentItem = arr;
    } else {
      currentItem += arr;
    }
    // Set the updated value back to localStorage
    localStorage.setItem('item', currentItem.replace(/<br\s*\/?>/g, '<br/>'));
    text.classList.add('my-hover');  
    renderItems(currentItem)
    document.getElementById("TimeSelect").innerHTML = Time;
    document.getElementById("cboTemplate1").setAttribute("disabled", true);
    document.getElementById("addtag").value = "Add";
    $("#message-text-hidden").val(currentItem);
    $('#ScadamyModal').modal('show');
}

const SetText = async (arr,time) => {

  
const filteredData = Object.fromEntries(
    Object.entries(arr).map(([tag, items]) => [tag,Object.fromEntries(
                Object.entries(items).filter(([key, value]) => value === "Checked")
            )
        ]).filter(([, filteredItems]) => Object.keys(filteredItems).length > 0) // Exclude empty objects
    );
    window.location.href = "Index.php?SetArray=" + encodeURIComponent(JSON.stringify(filteredData)) + "&Time=" + time;
}

const PushArrKey = async (e,time) => {
  const obj = {};
  e.forEach(function (nodeListKey) {
    nodeListKey.forEach(function (elementKey) {
      obj[elementKey.name] = {};
    });
  });
  e.forEach(function (nodeList) {
    nodeList.forEach(function (element) {
        if (element.name === "CurrentTag") {
          if(element.checked == true){
              obj["CurrentTag"][element.value] = "Checked";
            }else{
              obj["CurrentTag"][element.value] = "Not Checked";
          }
        }
        if (element.name === "AverageTag") {
          if(element.checked == true){
              obj["AverageTag"][element.value] = "Checked";
            }else{
              obj["AverageTag"][element.value] = "Not Checked";
          }
        }
        if (element.name === "MinTag") {
          if(element.checked == true){
              obj["MinTag"][element.value] = "Checked";
            }else{
              obj["MinTag"][element.value] = "Not Checked";
          }
        }
        if (element.name === "MaxTag") {
          if(element.checked == true){
              obj["MaxTag"][element.value] = "Checked";
            }else{
              obj["MaxTag"][element.value] = "Not Checked";
          }
        }
        if (element.name === "DiffIndexTag") {
          if(element.checked == true){
              obj["DiffIndexTag"][element.value] = "Checked";
            }else{
              obj["DiffIndexTag"][element.value] = "Not Checked";
          }
        }
        if (element.name === "NotusedTag") {
          if(element.checked == true){
              obj["NotusedTag"][element.value] = "Checked";
            }else{
              obj["NotusedTag"][element.value] = "Not Checked";
          }
        }
        if (element.name === "IntegratedTag") {
          if(element.checked == true){
              obj["IntegratedTag"][element.value] = "Checked";
            }else{
              obj["IntegratedTag"][element.value] = "Not Checked";
          }
        }
    });
  });
  return SetText(obj,time);
};
const CountCheck = (arr) => {
  let checkedCount = 0;
  arr.forEach(function (node) {
    node.forEach(function (element) {if(element.checked){checkedCount++;}});
  });
  return checkedCount;
}

const SubmitAddTag = async () => {
  const url = window.location.href;
  const regex = /Time=([^&]+)/;
  const match = url.match(regex);
  const time = match ? match[1] : null;
  const CurrentTagnodeList = document.getElementsByName("CurrentTag");
  const AverageTagnodeList = document.getElementsByName("AverageTag");
  const MinTagnodeList = document.getElementsByName("MinTag");
  const MaxTagnodeList = document.getElementsByName("MaxTag");
  const DiffIndexTagnodeList = document.getElementsByName("DiffIndexTag");
  const NotusedTagnodeList = document.getElementsByName("NotusedTag");
  const IntegratedTagnodeList = document.getElementsByName("IntegratedTag");
  if(CountCheck([CurrentTagnodeList, AverageTagnodeList, 
    MinTagnodeList, MaxTagnodeList, DiffIndexTagnodeList, 
    NotusedTagnodeList, IntegratedTagnodeList]) > 100)
    {
     Swal.fire({title: "Warning!",text: "Maximum 100 tag!",icon: "warning",showConfirmButton: false,timer: 3500,})
    }else{
      return PushArrKey([
        CurrentTagnodeList, AverageTagnodeList, 
        MinTagnodeList, MaxTagnodeList, DiffIndexTagnodeList, 
        NotusedTagnodeList, IntegratedTagnodeList
      ],time);
    }
};

const OpenModalScada = (e) => {
const Label = document.getElementById("Maxdays");
  if(e == '1 Minute'){
    Label.textContent = "Maximum duration 3 days*";
  }else{
    Label.textContent = "Maximum duration 35 days*";
  }
  document.getElementById("TimeSelect").innerHTML = "Archive - " + e;
  document.getElementById("period").value = e;
  ChangeTimeSelectScada(e);
  // Initialize the Bootstrap modal
  
    $("#ScadamyModal").modal("show");
};


const displayTimeSelect = (Time) => {
  const today = document.getElementById("today");
  const l60min = document.getElementById("l60min");
  const l24hr = document.getElementById("l24hr");
  const Yes = document.getElementById("Yes");
  const day = document.getElementById("day");
  switch (Time) {
    case "Archive - 1 Minute":
      today.style.display = "block";
      l60min.style.display = "block";
      l24hr.style.display = "none";
      Yes.style.display = "none";
      day.style.display = "none";
      break;
    case "Archive - 10 Minute":
      today.style.display = "block";
      l60min.style.display = "block";
      l24hr.style.display = "block";
      Yes.style.display = "none";
      day.style.display = "none";
      break;
    case "Archive - Hour":
      today.style.display = "block";
      l60min.style.display = "none";
      l24hr.style.display = "block";
      Yes.style.display = "block";
      day.style.display = "none";
      break;
    case "Archive - Day":
      today.style.display = "none";
      l60min.style.display = "none";
      l24hr.style.display = "none";
      Yes.style.display = "none";
      day.style.display = "block";
      break;
  }
}
const SaveTemplate = async () => {
  $("#ScadamyModal").modal("hide");
  $("#NewTemplate").modal("show");
  //$("#ScadamyModal").modal("hide");
  const tag = document.getElementById('message-text');
  const tag1 = localStorage.getItem('item');
  document.getElementById('selected_tag').value = tag1.replace(/<br\s*\/?>/g, '\n');
}

const handleClickClose = () => {
  $('#NewTemplate').modal('hide');
}

const CancleAddTag = () => {
  const url = window.location.href;
  const regex = /Time=([^&]+)/;
  const match = url.match(regex);
  const time = match ? match[1] : null;
  const t = time.replace(/%20/g, ' ');
  const timetext = t.split('- ');
  window.location.href = "Index.php?Timeopen="+timetext[1];
} 
const submitScada = () => {
  const TotimeInput = document.getElementById("Totime");
  const FromtimeInput = document.getElementById("Fromtime");
  if (TotimeInput.value !== "" && FromtimeInput.value !== "") {
    document.getElementById("fromScada").submit();
    //localStorage.removeItem('item');
    //localStorage.removeItem('tmp_id');
    //localStorage.removeItem('tag_name');
  } 
}
const submitGmdr = () => {
  const TotimeInputGmdr = document.getElementById("Totime-gmdr");
  const FromtimeInputGmdr = document.getElementById("Fromtime-gmdr");
  if (TotimeInputGmdr.value !== "" && FromtimeInputGmdr.value !== "") {
    document.getElementById("fromGmdr").submit();
  } 
}

const PrintData = () => {
  event.preventDefault(); // Prevent the default action
  const table = document.getElementById("example"); // Get the table element
  const newWindow = window.open("", "_blank"); // Open a new window
  newWindow.document.write(`
    <html>
      <head>
        <title>Print Table</title>
        <style>
          /* Add some styling for printing */
          table {
            width: 100%;
            border-collapse: collapse;
          }
          table, th, td {
            border: 1px solid black;
          }
          th, td {
            padding: 8px;
            text-align: left;
          }
        </style>
      </head>
      <body>
        ${table.outerHTML} <!-- Add the table content -->
      </body>
    </html>
  `);
  newWindow.document.close(); // Close the document to finish loading
  newWindow.print(); // Trigger the print dialog
  newWindow.close(); // Close the new window after printing
};

const exportTableToExcel = (tableID, tagname) => {
  const table = document.getElementById(tableID); // Get the table element
  const rows = Array.from(table.rows); // Convert table rows to an array

  // Extract data from the table
  const data = rows.map(row =>
    Array.from(row.cells).map(cell => cell.innerText)
  );

  // Create a workbook and worksheet
  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.aoa_to_sheet(data); // Convert the table data to a worksheet

  // Append the worksheet to the workbook
  XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

  // Generate the filename using tagname
  const finalFilename = `${tagname}.xlsx`;

  // Write the workbook and trigger the download
  XLSX.writeFile(wb, finalFilename);
};

const exportTableToCSV = (tableID, filename) => {
  const table = document.getElementById(tableID);
  const rows = Array.from(table.rows);
  
  // Create CSV content
  const csvContent = rows.map(row => {
    const cells = Array.from(row.cells);
    return cells.map(cell => `"${cell.innerText}"`).join(","); // Escape values with quotes
  }).join("\n");

  // Create a Blob object
  const blob = new Blob([csvContent], { type: 'text/csv' });

  // Create a download link
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = filename; // Set the file name
  document.body.appendChild(link);
  link.click(); // Trigger the download
  document.body.removeChild(link); // Remove the link after download
};


const filterTable = () => {
  const input = document.getElementById("searchInput");
  const filter = input.value.toLowerCase();
  const table = document.getElementById("example");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
    const cells = rows[i].getElementsByTagName("td");
    let rowContainsFilter = false;

    for (let j = 0; j < cells.length; j++) {
      const cellText = cells[j].textContent || cells[j].innerText;
      if (cellText.toLowerCase().indexOf(filter) > -1) {
        rowContainsFilter = true;
        break;
      }
    }

    rows[i].style.display = rowContainsFilter ? "" : "none";
  }
}


const Template = (id,text) => {
  const tmplID = id;
  // Show the modal
  $('#ScadamyModal').modal('show');
  // Get the dropdown element
  const selectElement = document.getElementById('cboTemplate1');
  if (selectElement) {
      // Convert id to string
      const normalizedId = id.toString();
      // Check if the option exists
      const optionExists = [...selectElement.options].some(option => option.value === normalizedId);
      if (optionExists) {
          // Set the selected value for the select element
          document.getElementById("TimeSelect").innerHTML = "Archive - " + "Hour";
          document.getElementById("period").value = "Hour";
          ChangeTimeSelectScada("Hour");
          const currentElement = document.querySelector('span.current');
          currentElement.textContent = text;
          const selectElement = document.getElementById('cboTemplate1');
          selectElement.value = normalizedId;
          document.getElementById("period").value = 'Hour';
          document.getElementById("tagnamescada").value = text;
          document.getElementById("addtag").value = '';
          localStorage.setItem('tmp_id', normalizedId); // Fixed
          $.ajax({
            url: "../src/auth/PostFile.php",
            type: "POST",
            data: { tmplID },
            success: function (result) {
              if (result) {
                // Set the selected template ID
                $("#id_tmp").val(tmplID);
                // Process the result and create span elements with buttons
                const formattedResult = result.split("\n").map(line => line.trim()).join("<br/>");
                localStorage.setItem('item', formattedResult);
                // Initial rendering of items
                const currentItems = localStorage.getItem('item');
                renderItems(currentItems);
              }
            },
            error: function (error) {
              console.error("Error:", error);
            },
          });
          // Update the corresponding <li> element
          const optionListItems = document.querySelectorAll('.option-scada');
          optionListItems.forEach(li => {
              // Remove "selected" class from all <li> elements
              li.classList.remove('selected');

              // Add "selected" class to the matching <li>
              if (li.getAttribute('data-value') === normalizedId) {
                  li.classList.add('selected');
              }
          });
      } else {
          console.error(`Option with id '${normalizedId}' does not exist in the dropdown.`);
      }
  } else {
      console.error("Dropdown element not found!");
  }
};




$(document).ready(function () {
  const currentElement = document.querySelector('span.current');
  if(currentElement){
    document.getElementById("tagnamescada").value =currentElement.textContent;
  }
});

// JavaScript for filtering the list
const filterTemplate = () => {
    const valueFilter = document.getElementById("searchInput");
    const filter = valueFilter.value.toLowerCase();
    const listItems = document.querySelectorAll("#templateList li");
    listItems.forEach((item) => {
        const templateText = item.getAttribute("data-template").toLowerCase();
        item.style.display = templateText.includes(filter) ? "" : "none";
    });
};
















