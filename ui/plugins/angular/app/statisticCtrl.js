app.controller('statisticCtrl', function($rootScope,$scope,$http,$filter,$route) {
	$rootScope.page_name = "สถิติ";
    $rootScope.page_sub_name = "";
	var params = $route.current.params;

	$rootScope.api({
        method:"GET",
        url: "/statistic?owner_group_id="+params.id,
        data:{},
        success:function(res){
            $scope.ages = [];
            $scope.careers = res.data.data.careers;
             angular.forEach(res.data.data.ages.value, function(value, key){
     			$scope.ages.push( {
     				n:res.data.data.ages.name[key],
     				v:value
     			});
     		});
     		console.log($scope.ages);
        },
        fail:function(err){
            console.log(err);
        }

    });

});