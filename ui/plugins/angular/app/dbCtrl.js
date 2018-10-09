app.controller('dbCtrl', function($rootScope,$scope,$http,$filter) {
	$rootScope.page_name = "รายงาน";
    $rootScope.page_sub_name = "";
    $scope.persid = 100;
	$('#p_type').on('change', function() {
	  if(this.value == 0){
	   $rootScope.api({
	        method:"GET",
	        url: "/person/getaccount",
	        data:{},
	        success:function(res){
	        	console.log(res);
	            $scope.persons = res.data.data;
	            var tr = "";
		          for (var i = 0; i < $scope.persons.length; i++) {
		             var w = $scope.persons[i];
		             // var cou = cc(w.reports_in_week);
		             // console.log(cou);
		             tr += '<tr>'+'<td>'+(i+1)+'</td>';
		             tr += '<td>' + (w.PERS_N_ID ? w.PERS_N_ID : '') + '</td>'
		             tr += '<td>' + (w.FIRST_NAME ? w.FIRST_NAME : '') + '</td>'
		             tr += '<td>' + (w.PERS_NICKNAME ? w.PERS_NICKNAME : '') + '</td>'
		             tr += '<td>' + '<input type="radio" name="persid" value="'+w.PERS_ID+'">' + '</td>'
		             // tr += '<td>' + '<button class="btn btn-success btn-saves" ><i class="fa fa-check-square-o"></i></button>' + '</td>'
		             tr +='</tr>';
		          }
		          $("#acc_tr").html(tr);
		          if ( ! $.fn.DataTable.isDataTable( "#tbaccount" ) ) {
			          $("#tbaccount").DataTable({"autoWidth": false}); 
			       }
		          $("#btnseluser").click();
	        },
	        fail:function(){

	        }

	    });
	   
	  }
	});

	$scope.save = function(pers_id){
		console.log($('input[type=radio]')[0]);

	}
});