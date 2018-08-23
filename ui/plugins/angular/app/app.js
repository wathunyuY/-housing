var app = angular.module('myApp', ["ngRoute"]);

app.run(function($rootScope,$http,$filter) {
    $rootScope.apiUrl = "http://localhost:8070/home/index.php"
	$rootScope.filepath = "http://localhost:8070/home/"
	$rootScope.masterData ={
    		ownerGroups :[],
    		homeTypes:[]
    }
    $rootScope.HEAD_FAMILY_TEXT = "หัวหน้าครอบครัว";
    $rootScope.genders = [{k:"M",v:"ชาย"},{k:"F",v:"หญิง"}];
    $rootScope.getGender=(g)=>{
       var gd = $filter('filter')($rootScope.genders , {'k':g});
       return gd[0].v;
    }
    $http.get($rootScope.apiUrl+"/home/ownerGroups")
    .then(function(response) {
        $rootScope.masterData.ownerGroups = response.data.data;
    });

    $http.get($rootScope.apiUrl+"/home/masterData")
    .then(function(response) {
        $rootScope.masterData.homeTypes = response.data.data.home_type;
        $rootScope.masterData.roomStatus = response.data.data.room_status;
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

