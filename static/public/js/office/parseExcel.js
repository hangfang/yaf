$(function(){
    var dataSource = new kendo.data.DataSource({
        data: data,
        //batch: true,
        pageSize: 20,
//        schema: {
//            parse: function(response) {
//                $(".img-preview").imgbox({
//                    'zoomOpacity' : true,
//                    'alignment' : 'center'
//                });
//                
//                return response;
//            }
//        }
    });
    
    $('#grid').kendoGrid({
        dataSource: dataSource,
        allowCopy: true,
        navigatable: true,
        selectable: "multiple row",
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        
        scrollable: false,
        sortable: true,
        toolbar: ["create", "save", "cancel"],
        editable: "popup",//true、inline、popup
        columns: columns,
        //rowTemplate: kendo.template($("#rowTemplate").html()),
        //groupable: true,
    });
});