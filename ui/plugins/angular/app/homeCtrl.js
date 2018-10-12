app.controller('ownerGrpCtrl', function($rootScope,$scope,$http,$filter) {
    $rootScope.page_name = "ระบบจัดการบ้านพัก";
    // console.log($rootScope.masterData);
    // $scope.fel = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':1}) 
    $rootScope.loadOwnerGroup();
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
    // $scope.owner = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':params.id})
    // $scope.owner = $scope.owner[0];
    $rootScope.api({
        method:"GET",
        url: "/home/homeByOwner/"+params.id,
        data:{},
        success:function(res){
            $scope.homes = res.data.data;
            console.log($scope.homes);
            $scope.homes = $scope.homes.map((h) =>{
                var links = "";
                var linkEmpty = "";
                var linkStay = "";
                var linkFixed = "";
                if(h.HOME_TYPE_ID == 1 || h.HOME_TYPE_ID==4 || h.HOME_TYPE_ID==6){
                    var room_id = h.sections[0].rooms[0].ROOM_ID;
                    links = "#!room_details?id="+room_id;
                }else if(h.HOME_TYPE_ID == 2 || h.HOME_TYPE_ID==3){
                    links = "#!sections?id="+h.HOME_ID+"&owner="+params.id;
                    linkEmpty = "#!quick_rooms?id="+h.HOME_ID+"&status=1&p=h";
                    linkStay = "#!quick_rooms?id="+h.HOME_ID+"&status=2&p=h";
                    linkFixed = "#!quick_rooms?id="+h.HOME_ID+"&status=3&p=h";
                }else{
                    var sec_id = h.sections[0].HOME_SECTION_ID;
                    links = "#!rooms?id="+sec_id+"&owner="+params.id+"&home="+h.HOME_ID;
                }
                h["links"]=links;
                h["linkEmpty"]=linkEmpty;
                h["linkStay"]=linkStay;
                h["linkFixed"]=linkFixed;
                return h;
            });
        },
        fail:function(){

        }

    });

    
    $scope.getHomeType = (id)=>{
        var t = $filter('filter')($rootScope.masterData.homeTypes , {'HOME_TYPE_ID':id})
        return t[0].HOME_TYPE_NAME;
    }
    $scope.getOwnerGroup = (id)=>{
        var t = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':id})
        return t[0].OWNER_GROUP_NAME + " - " + t[0].OWNER_GROUP_DESCR;
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
                    sectionName: sec_names[$scope.home_type-2] + " " + (i+1),
                    sectionOrder : i,
                    rooms:[]
                }
                for (var j = 0; j < $scope.column; j++) {
                    room = {
                        roomId: null,
                        roomName: "ห้อง"+ " " + (j+1),
                        roomOrder: j,
                        // roomAddress: $scope.add_main,
                        // roomSubAddress: $scope.add_sub,
                        roomSeq: j+1,
                        roomStatusId: 1,
                        ownerGroupId: $scope.owner_group
                    }
                    sec.rooms.push(room);
                }
                secs.push(sec);
            }
        }else if($scope.home_type == 1 || $scope.home_type == 4 || $scope.home_type == 6 ){
            secs = [
                    {
                        sectionId: null,
                        sectionName: "",
                        sectionOrder : 0,
                        rooms:[
                            {
                                roomId: null,
                                roomName: "",
                                roomOrder: 0,
                                roomAddress: $scope.home_addr_one,
                                roomSubAddress: $scope.home_sub_addr_one,
                                roomSeq: 1,
                                roomStatusId: 1,
                                ownerGroupId: $scope.owner_group
                            }
                        ]
                    }
                ];
        }else{//บ้านแฝด
            secs = [
                    {
                        sectionId: null,
                        sectionName: "-",
                        sectionOrder : 0,
                        rooms:[
                            {
                                roomId: null,
                                roomName: "บ้านแฝดห้อง 1",
                                roomOrder: 0,
                                roomAddress: $scope.home_addr_duo1,
                                roomSubAddress: $scope.home_sub_addr_duo1,
                                roomSeq: 1,
                                roomStatusId: 1,
                                ownerGroupId: $scope.owner_group
                            },
                            {
                                roomId: null,
                                roomName: "บ้านแฝดห้อง 2",
                                roomOrder: 1,
                                roomAddress: $scope.home_addr_duo2,
                                roomSubAddress: $scope.home_sub_addr_duo2,
                                roomSeq: 2,
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
            homeNumber: $scope.add_main,
            homeSubNumber: $scope.add_sub,
            homeSubNumberSeq: $scope.add_sub_seq,
            homeAddr: $scope.home_addr_front,
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
            fail:function(err){
                console.log(err);
            }

        });
    }
    $scope.homeDetail = (index)=>{
        var h = $scope.homes[index];
        $scope.home_name = h.HOME_NAME;
        $scope.home_descr = h.HOME_DESCR  != null ? h.HOME_DESCR : "-";
        $scope.home_type = $scope.getHomeType(h.HOME_TYPE_ID);
        $scope.home_type_id = h.HOME_TYPE_ID;
        $scope.owner_group =  $scope.getOwnerGroup(h.OWNER_GROUP_ID);
        if(h.HOME_TYPE_ID == 2 || h.HOME_TYPE_ID == 3){
            var room = 0;
            var sec = 0;
            for (var i = 0; i < h.sections.length; i++) { sec++;
                for (var j = 0; j < h.sections[i].rooms.length; j++)room++;
            }
            $scope.sec_count = sec;
            $scope.room_count = room;
        }
        $scope.add_main =h.HOME_NUMBER;// h.sections[0].rooms[0].ROOM_ADDRESS;
        $scope.add_sub = h.HOME_SUB_NUMBER;//h.sections[0].rooms[0].ROOM_SUB_ADDRESS;
    }  
    $scope.editHomePre = (index)=>{
        var h = $scope.homes[index];
        $scope.home_index = index;
        $scope.home_name = h.HOME_NAME;
        $scope.owner_group = h.OWNER_GROUP_ID;
    }
    $scope.editHome = (index)=>{
        var h = $scope.homes[index];
        var data ={
            homeId: h.HOME_ID,
            homeName: $scope.home_name,
            homeDescr: $scope.home_descr != null ? $scope.home_descr : "-"
        }
        console.log(data)
        $rootScope.api({
            method:"POST",
            url: "/home/edit",
            data:{homeRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(err){
                console.log(err);
            }

        });
    }
    $scope.deleteHome = (index)=>{
        var h = $scope.homes[index];
        $rootScope.api({
            method:"GET",
            url: "/home/delete/"+h.HOME_ID,
            data:{},
            success:function(res){
                console.log(res);
                if(res.data.data) alert("ไม่สามารถลบได้");
                $route.reload();
            },
            fail:function(err){
                console.log(err);
            }

        });
    }
});
app.controller('secCrdCtrl', function($rootScope,$scope,$route,$filter) {
    var params = $route.current.params;
    // $scope.owner = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':params.owner})
    // $scope.home = $filter('filter')($scope.owner[0].homes , {'HOME_ID':params.id});
    // $scope.home =  $scope.home[0];
    $scope.ownerId = params.owner;
    $rootScope.api({
        method:"GET",
        url: "/home/sectionByHome/"+params.id,
        data:{},
        success:function(res){
            $scope.home = res.data.data;
            $scope.sec_name = ($scope.home.HOME_TYPE_ID == 2 ? "ชั้น " : "แถว ") +($scope.home.sections.length+1);
            console.log($scope.home);
            $scope.home.sections = $scope.home.sections.map((sec)=>{
                sec["linkEmpty"] = "#!quick_rooms?id="+sec.HOME_SECTION_ID+"&status=1&p=s";
                sec["linkStay"] =  "#!quick_rooms?id="+sec.HOME_SECTION_ID+"&status=2&p=s";
                sec["linkFixed"] = "#!quick_rooms?id="+sec.HOME_SECTION_ID+"&status=3&p=s";
                return sec;
            });
        },
        fail:function(){

        }

    });

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
                ownerGroupId: params.owner
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

    $scope.secDetail = (index)=>{
        var h = $scope.home.sections[index];
        $scope.sec_name = h.HOME_SECTION_NAME;
        $scope.sec_descr = h.HOME_SECTION_DESCR  != null ? h.HOME_SECTION_DESCR : "-";
        var room = 0;
        for (var j = 0; j < h.rooms.length; j++)room++;
        $scope.num_room = room;
    } 

    $scope.editSecPre = (index)=>{
        var s = $scope.home.sections[index];
        $scope.sec_index = index;
        $scope.sec_name = s.HOME_SECTION_NAME;
        // $scope.owner_group = h.OWNER_GROUP_ID;
    }
    $scope.editSec = (index)=>{
        var s = $scope.home.sections[index];
        var data ={
            homeId: $scope.home.HOME_ID,
            sectionId: s.HOME_SECTION_ID,
            sectionName: $scope.sec_name,
        }
        $rootScope.api({
            method:"POST",
            url: "/home/section/edit",
            data:{secRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
    }
    $scope.deleteSec = (index)=>{
        var h = $scope.home.sections[index];
        $rootScope.api({
            method:"GET",
            url: "/home/section/delete/"+h.HOME_SECTION_ID,
            data:{},
            success:function(res){
                console.log(res);
                if(res.data.data) alert("ไม่สามารถลบได้");
                $route.reload();
            },
            fail:function(err){
                console.log(err);
            }
        });
    }

});

app.controller('rmCrdCtrl', function($rootScope,$scope,$route,$filter) {
    var params = $route.current.params;
    // $scope.owner = $filter('filter')($rootScope.masterData.ownerGroups , {'OWNER_GROUP_ID':params.owner})
    // $scope.home = $filter('filter')($scope.owner[0].homes , {'HOME_ID':params.home});
    // $scope.section = $filter('filter')($scope.home[0].sections , {'HOME_SECTION_ID':params.id});
    // $scope.section =  $scope.section[0];
    // console.log($scope.section);
    $rootScope.api({
        method:"GET",
        url: "/home/roomBySection/"+params.id,
        data:{},
        success:function(res){
            $scope.section = res.data.data;
            $scope.room_name = "ห้อง " +($scope.section.rooms.length+1);
            console.log($scope.home);
        },
        fail:function(){

        }

    });

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
                ownerGroupId: params.owner
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
    $scope.editRoomPre = (index)=>{
        var s = $scope.section.rooms[index];
        $scope.room_index = index;
        $scope.room_name = s.ROOM_NAME;
        // $scope.owner_group = h.OWNER_GROUP_ID;
    }
    $scope.editRoom = (index)=>{
        var s = $scope.section.rooms[index];
        var data = {
                roomId: s.ROOM_ID,
                sectionId: $scope.section.HOME_SECTION_ID,
                roomName: $scope.room_name,
                // roomAddress: $scope.section.rooms[0].ROOM_ADDRESS,
                // roomSubAddress: $scope.section.rooms[0].ROOM_SUB_ADDRESS,
                // roomStatusId: 1
        };
        console.log(data)
        $rootScope.api({
            method:"POST",
            url: "/home/room/edit",
            data:{roomRqType:data},
            success:function(res){
                console.log(res);
                $route.reload();
            },
            fail:function(){

            }

        });
    }
    $scope.deleteRoom = (index)=>{
        var h = $scope.section.rooms[index];
        $rootScope.api({
            method:"GET",
            url: "/home/room/delete/"+h.ROOM_ID,
            data:{},
            success:function(res){
                console.log(res);
                if(res.data.data) alert("ไม่สามารถลบได้");
                $route.reload();
            },
            fail:function(err){
                console.log(err);
            }
        });
    }
});

