var app = angular.module('myApp', ["ngRoute"]);

app.run(function($rootScope,$http) {
	$rootScope.apiUrl = "http://localhost:8070/home/index.php"
	$rootScope.masterData ={
    		ownerGroups :[],
    		homeTypes:[]
    }
    $rootScope.HEAD_FAMILY_TEXT = "หัวหน้าครอบครัว";
    $rootScope.genders = [{k:"M",v:"ชาย"},{k:"F",v:"หญิง"}];
    $http.get($rootScope.apiUrl+"/home/ownerGroups")
    .then(function(response) {
        $rootScope.masterData.ownerGroups = response.data.data;
    });

    $http.get($rootScope.apiUrl+"/home/type")
    .then(function(response) {
        $rootScope.masterData.homeTypes = response.data.data;
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

});

app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "template/blank.html"
    })
    .when("/new_persons", {
        templateUrl : "template/new_person.html",
        controller : "newPersCtrl"
    })
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

app.controller('ownerGrpCtrl', function($rootScope,$scope,$http,$filter) {
	// console.log($rootScope.masterData);
    // $scope.fel = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':1}) 
 	$scope.countRoom = (homes,status)=>{
 		var count = 0
 		angular.forEach(homes, function(home){
	        angular.forEach(home.sections, function(sec){
		        angular.forEach(sec.rooms, function(r){
		        	if(status != 0)
			        	count += r.ROOM_STATUS_ID == status ? 1 : 0;
			        else count++;
			    });
		    });
	    });
 		return count;
 	}
});

app.controller('homeCrdCtrl', function($rootScope,$scope,$route,$filter) {
    var params = $route.current.params;
    $scope.owner = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':params.id})
    $scope.owner = $scope.owner[0];
    $scope.getHomeType = (id)=>{
	    var t = $filter('filter')($rootScope.masterData.homeTypes , {'HOME_TYPE_ID':id})
	    return t[0].HOME_TYPE_NAME;
    }
    $scope.countRoom = (home,status)=>{
 		var count = 0
 		
        angular.forEach(home.sections, function(sec){
	        angular.forEach(sec.rooms, function(r){
	        	if(status != 0)
		        	count += r.ROOM_STATUS_ID == status ? 1 : 0;
		        else count++;
		    });
	    });
    
 		return count;
 	}
    $scope.goDetail =(typeId)=>{
        if(typeId == 1 || typeId == 4){
            $("#oneroom").click();
        }
    }
});
app.controller('secCrdCtrl', function($rootScope,$scope,$route,$filter) {
    var params = $route.current.params;
    $scope.owner = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':params.owner})
    $scope.home = $filter('filter')($scope.owner[0].homes , {'HOME_ID':params.id});
    $scope.home =  $scope.home[0];
    $scope.getHomeType = ()=>{
	    var t = $filter('filter')($rootScope.masterData.homeTypes , {'HOME_TYPE_ID':$scope.home.HOME_TYPE_ID})
	    return t[0].HOME_TYPE_NAME;
    }
    $scope.countRoom = (sec,status)=>{
 		var count = 0
 		
        angular.forEach(sec.rooms, function(r){
        	if(status != 0)
	        	count += r.ROOM_STATUS_ID == status ? 1 : 0;
	        else count++;
	    });
    
 		return count;
 	}
});

app.controller('rmCrdCtrl', function($rootScope,$scope,$route,$filter) {
    var params = $route.current.params;
    $scope.owner = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':params.owner})
    $scope.home = $filter('filter')($scope.owner[0].homes , {'HOME_ID':params.home});
    $scope.section = $filter('filter')($scope.home[0].sections , {'HOME_SECTION_ID':params.id});
    $scope.section =  $scope.section[0];
    console.log($scope.section);
    $scope.getHomeType = ()=>{
	    var t = $filter('filter')($rootScope.masterData.homeTypes , {'HOME_TYPE_ID':$scope.home[0].HOME_TYPE_ID})
	    return t[0].HOME_TYPE_NAME;
    }
    $scope.countRoom = (status)=>{
 		var count = 0
 		
        angular.forEach($scope.section.rooms, function(r){
        	if(status != 0)
	        	count += r.ROOM_STATUS_ID == status ? 1 : 0;
	        else count++;
	    });
    
 		return count;
 	}
});

