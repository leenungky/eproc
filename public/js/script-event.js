window.addEventListener("resize", function () {
    if (window.innerWidth < 992) {
        $('.card-menu .heading-left a.text-logo').hide();
        $('.card-menu .heading-left a.ico-logo').show();
    } else {
        $('.card-menu .heading-left a.text-logo').show();
        $('.card-menu .heading-left a.ico-logo').hide();
    }
    if (document.body.clientWidth < 1250) {
        $(".app-container").addClass("closed-sidebar-mobile closed-sidebar");
    } else {
        $(".app-container").removeClass("closed-sidebar-mobile closed-sidebar");
    }
});
window.addEventListener('load', function () {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            // if (form.checkValidity() === false) {
            //   event.preventDefault();
            //   event.stopPropagation();
            // }
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }, false);
    });

    // $('.date, .datetime').attr('autocomplete', 'off');
    var dt = document.getElementsByClassName('date');
    var i;
    for (i = 0; i < dt.length; i++) {
        dt[i].setAttribute('autocomplete', 'off');
    }
    var dtime = document.getElementsByClassName('datetime');
    for (i = 0; i < dtime.length; i++) {
        dtime[i].setAttribute('autocomplete', 'off');
    }
    var hleft = document.getElementsByClassName('heading-left');
    if (hleft.length > 0) {
        var xlogo = document.createElement('div');
        xlogo.className = "xlogo";
        xlogo.addEventListener('click', function (e) {
            e.preventDefault();
            var cm = document.querySelectorAll('.card.card-menu');
            if (cm.length > 0) {
                cm[0].classList.toggle('show');
            }
        });
        hleft[0].parentNode.appendChild(xlogo);
    }
}, false);
