$(document).ready(function () {
  const $acceptCookiesBtn = $("#acceptCookies");
  const cookiePolicyModal = new bootstrap.Modal($("#cookiePolicyModal")[0]);

  if (!getCookie("cookies_accepted")) {
    cookiePolicyModal.show();
  }

  $acceptCookiesBtn.on("click", function () {
    setCookie("cookies_accepted", "true", 365);
    cookiePolicyModal.hide();
  });

  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    const expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
  }

  function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i].trim();
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  // call ajax action with verb delete  on form with call action-delete
  $(".action-delete").on("submit", function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr("action");
    $.ajax({
      type: "DELETE",
      url: url,
      data: form.serialize(),
      success: function (data) {
        if (data.success) {
          window.location = data.redirect_url;
        } else {
          window.location.reload();
        }
      },
    });
  });
});
