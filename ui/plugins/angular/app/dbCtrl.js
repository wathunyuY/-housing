app.controller('dbCtrl', function($rootScope,$scope,$http,$filter,$route) {
	$rootScope.page_name = "รายงาน";
    $rootScope.page_sub_name = "";
    $scope.persid = 100;
    $scope.nameedit = "";
    $scope.idedit = null;
    $scope.typeedit = null;

    $http.get($rootScope.apiUrl+"/home/dashboard")
    .then(async function(response) {
    	$scope.stat = response.data.data.roomstat;
    	$scope._all = Math.round($scope.stat._all * 100 / $scope.stat._all);
    	$scope._stay = Math.round($scope.stat._stay * 100 / $scope.stat._all);
    	$scope._empty = Math.round($scope.stat._empty * 100 / $scope.stat._all);
    	$scope._fix = Math.round($scope.stat._fix * 100 / $scope.stat._all);
    	$scope.personstat = response.data.data.personstat;
    	$scope._all = Math.round($scope.personstat._all * 100 / $scope.personstat._all);
    	$scope._nomal = Math.round($scope.personstat._nomal * 100 / $scope.personstat._all);
    	$scope._owner = Math.round($scope.personstat._owner * 100 / $scope.personstat._all);
    	console.log(response);
    });

    $scope.addAdmin = function(){
    	if(!($scope.user)){
    		alert("username เป็นค่าว่าง!"); 
    		return;
    	}
    	if($scope.password != $scope.confirmpassword) {
    		alert("password ไม่ตรงกัน"); 
    		return;
    	}else if($scope.password.length < 5){
    		alert("password ต้องมีอย่างน้อย 5 ตัว");
    		return ;
    	}
    	var data = {user:$scope.user,password:$scope.password,confirmpassword:$scope.confirmpassword};
    	$rootScope.api({
            method:"POST",
            url: "/person/addadmin",
            data:{adminRqType:data},
            success:function(res){
                console.log(res);
                if(res.data.code != 0){
                	alert(res.data.message);
                }else{
                	alert("เพิ่ม Admin เรียบร้อย (Username : ["+res.data.data.USERNAME+"] password : ["+ res.data.data.PASSWORD+"])");
                	// $route.reload();
                    location.reload();
                }
            },
            fail:function(){

            }

        });
    }
    $scope.editmasterdata = function(id,type){
		var obj = null;
		$scope.typeedit = type;
		$scope.idedit =id;
		if(type == 'type'){
    		obj = $filter('filter')($rootScope.masterData.homeTypes , {'HOME_TYPE_ID':id})[0];
    		$scope.nameedit = obj.HOME_TYPE_NAME;
		}
    	else{
    		obj = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':id})[0];
    		$scope.nameedit = obj.OWNER_GROUP_DESCR;
    	}
    	console.log(obj);
    }
    $scope.savemasterdata = function(){
		 $http.get($rootScope.apiUrl+"/home/"+$scope.typeedit+"/edit?id="+$scope.idedit+"&name="+$scope.nameedit)
	    .then(/*async*/ function(response) {
	    	// await $rootScope.getMasterData();
	    	// $route.reload();
            location.reload();
	    });
    }

});