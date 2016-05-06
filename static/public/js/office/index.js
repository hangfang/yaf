$(function(){
    $('#file').on('change', function(e){
        var form = $('#form');
        var ext = e.target.files[0].name.split('.').pop();
        if(ext === 'xls' || ext === 'xlsx'){
            form.attr('action', '/Weapp/Office/parseExcel');
        }else if(ext === 'doc' || ext === 'docx'){
            form.attr('action', '/Weapp/Office/parseWord');
        }else{
            
            return false;
        }
        form.submit();
    });
    
});