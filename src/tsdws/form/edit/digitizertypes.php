<?php
    $id = isset($_GET["id"]) ? $_GET["id"] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Editor</title>
    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap-vue.min.css" />
    <script src="../js-download/jquery-3.6.0.min.js"></script>
    <script src="../js-download/jsoneditor-2.8.0.min.js"></script>
    <style>
        /* The Modal (background) */
        .mymodal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .mymodal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            font-size:0.8em;
        }

        /* The Close Button */
        span.close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        span.close:hover,
        span.close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        
        /* prettify xml */
		.xtb { display: table; font-family: monospace; }
		.xtc { display: table-cell; }
		.xmt { color: #0000CC!important; display: inline; }
		.xel { color: #990000!important; display: inline; }
		.xdt { color: #000000!important; display: inline; }
		.xat { color: #FF0000!important; display: inline; }
    </style>
</head>
<body>
    <div class='container-flex'>
        <div class='columns'>
            <div class='column col-md-12' id='editor_holder'></div>
        </div>
        <div class='columns'>
            <div class='column col-md-12'>
                <button id='check' class='btn btn-success'>Validate</button>  
                <button id='restore' class='btn btn-secondary'>Restore to Default</button>
                <div id='valid_indicator' class='mt-1 alert alert-danger'></div>
                <button id='submit' class='btn btn-primary' disabled>Submit</button>  
                <?php 
                    if ($id) {
                        echo "<button id='delete' class='btn btn-danger'>Delete item [id=" . $id . "]</button>";
                    } 
                ?>
            </div>
        </div>
        <div class='columns p-3 mt-0'>
            <div id='server_response' class="alert alert-dismissible fade" role="alert">
            <span class='mymessage'></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="$(this).parent().removeClass('show')">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
        </div>
    </div>
    <script>
        var editor = null;

        // This is the starting value for the editor
        // We will use this to seed the initial editor 
        // and to provide a "Restore to Default" button.
        var default_starting_value = null;
        
        var id = "<?php echo $id; ?>";
        var ref = "../../json-schemas/digitizertypes.json";
        var route = "../../digitizertypes";
        var method =  id ? "PATCH" : "POST";
        var mySchema = {};

        // Set action on delete button
        $(function(){
            $("button#delete").on("click", function(){
                if (confirm("This action will remove record with id=" + id + ". Continue?") == true) {
                    $.ajax({
                        "url": route + "?id=" + id,
                        "method": "DELETE",
                        "beforeSend": function(jqXHR, settings) {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Remove digitizertype [id=" + id + "]"}, settings);
                        },
                        "success": function(response, textStatus, jqXHR) {
                            emitSignal(Object.assign(jqXHR, {"messageType":"success"}));

                            alert("Record with id=" + id + " removed successfully!");
                            let new_location = window.location.href.split('?')[0];
                            window.location.href = new_location;
                        },
                        "error": function(jqXHR) {
                            emitSignal(Object.assign(jqXHR, {"messageType":"danger"}));
                            $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                            $('#server_response').addClass("alert-danger show");
                        }
                    });
                }
            });  
        });

        // Load schema
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                handleInputID();
                startEditor();
            },
            "error": function(jqXHR) {
                            $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                            $('#server_response').addClass("alert-danger show");
                        }
        });

        function handleInputID() {
            if (!id) {
                mySchema.properties.id.options = {
                    "hidden": true
                }
            } else {
                mySchema.required.push("id");
            }
        }

        function startEditor() {
            if (id) {
                $.ajax({
                    "url": route,
                    "data": {
                        "id": id
                    },
                    "success": function(starting_value) {
                        var data = starting_value.data[0];
                        initializeEditor(data);
                    },
                    "error": function(jqXHR) {
                        $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                        $('#server_response').addClass("alert-danger show");
                    }
                });
            } else {
                initializeEditor(default_starting_value);
            }
        }

        function initializeEditor(starting_value) {
        
            const container = document.getElementById('editor_holder')

            // Initialize the editor
            editor = new JSONEditor(container,{
                // Enable fetching schemas via ajax
                ajax: true,
            
                // The schema for the editor
                schema: mySchema,
            
                // Seed the form with a starting value
                startval: starting_value,

                // Setting theme
                theme: 'bootstrap4',

                show_opt_in: true
            });

            editor.on('ready',() => {
                // Now the api methods will be available
                editor.validate();
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                var toPost = editor.getValue();
                /*
                if (toPost["info"]) {
                    toPost.info = JSON.stringify(toPost.info);
                }
                */
                // PATCH if id is indicated, else POST
                $.ajax({
                    "url": route,
                    "data": JSON.stringify(toPost),
                    "method": method,
                    "beforeSend": function(jqXHR, settings) {
                        jqXHR = Object.assign(jqXHR, settings);
                        if (method == 'POST') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Add digitizertype"});
                        }
                        if (method == 'PATCH') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Edit digitizertype [id=" + id + "]"});
                        }
                    },
                    "success": function(response, textStatus, jqXHR) {
                        //console.log(jqXHR);
                        jqXHR = Object.assign(jqXHR, {"messageType":"success"});
                        if (method == 'POST') {
                            if (jqXHR.status == 207) {
                                jqXHR = Object.assign(jqXHR, {"messageType":"warning"});
                                emitSignal(jqXHR);
                                $('#server_response span.mymessage').html(jqXHR.statusText);
                                $('#server_response').addClass("alert-warning show");
                            } else {
                                emitSignal(jqXHR);
                                let separator = window.location.href.includes('?') ? "&" : "?";
                                window.location.href += separator + "id=" + response.data.id; 
                            }
                        }
                        if (method == 'PATCH') {
                            if (jqXHR.status == 207) {
                                jqXHR = Object.assign(jqXHR, {"messageType":"warning"});
                                emitSignal(jqXHR);
                                $('#server_response span.mymessage').html(jqXHR.statusText);
                                $('#server_response').addClass("alert-warning show");
                            } else {
                                emitSignal(jqXHR);
                                window.location.reload()
                            }
                        }
                    },
                    "error": function(jqXHR) {
                        jqXHR = Object.assign(jqXHR, {"messageType":"danger"});
                        emitSignal(jqXHR);
                        $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                        $('#server_response').addClass("alert-danger show");
                    }
                });
            });
            
            // Hook up the Restore to Default button
            $('#restore').on('click',function() {
                editor.setValue(starting_value);
            });
            
            // Hook up the validation indicator to update its 
            // status whenever the editor changes
            editor.on('change',function() {
                // Get an array of errors from the validator
                var errors = editor.validate();
                
                // Not valid
                if(errors.length) {
                    $("#valid_indicator").removeClass();
                    $("#valid_indicator").addClass('mt-1 alert alert-danger');
                    let html = '<b>Not valid</b>:<br><ul>';
                    for(var i=0; i<errors.length; i++) {
                        html += '<li>' + errors[i].path + ': ' + errors[i].message + '</li>';
                    }
                    html += '</ul>';
                    $("#valid_indicator").html(html);
                    $("#submit").attr("disabled", true);
                }
                // Valid
                else {
                    $("#valid_indicator").removeClass();
                    $("#valid_indicator").addClass('mt-1 alert alert-success');
                    $("#valid_indicator").html('Valid');
                    $("#submit").attr("disabled", false);
                }
            });
        }

        // dispatch event if loaded from a parent frame
        function emitSignal(xhr=null) {
            try {
                var event = new CustomEvent('digitizertypeEdit', {"detail": xhr} );
                window.parent.document.dispatchEvent(event)
            } catch (e) {
                console.log(e);
            }
        }

        // prettify xml
        function formatXml(xml,colorize,indent) { 
            function esc(s){return s.replace(/[-\/&<> ]/g,function(c){         // Escape special chars
                return c==' '?'&nbsp;':'&#'+c.charCodeAt(0)+';';});}            
            var sm='<div class="xmt">',se='<div class="xel">',sd='<div class="xdt">',
                sa='<div class="xat">',tb='<div class="xtb">',tc='<div class="xtc">',
                ind=indent||'  ',sz='</div>',tz='</div>',re='',is='',ib,ob,at,i;
            if (!colorize) sm=se=sd=sa=sz='';   
            xml.match(/(?<=<).*(?=>)|$/s)[0].split(/>\s*</).forEach(function(nd){
                ob=('<'+nd+'>').match(/^(<[!?\/]?)(.*?)([?\/]?>)$/s);             // Split outer brackets
                ib=ob[2].match(/^(.*?)>(.*)<\/(.*)$/s)||['',ob[2],''];            // Split inner brackets 
                at=ib[1].match(/^--.*--$|=|('|").*?\1|[^\t\n\f \/>"'=]+/g)||['']; // Split attributes
                if (ob[1]=='</') is=is.substring(ind.length);                     // Decrease indent
                re+=tb+tc+esc(is)+tz+tc+sm+esc(ob[1])+sz+se+esc(at[0])+sz;
                for (i=1;i<at.length;i++) re+=(at[i]=="="?sm+"="+sz+sd+esc(at[++i]):sa+' '+at[i])+sz;
                re+=ib[2]?sm+esc('>')+sz+sd+esc(ib[2])+sz+sm+esc('</')+sz+se+ib[3]+sz:'';
                re+=sm+esc(ob[3])+sz+tz+tz;
                if (ob[1]+ob[3]+ib[2]=='<>') is+=ind;                             // Increase indent
            });
            return re;
        }

        JSONEditor.defaults.callbacks = {
            "button" : {
                "prettifyXML" : function (jseditor, e) {
                    editorValue = editor.getValue();
                    console.log(editorValue);

                    // Get the modal
                    var modal = document.getElementById("prettyXML");
                    
                    // Get the <span> element that closes the modal
                    var span = document.getElementsByClassName("close")[0];

                    // Get content container
                    var content = document.getElementById("prettyXMLContent");
                    content.innerHTML = formatXml(editorValue["additional_info"]["responseXML"], true);

                    // When the user clicks the button, open the modal 
                    modal.style.display = "block";

                    // When the user clicks on <span> (x), close the modal
                    $("span.close").on("click", function() {
                        modal.style.display = "none";
                    });

                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                }
            }
        }
    </script>
    <!-- The Modal -->
    <div id="prettyXML" class="mymodal">
        <!-- Modal content -->
        <div class="mymodal-content">
            <span class="close">&times;</span>
            <p id="prettyXMLContent"></p>
        </div>
    </div>
</body>
</html>