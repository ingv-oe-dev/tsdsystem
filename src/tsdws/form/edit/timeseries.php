<?php
    $id = isset($_GET["id"]) ? $_GET["id"] : null;
	$channel_id = isset($_GET["channel_id"]) ? $_GET["channel_id"] : null;
    $station_id = isset($_GET["station_id"]) ? $_GET["station_id"] : null;
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
        var default_starting_value = {};
        
        var id = "<?php echo $id; ?>";
		var channel_id = "<?php echo $channel_id; ?>";
        var station_id = "<?php echo $station_id; ?>";
        var ref = "../../json-schemas/timeseries.json";
        var route = "../../timeseries?showColDefs=true&showMapping=true";
        var method =  id ? "PATCH" : "POST";
        var mySchema = {};
        var selectedChannelIDs4Mapping = [];

        // Load schema
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                handleInputID();
                $.ajax({
                    "url": "../../channels",
                    "success": function(response) {
                        fillChannelList(response.data);
                        startEditor();
                    },
                    "error": function(jqXHR) {
                        $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                        $('#server_response').addClass("alert-danger show");
                    }
                });
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
                // disable columns editing on PATCH (coupled with line 151)
                mySchema.required.push("columns");
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
                        initializeEditor(starting_value.data[0]);
                    },
                    "error": function(jqXHR) {
                        $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                        $('#server_response').addClass("alert-danger show");
                    }
                });
            } else {
				if (channel_id) {
					default_starting_value["mapping"] = {};
					default_starting_value["mapping"]["channel_id"] = [channel_id];
				}
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

                array_controls_top: true,
                prompt_before_delete: false,
                show_opt_in: true
            });

            editor.on('ready',() => {
                // Now the api methods will be available
                editor.validate();

                Array.prototype.unique = function() {
                    var a = this.concat();
                    for(var i=0; i<a.length; ++i) {
                        for(var j=i+1; j<a.length; ++j) {
                            if(a[i] === a[j])
                                a.splice(j--, 1);
                        }
                    }

                    return a;
                };

                // add functionalities: 
                $(document.getElementById("root[mapping][channel_list]")).on("change", function(event) {
                    let channel_list = $(event.currentTarget).val();
                    //console.log(channel_list);
                    if(editor.getEditor('root.mapping.add_channel_mode').getValue()) {
                        console.log(selectedChannelIDs4Mapping.concat(channel_list).unique());
                        selectedChannelIDs4Mapping = selectedChannelIDs4Mapping.concat(channel_list).unique();
                        // `getEditor` will return null if the path is invalid
                        let channel_id = editor.getEditor('root.mapping.channel_id');
                        channel_id.setValue(selectedChannelIDs4Mapping);
                    }
                });

                $(document.getElementById("root[mapping][add_channel_mode]")).on("change", function(event) {
                    let disabled = !editor.getEditor('root.mapping.add_channel_mode').getValue();
                    $(document.getElementById("root[mapping][channel_list]")).prop('disabled', disabled);
                });

                // disable columns editing on PATCH
                if (method == "PATCH") {
					editor.getEditor('root.columns').disable();
					editor.getEditor('root.schema').disable();
					editor.getEditor('root.name').disable();
				}
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                var toPost = editor.getValue();
                /*
                if (toPost["metadata"]) {
                    toPost.metadata = JSON.stringify(toPost.metadata);
                }
                */

                // force deleting of old mappings
                if (toPost["mapping"]) {
                    toPost["mapping"]["force"] = true;
                }

                // PATCH if id is indicated, else POST
                $.ajax({
                    "url": route,
                    "data": JSON.stringify(toPost),
                    "method": method,
                    "beforeSend": function(jqXHR, settings) {
                        jqXHR = Object.assign(jqXHR, settings, {"channel_id": channel_id, "station_id": station_id});
                        if (method == 'POST') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Add timeseries"});
                        }
                        if (method == 'PATCH') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Edit timeseries [id=" + id + "]"});
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

                // custom
                var i=0;
                selectedChannelIDs4Mapping = [];
                while (document.getElementById("root[mapping][channel_id][" + i + "]") !== null) {
                    let value = $(document.getElementById("root[mapping][channel_id][" + i + "]")).val();
                    selectedChannelIDs4Mapping.push(value);
                    i++;
                } 
                
            });
        }

        function fillChannelList(data) {
            var custom_enum = new Array();
            var custom_enum_titles = new Array();
            for (var i=0; i<data.length; i++) {
                custom_enum.push(data[i].id);
                custom_enum_titles.push(data[i].net_name + " -> " + data[i].station_name + " -> " + data[i].sensortype_name +" -> " + data[i].name + " [ID: " + data[i].id + "] from " + data[i].start_datetime.substr(0,10));
            }
            //console.log(mySchema);
            mySchema.properties.mapping.properties.channel_list.items.enum = custom_enum;
            mySchema.properties.mapping.properties.channel_list.items.options.enum_titles = custom_enum_titles;
        }
		
		// dispatch event if loaded from a parent frame
        function emitSignal(xhr=null) {
            try {
                var event = new CustomEvent('timeseriesEdit', {"detail": xhr} );
                window.parent.document.dispatchEvent(event)
            } catch (e) {
                console.log(e);
            }
        }
    </script>
</body>
</html>