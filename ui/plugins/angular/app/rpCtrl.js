app.controller('rpCtrl', function($rootScope,$scope,$http,$filter) {
	$rootScope.page_name = "รายงาน";
    $rootScope.page_sub_name = "";
	$scope.tog = false;
	$scope.tog2 = false;
	$scope.ownerTemp = -1;
	$scope.homes =[];
	$scope.pv =0;
	$scope.ap =0;
	$scope.dt =0;
	$scope.ownerClick = (id)=>{
		if($scope.ownerTemp == id)
			$scope.tog = !$scope.tog;
		else{
			$scope.tog = true;
			$scope.ownerTemp = id;
		}		
		$rootScope.api({
	        method:"GET",
	        url: "/home/homeByOwner/"+id,
	        data:{},
	        success:function(res){
	            $scope.homes = res.data.data;
	            console.log($scope.homes);
	            $scope.homes = $scope.homes.map((h) =>{
	                var links = "";
	                // if(h.HOME_TYPE_ID == 1 || h.HOME_TYPE_ID==4){
	                    links = $rootScope.host+"/report/report/"+id+"?home="+h.HOME_ID;
	                // }else links = "#!sections?id="+h.HOME_ID+"&owner="+params.id;
	                
	                h["links"]=links;
	                return h;
	            });
	        },
	        fail:function(){

	        }

	    });
	}
	$scope.roomSearchRs = [];
	$scope.findRoom=()=>{
		$rootScope.api({
	        method:"POST",
	        url: "/home/roomSearch",
	        data:{	
	        		owner:$scope.ownerTemp,
	        		key:($scope.find_key == null ? '':$scope.find_key),
					pv:($scope.pv == null ? '0' : $scope.pv),
					ap:($scope.ap == null ? '0' : $scope.ap),
					dt:($scope.dt == null ? '0' : $scope.dt),
					bd:($( "#birthday").datepicker( "getDate" ) == 'Invalid Date' ? null : $( "#birthday").datepicker( "getDate" )),
					sd:($( "#startDate").datepicker( "getDate" )  == 'Invalid Date' ? null :$( "#startDate").datepicker( "getDate" ))
				},
	        success:function(res){
	            $scope.roomSearchRs = res.data.data;
	            console.log($scope.roomSearchRs);
	            $scope.tog2 = true;
	        },
	        fail:function(){

	        }

	    });
	}
	
	$('#birthday').datepicker({
      dateFormat: 'dd-mm-yy',
      autoclose: true
    });
    $('#startDate').datepicker({
      dateFormat: 'dd-mm-yy',
      autoclose: true
    });

});