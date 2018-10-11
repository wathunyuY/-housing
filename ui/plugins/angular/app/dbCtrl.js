app.controller('dbCtrl', function($rootScope,$scope,$http,$filter,$route) {
	$rootScope.page_name = "รายงาน";
    $rootScope.page_sub_name = "";
    $scope.persid = 100;

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
                	$route.reload();
                }
            },
            fail:function(){

            }

        });
    }

});