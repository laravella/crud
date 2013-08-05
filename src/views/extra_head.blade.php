{{-------------------------------------------------------- extra_head --------------}}

@section('extra_head')

<script type="text/javascript" src="/assets/scripts/js/vendor/jsonconvert.js"></script>

<style>
    /* ravel */
    #page {min-height:600px;}
    .package {margin-left:10px;padding:3px;border-radius:2px;margin-top:2px;}
    .header {cursor:pointer;}

    .name {color:gray;}

    .array {background-color:#FFD8BB;border:thin solid #FFB780;}
    .object {background-color:#E7F1FE;border:thin solid #7DA2CE;}
    .string {color:red;}
    .number {color:blue;}
    .function {color:green;}

    .open .children {display:block;}
    .closed .children {display:none;}

    .arrow {background-image:url("/assets/images/d.png"); background-repeat:no-repeat; background-color:transparent; height:15px; width:15px; display:inline-block;}

    .open .arrow {background-position:-20px 0;}
    .closed .arrow {background-position:0 0;}

    .type {color:gray;font-size:8pt;float:right;}

    .hide {display:none;}

    #main {width:100%;height:500px;overflow-y:scroll;}
</style>


<style>
    .label {
        width: 80px;
    }
    table.dbtable {
        margin : 0px;
        border : 0px;
        width : auto;
    }

    table.dbtable td {
        border : 1px solid #dddddd;
    }

    table.dbtable td input[type="text"] {
        margin-bottom: 0px;
    }

    div.table_container {
        width:100%; 
        overflow-x: auto;
        padding : 0px;
    }
    td.td_toolbox {
        text-align:center; 
        width: 89px; 
        max-width:89px;
    }
    span.h1a {
        color : #c0c0c0;
    }
    
    #footer {
        margin-top : 30px;
        margin-bottom : 30px;
    }
</style>

<script type="text/javascript">

    
    
    $(function() {    
    
        $('#msg-alert').fadeOut(4000);
    
        @foreach($tables as $tName=>$table)
    
        $('#acc-{{$tName}}').on('show', {table : '{{$tName}}'}, function (event) {
            $.get('/dbapi/select/' + event.data.table, function(data) {$('#acc-{{$tName}}').html(data);});
        });
        
        $('#acc-{{$tName}}').on('shown', {table : '{{$tName}}'}, function (event) {
            //$(this).html(event.data.table);
        });
        
        @endforeach
        
    });
    
    
    function alertBox(message) {
    $(".alert").alert();
    }

    function debugBox() {
    $('.alert-debug').show();
    $('.alert-debug').css('opacity', '1');
    }

    function logBox() {
    $('.alert-log').show();
    $('.alert-log').css('opacity', '1');
    }

    function sendSearch() {
    var qString = "";
    var qA = new Object();
    var comma = '';
    var table = '';
    var field = '';
    //turn the search form into JSON
    $('.formfield').each(function( index ) {
    table = $(this).attr('data-table');
    if (qA[table] == null || qA[table] == 'undefined' || !qA[table]) {
    qA[table] = new Object();
    }
    if ($(this).val().length > 0) {
    field = $(this).attr('name');
    qString += comma+'"'+$(this).attr('name')+'" : "'+$(this).val()+'"';
    qA[table][field] = $(this).val();
    comma = ',';
    }
    });
    qString = JSON.stringify(qA);
    window.location.href = "/db/search/"+table+"/"+qString;
    }

    function sendDelete() {
        var recNo = null;
        var tableName = null;
        $('a.record.active').each(function( index ) {
            console.log($(this).attr('id'));
            tableName = $(this).attr('data-tablename');
            console.log(tableName);
            recNo = $(this).attr('data-recordid');
            console.log(recNo);

            console.log('/dbapi/delete/'+tableName+'/'+recNo);

            $.get('/dbapi/delete/'+tableName+'/'+recNo, '',function(data) { 
                $('#tr-'+tableName+'-'+recNo).remove(); 
                //console.log(data);
            });

/*
            $.ajax({
                data: encodeURIComponent(data),
                type: "GET",
                url: '/dbapi/delete/'+tableName+'/'+recNo,
                timeout: 20000,
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                dataType: 'json',
                success: function(data) {console.log(data);}
            });        
  */          
            
        });
    }

    function checkRec(recNo) {
    $('#chkico_'+recNo).toggleClass('icon-ok-sign');
    $('#chkico_'+recNo).toggleClass('icon-ok-circle');
    }
    
    function saveRec(tableName, recNo) {
//        alert(tableName + " : " + recNo);

        var qA = new Object();
        //qA[tableName] = new Object();

        $(".fld-" + tableName + "-" + recNo).each(function (index, element) {
            
            //var table = $(this).attr('data-tablename');
            var record = $(this).attr('data-recordid');
            var fieldName = $(this).attr('data-fieldname');
            var value = $(this).val();
            
            qA[fieldName] = value;
            //console.log(tableName + fieldName + value);
            
        });
        var data = JSON.stringify(qA);
        console.log(data);
        
        $.ajax({
            data: encodeURIComponent(data),
            type: "POST",
            url: '/db/edit/'+tableName+'/'+recNo,
            timeout: 20000,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            dataType: 'json',
            success: function(data) {console.log(data);}
        });        
        
        //$.post('/db/edit/'+tableName+'/'+recNo, 'data='+encodeURIComponent(data), function(data) {console.log(data);}, 'json');
        
    }
</script>

@stop
