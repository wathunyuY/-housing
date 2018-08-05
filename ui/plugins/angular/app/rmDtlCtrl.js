app.controller('rmDtlCtrl', function($rootScope,$http,$scope,$route,$filter) {
    var params = $route.current.params;
    $scope.persons = [];
    $scope.origin_address = null;
    $scope.districts= json_districts;
    $scope.is_header_family = false;


    $http.get($rootScope.apiUrl+"/home/roomDetail?room_id="+params.id)
    .then(function(response) {
        $scope.roomDetail = response.data.data;
        if($scope.roomDetail.find){
            var family = $scope.roomDetail.family;
            $scope.family_name = family.FAMILY_NAME;
            $scope.members = family.MEMBERS;
            $scope.headFam = family.PERSON;
            $scope.headFamCur = family.PERSON.CURRENT;
            $scope.fullname = $scope.headFamCur.FIRST_NAME;
            $scope.idCard = $scope.headFamCur.PERS_N_ID;
            $scope.national = $scope.headFamCur.NATIONALITY;
            $scope.edu = $scope.headFamCur.EDUCATION;
            $scope.career = $scope.headFamCur.CAREER;
            $scope.academy = $scope.headFamCur.ACADEMY;
            $scope.mobile = $scope.headFamCur.MOBILE_NBR_1;
            $scope.phone = $scope.headFamCur.PHONE_NBR;
            $scope.origin_address_descr = $scope.headFamCur.ADDRESS_1_TYPE0;
            $scope.origin_address = $scope.headFamCur.DISTRICT_ID_TYPE0;
            $scope.car = $scope.headFamCur.CAR_NUMBER;
            $scope.biker = $scope.headFamCur.BIKER_NUMBER;
            $scope.reference = $scope.headFamCur.REFERENCE;
            $scope.gender = $scope.headFamCur.GENDER ;
            $scope.birth_date = $scope.headFam.BIRTHDAY;
            $scope.start_date = family.start_date;
            $scope.relation = $rootScope.HEAD_FAMILY_TEXT;
            $('#datepicker').datepicker("setDate",new Date($scope.headFam.BIRTHDAY));
            $('#datepicker2').datepicker("setDate",new Date(family.start_date));
            if($scope.members.length > 0){
                
            }
        }
    });


    console.log(params);
    // $("#demoTabs").tabs();
    // $("#removeTabs").click(function() {
    //     var tabIndex = parseInt($("#indexValue").val(), 10);
    //     var tab = $( "#demoTabs" ).find(".ui-tabs-nav li:eq(" + tabIndex + ")").remove();
    //     $("#demoTabs").tabs("refresh");
    // });
    // $("#addTabs").click(function() {
    //     // $("<li><a href='myTab.txt'>New Tab</a></li>").appendTo("#demoTabs .ui-tabs-nav");
    //     $("#demoTabs .tab_head:last").before("<li><a href='myTab.txt'>สมาชิก </a></li>");
    //     $("#demoTabs").tabs("refresh");
    // });

    $("[data-mask]").inputmask();
    $('#datepicker').datepicker({
      dateFormat: 'dd-mm-yy',
      autoclose: true
    });
    $('#datepicker2').datepicker({
      dateFormat: 'dd-mm-yy',
      autoclose: true
    });
    $('.select2').select2({
      placeholder: "Select a state"
    });
    $('.select2').on('change', function() {
      var data = $(".select2 option:selected").val();
      $scope.origin_address =data;
    });
    $('.select2').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);    
    });
    $scope.getIdCard =()=>{
        return $("#idCard").val();
    }
    $scope.getMobile =()=>{
        return  $("#mobile").val();
    }
    $scope.getBirthDate =()=>{
        var a = new Date($( "#datepicker" ).datepicker( "getDate" ));
        console.log(a);
        console.log(a.getTime());
        // console.log($( "#datepicker" ).datepicker( "getDate" ));
    }
    $scope.clickImage=()=>{
        $("#exampleInputFile").click();
    }
    $scope.fileNameChanged = function (ele) {
      var files = ele.files;
      var l = files.length;
      console.log(files);
        var reader = new FileReader();
       reader.readAsDataURL(files[0]);
       reader.onload = function () {
         $scope.picture = reader.result;
         var img = $("#pictureView");
        img.attr("src",$scope.picture);
       };
       reader.onerror = function (error) {
         console.log('Error: ', error);
       };
    }

    $scope.save=()=>{
        $scope.getBirthDate();
        var data ={
            name : $scope.fullname,
            gender : $scope.gender,
            idCard : $("#idCard").val(),
            birth_date : $( "#datepicker" ).datepicker( "getDate" ),
            national:$scope.national,
            edu:$scope.edu,
            career:$scope.career,
            academy:$scope.academy,
            origin_address_descr:$scope.origin_address_descr,
            origin_address:$scope.origin_address,
            mobile : $("#mobile").val(),
            phone : $("#phone").val(),
            car:$scope.car,
            biker:$scope.biker,
            reference : $scope.reference,
            picture : $scope.picture,
            start_date:$( "#datepicker2" ).datepicker( "getDate" ),
            is_header_family : $scope.is_header_family,
            member_status : $scope.relation,
            roomId:params.id,
            person_type:3,
            family_id:6

        }
        console.log(data);
        $rootScope.api({
            method:"POST",
            url: "/person/add",
            data:{personRqType:data},
            success:function(res){
                console.log(res);
            },
            fail:function(){

            }

        });

        $scope.getMemberDetail = (memberId)=>{
            alert(memberId);
        }
    }
    
});