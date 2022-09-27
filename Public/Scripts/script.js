"use strict";

jQuery(function ($) {
  var $body = $("body");

  function setTrumbowyg() {
    $.trumbowyg.svgPath = "/node_modules/trumbowyg/dist/ui/icons.svg"; // Textarea editor

    $(".to_trumbowyg").trumbowyg({
      removeformatPasted: true,
      lang: "fr",
      btns: [['undo', 'redo'], // Marche pas sur Edge / IE
      ['formatting'], ['strong', 'em', 'del', 'underline'], ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'], ['unorderedList', 'orderedList'], ['removeformat']]
    });
  }

  setTrumbowyg(); // ========== ToggleClass

  $(document).on("click", ":attrStartsWith(data-togclass)", function (event) {
    if (event) event.preventDefault();
    var classvalue = null;
    var target = null;
    $.each(this.attributes, function () {
      if (this.name.includes("data-togclass")) {
        classvalue = this.name.split("-")[2];
        target = this.value;
        $("#" + target).toggleClass(classvalue);
      }
    });
  }); //  ========= Allow to change some attribute from an element to another

  $(document).on("click", ":attrStartsWith(data-change)", function (event) {
    if (event) event.preventDefault();
    var data_change_attr = null;
    var data_value = null;
    $.each(this.attributes, function () {
      if (this.name.includes("data-change")) {
        data_change_attr = this.name.replace("data-change-", "");
        data_value = this.value;
      }
    }); //console.log(data_change_attr);

    if (!empty(data_value)) {
      var $target = $("#" + $(this).attr("data-ch-target")); //console.log($target);

      if (!empty($target)) {
        $target.attr(data_change_attr, data_value);
      }
    }
  }); // ======== Slide down animation

  var $data_slide_down = $("[data-slide-down]");
  $data_slide_down.each(function () {
    var $el = $(this);
    var data_slide = $el.attr("data-slide-down");
    var $target = $("#" + data_slide);

    if ($target.hasClass("open")) {
      $target.css("height", $target.children().outerHeight() + "px");
    }

    $(this).on("click", function (event) {
      if (event) event.preventDefault();
      var $el = $(event.target);
      var data_slide = $el.attr("data-slide-down");
      var $target = $("#" + data_slide);

      if ($target.hasClass("open")) {
        $target.css("height", $target.children().outerHeight() + "px");
      }

      if ($target.length > 0) {
        var height = $target.children().outerHeight();

        if ($target.outerHeight() > 0) {
          $el.removeClass("active");
          $target.css("height", "0px");
        } else {
          $el.addClass("active");
          $target.css("height", height + "px");
        }
      }
    });
  }); // ======= Sort ajax system

  $body.on("click", "[data-col-sort]", function (event) {
    if (event) event.preventDefault();
    $body.addClass("ovh-x");
    var $ajaxContainer = $("#" + $(this).attr("data-col-ajax"));
    $ajaxContainer.removeClass("after_load");
    $ajaxContainer.addClass("before_load");
    var col = $(this).attr("data-col-sort");
    var order = $(this).attr("data-col-order");

    if (order === "ASC") {
      $(this).attr("data-col-order", "DESC");
    } else {
      $(this).attr("data-col-order", "ASC");
    }

    var data = {
      BuildSorts: {}
    };
    data.BuildSorts[col] = order;
    $("[data-col-sort]").each(function () {
      if ($(this).attr("data-col-sort") === col) return true;
      var other_col = $(this).attr("data-col-sort");
      var other_order = $(this).attr("data-col-order");
      if (other_order === "ASC") other_order = "DESC";else other_order = "ASC";
      data.BuildSorts[other_col] = other_order;
    });
    var url = $(this).attr("data-col-url");
    var callback = $(this).attr("data-col-callback");
    var ajax_options = {
      type: "GET",
      url: url,
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: data,
      success: function success(data) {
        window[callback](data);
      }
    };
    $.ajax(ajax_options);
  }); // ===== NOTIFICATION System

  $body.on("click", ".close_notif", function () {
    //console.log($(this));
    $(this).closest(".notif").removeClass("active");
    setTimeout(function () {
      $(this).closest(".notif").removeClass("success");
      $(this).closest(".notif").removeClass("error");
    }.bind(this), 401);
  }); // ====== Commentaires

  /**
   * TODO : Mettre système commentaire dans fichier js séparé pour réutilisation
   */

  $(".answer_com_button").each(function () {
    $(this).click(function (event) {
      if (event) event.preventDefault();
      $(this).after($("#AddComm").detach());
      $("#AddComm").removeClass("d_none");
      $("#ComId").val($(this).attr("data-id"));
    });
  });
  $("#WriteComButton").click(function (event) {
    if (event) event.preventDefault();
    $(this).parent().after($("#AddComm").detach());
    $("#AddComm").removeClass("d_none");
    $("#ComId").attr("value", "");
  });
  $body.on("click", ".see_answer_btn", function () {
    $(this).remove();
  }); // =========== Copy system

  $body.on("click", "[data-copy]", function () {
    var $elem = $(this);
    var $target = $("#" + $elem.attr("data-copy"));
    $target.select();
    document.execCommand("copy");
    var $hidden_message = $elem.siblings(".hidden_message");

    if (!empty($hidden_message)) {
      $hidden_message.addClass("active");
      setTimeout(function () {
        $hidden_message.removeClass("active");
      }, 2000);
    }
  }); // ========== Menu style quand scroll

  $(window).scroll(function () {
    if ($(window).scrollTop() <= 50) {
      $("#TopMenu").removeClass("moved");
    } else {
      $("#TopMenu").addClass("moved");
    }
  }); // ========== Parallax system

  $(window).scroll(function () {
    var topDistance = window.pageYOffset;
    var $layers = $("[data-type=parallax]");
    $layers.each(function () {
      var depth = $(this).attr('data-depth');
      var movement = -(topDistance * depth);
      var translate3d = 'translate3d(0, ' + movement + 'px, 0)';
      $(this).css("-webkit-transform", translate3d);
      $(this).css("-moz-transform", translate3d);
      $(this).css("-ms-transform", translate3d);
      $(this).css("-webkit-transform", translate3d);
      $(this).css("-o-transform", translate3d);
    });
  }); // =========== Menu Responsive Burger

  $("#BurgerMenu").click(function (event) {
    //console.log("works");
    if (event) event.preventDefault();
    $("#nav-icon3").toggleClass("open");
    $("#PublicMenu").toggleClass("open");
  });
}); // ===== NOTIF Function =====

function addNotifMessage(message, type) {
  if (!empty(timeout)) {
    clearTimeout(timeout);
  }

  var $notif = $(".notif");
  $notif.addClass(type);
  var $para = $notif.find("p");
  $para.empty();
  $para.append(message);
  $notif.addClass("active");
  timeout = setTimeout(function () {
    $notif.removeClass("active");
    setTimeout(function () {
      $notif.removeClass("success");
      $notif.removeClass("error");
    }, 401);
  }, 6000);
}
//# sourceMappingURL=script.js.map