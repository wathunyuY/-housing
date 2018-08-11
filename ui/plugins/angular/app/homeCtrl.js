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
    $scope.owner.homesMod = $scope.owner.homes.map((h) =>{
        var links = "";
        if(h.HOME_TYPE_ID == 1 || h.HOME_TYPE_ID==4){
            var room_id = h.sections[0].rooms[0].ROOM_ID;
            links = "#!room_details?id="+room_id;
        }else links = "#!sections?id="+h.HOME_ID+"&owner="+$scope.owner.OWNER_GROUP_ID;
            h["links"]=links;
        return h;
    });
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

    $scope.addHome = ()=>{
        var secs = [];
        if($scope.home_type == 2 || $scope.home_type == 3 ){
            // alert($scope.column + " " + $scope._row);
            var sec = {};
            var room = {};
            var sec_names = ["ชั้น","แถว"];
            for (var i = 0; i < $scope._row; i++) {
                sec = {
                    sectionId: null,
                    sectionName: sec_names[$scope._row-2] + " " + (i+1),
                    sectionOrder : i,
                    rooms:[]
                }
                for (var j = 0; j < $scope.column; j++) {
                    room = {
                        roomId: null,
                        roomName: "ห้อง"+ " " + (j+1),
                        roomOrder: j,
                        roomAddress: $scope.add_main,
                        roomSubAddress: $scope.add_sub,
                        roomSeq: j,
                        roomStatusId: 1,
                        ownerGroupId: $scope.owner_group
                    }
                    sec.rooms.push(room);
                }
                secs.push(sec);
            }
        }else{
            secs = [
                    {
                        sectionId: null,
                        sectionName: "-",
                        sectionOrder : 0,
                        rooms:[
                            {
                                roomId: null,
                                roomName: "-",
                                roomOrder: 0,
                                roomAddress: $scope.add_main,
                                roomSubAddress: $scope.add_sub,
                                roomSeq: 0,
                                roomStatusId: 1,
                                ownerGroupId: $scope.owner_group
                            }
                        ]
                    }
                ];
        }
        var data ={
            homeId: null,
            homeName: $scope.home_name,
            homeDescr: $scope.home_descr != null ? $scope.home_descr : "-",
            homeTypeId: $scope.home_type,
            ownerGroupId: $scope.owner_group,
            sections : secs
        }
        console.log(data)
        $rootScope.api({
            method:"POST",
            url: "/home/add",
            data:{homeRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
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

    $scope.addSec = ()=>{
        
        var data ={
            homeId: $scope.home.HOME_ID,
            sectionId: null,
            sectionName: $scope.sec_name,
            sectionOrder : $scope.home.sections.length,
            rooms:[]
        }
        for (var j = 0; j < $scope.num_room; j++) {
            room = {
                roomId: null,
                roomName: "ห้อง"+ " " + (j+1),
                roomOrder: j,
                roomAddress: $scope.home.sections[0].rooms[0].ROOM_ADDRESS,
                roomSubAddress: $scope.home.sections[0].rooms[0].ROOM_SUB_ADDRESS,
                roomSeq: j,
                roomStatusId: 1,
                ownerGroupId: $scope.owner[0].OWNER_GROUP_ID
            }
            data.rooms.push(room);
        }
        console.log(data)
        $rootScope.api({
            method:"POST",
            url: "/home/section/add",
            data:{secRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
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
    $scope.getRoomStatus = (roomStatusId)=>{
        var t = $filter('filter')($rootScope.masterData.roomStatus , {'ROOM_STATUS_ID':roomStatusId})
        return t[0].ROOM_STATUS_NAME;
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

    $scope.addRoom = ()=>{
        
        var data = {
                roomId: null,
                sectionId: $scope.section.HOME_SECTION_ID,
                roomName: $scope.room_name,
                roomOrder: $scope.section.rooms.length,
                roomAddress: $scope.section.rooms[0].ROOM_ADDRESS,
                roomSubAddress: $scope.section.rooms[0].ROOM_SUB_ADDRESS,
                roomSeq: $scope.section.rooms.length,
                roomStatusId: 1,
                ownerGroupId: $scope.owner[0].OWNER_GROUP_ID
        };
        console.log(data)
        $rootScope.api({
            method:"POST",
            url: "/home/room/add",
            data:{roomRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
    }
});

