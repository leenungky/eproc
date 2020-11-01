<script>
require(["bootstrap-fileinput"], function () {
    let buttonFooterLeft = `
        <div class="btn-group btn-pages mr-auto">
            <button id="{{$formName}}-previous" type="button" class="btn btn-sm btn-outline-secondary text-center" 
                style="width: 110px;" disabled><i class="fas fa-angle-double-left mr-2"></i>{{ __('homepage.previous') }}
            </button>
            <button id="{{$formName}}-next" type="button" class="btn btn-sm btn-outline-secondary text-center" 
                style="width: 110px;">{{ __('homepage.next') }}<i class="fas fa-angle-double-right ml-2"></i>
            </button>
        </div>`; 
    $('#{{$formName}} .modal-footer').prepend(buttonFooterLeft);

    $("#{{$formName}}-previous").click(function(){
        $("#{{$formName}}-previous").prop("disabled", true);
        $("#{{$formName}}-next").prop("disabled", false);
        $(".page1").removeClass("display-none");
        $(".page1").addClass("display-block");
        $(".page2").removeClass("display-block");
        $(".page2").addClass("display-none");
    });

    $("#{{$formName}}-next").click(function(){
        $("#{{$formName}}-previous").prop("disabled", false);
        $("#{{$formName}}-next").prop("disabled", true);
        $(".page1").removeClass("display-block");
        $(".page1").addClass("display-none");
        $(".page2").removeClass("display-none");
        $(".page2").addClass("display-block");
    });
});    
</script>