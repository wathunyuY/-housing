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
            $scope.family_id = family.FAMILY_ID
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
            // $scope.relation = $rootScope.HEAD_FAMILY_TEXT;
            $('#datepicker').datepicker("setDate",new Date($scope.headFam.BIRTHDAY));
            $('#datepicker2').datepicker("setDate",new Date(family.start_date));
        }else{
            $scope.room_detail = $scope.roomDetail.room;
            $scope.family_id = null;
            $scope.is_header_family = false;            
        }
    });
    $scope.setTable=(id)=>{
        $("#"+id).DataTable(); 
    }
    $scope.setDateTime = function(id,date){
        $('#datepicker_'+id).datepicker({
          dateFormat: 'dd-mm-yy',
          autoclose: true
        });
        $('#datepicker_'+id).datepicker("setDate",new Date(date));
    }
    $scope.setDateTime2 = function(id,date){
        $('#datepicker2_'+id).datepicker({
          dateFormat: 'dd-mm-yy',
          autoclose: true
        });
        $('#datepicker2_'+id).datepicker("setDate",new Date(date));
    }
    console.log(params);
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

    $scope.addBtn=()=>{
        if($scope.roomDetail.find){
            $scope.is_header_family = false;  
        }else{
            $scope.family_id = null;
            $scope.is_header_family = true;
            $scope.relation = $rootScope.HEAD_FAMILY_TEXT;
        }
    }

    $scope.save=()=>{
        $scope.getBirthDate();
        var data ={
            name : $scope.fullname,
            gender : $scope.gender,
            idCard : $("#idCard").val(),
            birth_date : $( "#datepicker").datepicker( "getDate" ),
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
            family_id:$scope.family_id

        }
        console.log(data);
        $rootScope.api({
            method:"POST",
            url: "/person/add",
            data:{personRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
    }
    $scope.changeRoomStatus = (status)=>{
        $rootScope.api({
            method:"GET",
            url: "/home/room/changeStatus/"+$scope.room_detail.ROOM_ID+"/"+status,
            data:{},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });   
    }
    
});

app.controller('mbDtlCtrl', function($rootScope,$http,$scope,$route,$filter) {
    var params = $route.current.params;
    $http.get($rootScope.apiUrl+"/person/memberDetail?id="+params.id+"&h="+params.h)
    .then(function(response) {
        $scope.districts= json_districts;
        $scope.person = response.data.data;
        $scope.person.birth_date = $scope.person.birth_date.split(" ")[0]; 
        $scope.person.start_date = $scope.person.start_date.split(" ")[0]; 
        $scope.person.origin_address = $filter('filter')($scope.districts , {'d_id':$scope.person.origin_address})[0];
        
    });
});
app.controller('mbEdtCtrl', function($rootScope,$http,$scope,$route,$filter) {
    var params = $route.current.params;
    $http.get($rootScope.apiUrl+"/person/memberDetail?id="+params.id+"&h="+params.h)
    .then(function(response) {
        $scope.districts= json_districts;
        $scope.person = response.data.data;
        // $scope.person.birth_date = $scope.person.birth_date.split(" ")[0]; 
        // $scope.person.start_date = $scope.person.start_date.split(" ")[0]; 
        $('#datepicker').datepicker("setDate",new Date($scope.person.birth_date));
        $('#datepicker2').datepicker("setDate",new Date($scope.person.start_date));
        // $scope.person.origin_address = $filter('filter')($scope.districts , {'d_id':$scope.person.origin_address})[0];
        
    });
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
      $scope.person.origin_address =data;
    });
    $scope.getIdCard =()=>{
        return $("#idCard").val();
    }
    $scope.getMobile =()=>{
        return  $("#mobile").val();
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
    $scope.picture = null;
    $scope.edit=()=>{
        // $scope.getBirthDate();
        var data ={
            pers_id:$scope.person.pers_id,
            name : $scope.person.fullname,
            gender : $scope.person.gender,
            idCard : $("#idCard").val(),
            birth_date : $( "#datepicker").datepicker( "getDate" ),
            national:$scope.person.national,
            edu:$scope.person.edu,
            career:$scope.person.career,
            academy:$scope.person.academy,
            origin_address_descr:$scope.person.origin_address_descr,
            origin_address:$scope.person.origin_address,
            mobile : $("#mobile").val(),
            phone : $("#phone").val(),
            car:$scope.person.car,
            biker:$scope.person.biker,
            reference : $scope.person.reference,
            picture : $scope.picture,
            start_date:$( "#datepicker2" ).datepicker( "getDate" ),
            is_header_family : $scope.person.is_header,
            member_status : $scope.person.relation,
            // roomId:params.id,
            person_type:3,
            family_id:$scope.person.member_id
        }
        console.log(data);
        $rootScope.api({
            method:"POST",
            url: "/person/edit",
            data:{personRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
    }
});