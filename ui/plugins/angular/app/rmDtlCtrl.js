app.controller('rmDtlCtrl', function($rootScope,$http,$scope,$route,$filter) {
    var params = $route.current.params;
    $scope.persons = [];
    $scope.origin_address = null;
    // $scope.districts= json_districts;
    $scope.is_header_family = false;


    $http.get($rootScope.apiUrl+"/home/roomDetail?room_id="+params.id)
    .then(function(response) {
        $scope.roomDetail = response.data.data;
        if($scope.roomDetail.find){
            var family = $scope.roomDetail.family;
            $scope.home = $scope.roomDetail.home;
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
            $scope.start_date = family.start_date.split(" ")[0];
            $scope.owner_group_id = $scope.headFam.OWNER_GROUP_ID;
            $scope.nickname = $scope.headFamCur.PERS_NICKNAME;
            // $scope.relation = $rootScope.HEAD_FAMILY_TEXT;
            $('#datepicker').datepicker("setDate",new Date($scope.headFam.BIRTHDAY));
            $('#datepicker2').datepicker("setDate",new Date(family.start_date));
        }else{
            $scope.room_detail = $scope.roomDetail.room;
            $scope.home = $scope.room_detail.home;
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
    // $("[data-mask]").inputmask();
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
    $scope.clearForm = ()=>{
        $scope.fullname = null;
        $scope.idCard = null;
        $scope.national = null;
        $scope.edu = null;
        $scope.career = null;
        $scope.academy = null;
        $scope.mobile = null;
        $scope.phone = null;
        $scope.origin_address_descr = null;
        $scope.origin_address = null;
        $scope.car = null;
        $scope.biker = null;
        $scope.reference = null;
        $scope.gender = null ;
        $scope.birth_date = null;
        $scope.start_date = null;
        $scope.nickname = null;
        $('#datepicker').datepicker("setDate",null);
        $('#datepicker2').datepicker("setDate",null);
    }
    $scope.addBtn=()=>{
        $scope.clearForm();
        if($scope.roomDetail.find){
            $scope.is_header_family = false;  
        }else{
            $scope.family_id = null;
            $scope.is_header_family = true;
            $scope.relation = $rootScope.HEAD_FAMILY_TEXT;
        }
    }
    $scope.alertErrors = (text)=>{
        $scope.errorText = text;
        $scope.alertError = true;
    }
    $scope.save=()=>{
        /*if(!$scope.fullname) return $scope.alertErrors("ใส่ชื่อ - สกุล");
        if(!$scope.nickname) return $scope.alertErrors("ใส่ชื่อเล่น");
        if(!$scope.gender) return $scope.alertErrors("เลือกเพศ");
        if("" == $("#idCard").val()) return $scope.alertErrors("ใส่เลขประจำตัวประชาชน");
        if(!$scope.relation) return $scope.alertErrors("ใส่ความสัมพันธ์");
        if($( "#datepicker").datepicker( "getDate" ) == "Invalid Date") return $scope.alertErrors("เลือก วัน/เดือน/ปี เกิด");
        if(!$scope.national) return $scope.alertErrors("ใส่สัญชาติ");
        if(!$scope.edu) return $scope.alertErrors("ใส่การศึกษา");
        if(!$scope.career) return $scope.alertErrors("ใส่อาชีพ");
        if(!$scope.academy) return $scope.alertErrors("ใส่สังกัด โรงเรียน ชื่อหน่วยงาน");
        if("" == $("#phone").val()) return $scope.alertErrors("ใส่เบอร์โทรศัพท์ที่ทำงาน");
        if("" == $("#mobile").val()) return $scope.alertErrors("ใส่เบอร์โทรศัพท์มือถือ");
        if($( "#datepicker2").datepicker( "getDate" ) == "Invalid Date") return $scope.alertErrors("เลือก วัน/เดือน/ปี ที่เข้าพัก");
        if(!$scope.origin_address_descr) return $scope.alertErrors("ใส่ที่อยู่");
        if(!$scope.pv) return $scope.alertErrors("เลือกจังหวัด");
        if(!$scope.ap) return $scope.alertErrors("เลือกอำเภอ/เขต");*/
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
            // origin_address:$scope.origin_address,
            mobile : $("#mobile").val(),
            phone : $("#phone").val(),
            car:$scope.car,
            biker:$scope.biker,
            reference : $scope.reference !=null ? $scope.reference : 'ไม่ระบุ',
            picture : $scope.picture,
            start_date:$( "#datepicker2" ).datepicker( "getDate" ),
            is_header_family : $scope.is_header_family,
            member_status : $scope.relation !=null ? $scope.relation:'ไม่ระบุ',
            roomId:params.id,
            person_type:3,
            family_id:$scope.family_id,
            owner_group_id:$scope.owner_group_id != null ? $scope.owner_group_id : 0,
            nickname:$scope.nickname,
            pv:$scope.pv == null ? "0":$scope.pv,
            ap:$scope.ap == null ? "0":$scope.ap,
            dt:$scope.dt == null ? "0":$scope.dt

        }
        console.log(data);
        $rootScope.api({
            method:"POST",
            url: "/person/add",
            data:{personRqType:data},
            success:function(res){
                console.log(res);
                $("#btn_tmp_save").click();
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
        // $scope.districts= json_districts;
        $scope.person = response.data.data;
        $scope.person.birth_date = $scope.person.birth_date.split(" ")[0]; 
        $scope.person.start_date = $scope.person.start_date.split(" ")[0]; 
        // $scope.person.origin_address = $filter('filter')($rootScope.districts , {'d_id':$scope.person.origin_address})[0];
        // $scope.person.pv = $scope.person.pv.id;
        // alert($scope.person.pv);
    });
});
app.controller('mbEdtCtrl', function($rootScope,$http,$scope,$route,$filter) {
    var params = $route.current.params;
    $http.get($rootScope.apiUrl+"/person/memberDetail?id="+params.id+"&h="+params.h)
    .then(function(response) {
        $scope.person = response.data.data;
        $('#datepicker').datepicker("setDate",null == $scope.person.birth_date ? null : new Date($scope.person.birth_date));
        $('#datepicker2').datepicker("setDate",null == $scope.person.start_date ? null : new Date($scope.person.start_date));
        $scope.person.pv = $scope.person.pv.id;
        $rootScope.getAmphures($scope.person.pv);
        $scope.person.ap = $scope.person.ap.id;
        $rootScope.getDistricts($scope.person.ap);
        $scope.person.dt = $scope.person.dt.id;
    });
    // $("[data-mask]").inputmask();
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
    $scope.alertErrors = (text)=>{
        $scope.errorText = text;
        $scope.alertError = true;
    }
    $scope.edit=()=>{
        // $scope.getBirthDate();
        /*if(!$scope.person.fullname) return $scope.alertErrors("ใส่ชื่อ - สกุล");
        if(!$scope.person.nickname) return $scope.alertErrors("ใส่ชื่อเล่น");
        if(!$scope.person.gender) return $scope.alertErrors("เลือกเพศ");
        if("" == $("#idCard").val()) return $scope.alertErrors("ใส่เลขประจำตัวประชาชน");
        if(!$scope.person.relation) return $scope.alertErrors("ใส่ความสัมพันธ์");
        if($( "#datepicker").datepicker( "getDate" ) == "Invalid Date") return $scope.alertErrors("เลือก วัน/เดือน/ปี เกิด");
        if(!$scope.person.national) return $scope.alertErrors("ใส่สัญชาติ");
        if(!$scope.person.edu) return $scope.alertErrors("ใส่การศึกษา");
        if(!$scope.person.career) return $scope.alertErrors("ใส่อาชีพ");
        if(!$scope.person.academy) return $scope.alertErrors("ใส่สังกัด โรงเรียน ชื่อหน่วยงาน");
        if("" == $("#phone").val()) return $scope.alertErrors("ใส่เบอร์โทรศัพท์ที่ทำงาน");
        if("" == $("#mobile").val()) return $scope.alertErrors("ใส่เบอร์โทรศัพท์มือถือ");
        if(!$( "#datepicker2").datepicker( "getDate" ) == "Invalid Date") return $scope.alertErrors("เลือก วัน/เดือน/ปี ที่เข้าพัก");
        if(!$scope.person.origin_address_descr) return $scope.alertErrors("ใส่ที่อยู่");
        if(!$scope.person.pv) return $scope.alertErrors("เลือกจังหวัด");
        if(!$scope.person.ap) return $scope.alertErrors("เลือกอำเภอ/เขต");*/
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
            // origin_address:$scope.person.origin_address,
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
            family_id:$scope.person.member_id,
            owner_group_id : $scope.person.owner_group_id,
            nickname : $scope.person.nickname,
            pv:$scope.person.pv == null ? "0":$scope.person.pv,
            ap:$scope.person.ap == null ? "0":$scope.person.ap,
            dt:$scope.person.dt == null ? "0":$scope.person.dt
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
app.controller('mbDltCtrl', function($rootScope,$http,$scope,$route,$filter) {
    $rootScope.page_name = "";
    var params = $route.current.params;
    $scope.roomId = params.roomId;
    $scope.h = params.h;
    // $scope.r = $route;
    $http.get($rootScope.apiUrl+"/person/memberDetail?id="+params.id+"&h="+params.h)
    .then(function(response) {
        $scope.person = response.data.data;
    });
    $scope.deleteMember = (id)=>{
        $http.get($rootScope.apiUrl+"/person/delete?id="+id+"&h="+$scope.h)
        .then(function(response) {
            window.location.replace(appConfig.httphost+"/ui/#!/room_details?id="+$scope.roomId);
        });        
    }
    
});
app.controller('qrCtrl', function($rootScope,$http,$scope,$route,$filter) {
    var params = $route.current.params;
    // $rootScope.page_name = "ห้อง" + $rootScope.getRoomStatus(params.status);
    var links = "";
    switch(params.p){
        case 'o': links = "?ownerId="+params.id+"&status="+params.status; break;
        case 'h': links = "?homeId="+params.id+"&status="+params.status; break;
        case 's': links = "?sectionId="+params.id+"&status="+params.status; break;
        default : links = "?ownerId="+params.id+"&status="+params.status;  break;
    }
    $http.get($rootScope.apiUrl+"/home/roomByStatus"+links)
    .then(function(response) {
        $scope.rooms = response.data.data;
        console.log($scope.rooms);       
    });

});