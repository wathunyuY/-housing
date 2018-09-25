var app = angular.module('myApp', ["ngRoute"]);

app.run(function($rootScope,$http,$filter) {
    $rootScope.page_name = "ระบบจัดการบ้านพัก";
    $rootScope.page_sub_name = "";
    $rootScope.host = appConfig.httphost+"/index.php"
    $rootScope.apiUrl = appConfig.httphost+"/index.php"
	$rootScope.filepath = appConfig.httphost;//"http://localhost:8070/home/"
    // $rootScope.districts= json_districts;
    $rootScope.unkownTh = 'ไม่ระบุ';
	$rootScope.masterData ={
    		ownerGroups :[],
    		homeTypes:[],
            provinces:[]
    }
    $rootScope.HEAD_FAMILY_TEXT = "หัวหน้าครอบครัว";
    $rootScope.genders = [{k:"M",v:"ชาย"},{k:"F",v:"หญิง"}];
    $rootScope.getGender=(g)=>{
        if(null == g) return $rootScope.unkownTh;
       var gd = $filter('filter')($rootScope.genders , {'k':g});
       return gd[0].v;
    }
    $rootScope.getAmphures= (id)=>{
        $http.get($rootScope.apiUrl+"/address/amphures/"+id)
        .then(function(response) {
            $rootScope.amphures = response.data.data;    
        });
    }
    $rootScope.getDistricts= (id)=>{
        $http.get($rootScope.apiUrl+"/address/districts/"+id)
        .then(function(response) {
            $rootScope.districts = response.data.data;    
        });
    }
    $rootScope.numadd = (num_str,add)=>{
        return parseInt(num_str) + parseInt(add);
    }

    $rootScope.loadOwnerGroup = ()=>{
        $http.get($rootScope.apiUrl+"/home/ownerGroups")
        .then(function(response) {
            $rootScope.masterData.ownerGroups = response.data.data;
        });    
    } 
    $rootScope.dateStr = (date)=>{
        if(null == date) return $rootScope.unkownTh;
        var d = new Date(date);
        return d.getDate() + " " + month_th[d.getMonth()] + " " + d.getFullYear();
    }
    $http.get($rootScope.apiUrl+"/home/ownerGroups")
    .then(function(response) {
        $rootScope.masterData.ownerGroups = response.data.data;
    });

    $http.get($rootScope.apiUrl+"/home/masterData")
    .then(function(response) {
        $rootScope.masterData.homeTypes = response.data.data.home_type;
        $rootScope.masterData.roomStatus = response.data.data.room_status;
        $rootScope.masterData.provinces = response.data.data.provinces;
    });

    $rootScope.api =(req)=>{
    	$http.defaults.headers.post["Content-Type"] = "application/json";
    	$http({
        	method : req.method,
	       	url : $rootScope.apiUrl+req.url,
	       	data  : JSON.stringify(req.data) 
	    }).then(req.success, req.fail);
	    // $http.post($rootScope.apiUrl+req.url,{data:[{a:1}]});
    }

    $rootScope.getRoomStatus = (roomStatusId)=>{
        var t = $filter('filter')($rootScope.masterData.roomStatus , {'ROOM_STATUS_ID':roomStatusId})
        return t[0].ROOM_STATUS_NAME;
    }
    $rootScope.getOwnerGroup = (id)=>{
        var t = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':id})
        return t[0].OWNER_GROUP_DESCR;
    }
    $rootScope.setTable=(id)=>{
        $("#"+id).DataTable(); 
    }
});

app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "template/blank.html"
    })
    // .when("/new_persons", {
    //     templateUrl : "template/new_person.html",
    //     controller : "newPersCtrl"
    // })
    .when("/owner_groups", {
        templateUrl : "template/owner_group.html",
        controller : "ownerGrpCtrl"
    })
    .when("/homes", {
        templateUrl : "template/home_card.html",
        controller : "homeCrdCtrl"
    })
    .when("/sections", {
        templateUrl : "template/section_card.html",
        controller : "secCrdCtrl"
    })
    .when("/rooms", {
        templateUrl : "template/room_card.html",
        controller : "rmCrdCtrl"
    })
    .when("/room_details", {
        templateUrl : "template/room_detail.html",
        controller : "rmDtlCtrl"
    })
    .when("/member_details", {
        templateUrl : "template/member_detail.html",
        controller : "mbDtlCtrl"
    })
    .when("/member_edits", {
        templateUrl : "template/member_edit.html",
        controller : "mbEdtCtrl"
    })
    .when("/reports", {
        templateUrl : "template/report.html",
        controller : "rpCtrl"
    })
    .when("/quick_rooms", {
        templateUrl : "template/quick_room.html",
        controller : "qrCtrl"
    })
    .when("/member_deletes", {
        templateUrl : "template/member_delete.html",
        controller : "mbDltCtrl"
    });
});

app.controller('newPersCtrl', function($scope,$rootScope) {
	$scope.setTable=()=>{
		$("#example1").DataTable();	
	}
	$("#btnCol").click();
    $("[data-mask]").inputmask();
    $('#datepicker').datepicker({
      autoclose: true
    });    
    $scope.fullname ="ผบ.ทบ. สมชาย ใจดี";
    $scope.idCard ="1958800540078";
    $scope.picture="";
    $scope.gender ="ชาย";
    $scope.persons = [
    	{
    		name:"สต. เอ บิซีดี",
    		gender : $rootScope.genders[0],
    		idCard:"000000000000",
    		relation:"-",
    		mobile:"0123456789"
    	}
    ]
    $scope.getIdCard =()=>{
    	$scope.idCard =$("#idCard").val();
    }
    $scope.getMobile =()=>{
    	$scope.mobile =$("#mobile").val();
    }
    $scope.getBirthDate =()=>{
    	$scope.currentDate = $( "#datepicker" ).datepicker( "getDate" );
    }
    $scope.clickImage=()=>{
        $("#exampleInputFile").click();
    }
    $scope.save=()=>{
    	var data ={
    		name : $scope.fullname,
    		gender : $scope.gender,
    		idCard : $("#idCard").val(),
    		birthDate : $( "#datepicker" ).datepicker( "getDate" ),
    		mobile : $("#mobile").val(),
    		address : $scope.real_address,
    		picture : $scope.picture
    	}
    	console.log(data);
    	$rootScope.api({
    		method:"POST",
    		url: "/person/add",
    		data:data,
    		success:function(){

    		},
    		fail:function(){

    		}

    	});
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

});

