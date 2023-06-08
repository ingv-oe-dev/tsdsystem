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
    <script src="../js-download/bootstrap.bundle.min.js"></script>
    <script src="../js-download/jquery-3.6.0.min.js"></script>
    <script src="../js-download/jsoneditor-2.8.0.min.js"></script>
</head>
<body>
    <div class='container-fluid p-4'>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="by-member-tab" data-bs-toggle="tab" data-bs-target="#by-member" type="button" role="tab" aria-controls="by-member" aria-selected="true">Roles by member</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="by-role-tab" data-bs-toggle="tab" data-bs-target="#by-role" type="button" role="tab" aria-controls="by-role" aria-selected="false">Members by role</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="by-member" role="tabpanel" aria-labelledby="by-member-tab">
                <span id='selectedMember'></span>
                <div id='table_by_member' class='table-responsive'><small class="text-muted">Empty table</small></div>
            </div>
            <div class="tab-pane fade" id="by-role" role="tabpanel" aria-labelledby="by-role-tab">
                <span id='selectedRole'></span>
                <div id='table_by_role' class='table-responsive'><small class="text-muted">Empty table</small></div>
            </div>
        </div>
    </div>
    <hr>
    <div class='container-flex'>
        <div class='columns'>
            <div class='column col-md-12' id='editor_holder'></div>
        </div>
        <p class='columns'>
            <div class='column col-md-12'>
                <button id='check' class='btn btn-success'>Validate</button>  
                <button id='restore' class='btn btn-secondary'>Restore to Default</button>
                <div id='valid_indicator' class='mt-1 alert alert-danger'></div>
                <button id='submit' class='btn btn-primary' disabled>Submit</button>  
            </div>
        </p>
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
        
        var ref = "../../json-schemas/members_mapping_roles.json";
        var route = "../../roles/mapping/";
        var mySchema = {};

        // Load schema
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                $.ajax({
                    "url": "../../roles",
                    "data": {
                        "sort_by": "name"
                    },
                    "success": function(response) {
                        fillEnum(response.data, "role_id");
                        $.ajax({
                            "url": "../../users",
                            "data": {
                                "sort_by": "name"
                            },
                            "success": function(response) {
                                fillEnum(response.data, "member_id");
                                startEditor();
                            }
                        });
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

        function resetEditor() {
            // make empty the JSON editor container
            $('#editor_holder').html('');

            // reset to null the variable representing the JSON editor11
            editor = null;
        }

        function startEditor() {

            // reset editor
            resetEditor();

            initializeEditor(default_starting_value);
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

                // add functionalities: 
                $(document.getElementById("root[member_id]")).on("change", function(event) {
                    reloadTable(type="by_member", id=document.getElementById("root[member_id]").value);
                });

                // change role_id -> load settings if exists
                $(document.getElementById("root[role_id]")).on("change", function(event) {
                    reloadTable(type="by_role", id=document.getElementById("root[role_id]").value);
                });
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                var toPost = editor.getValue();
                console.log(toPost);
                method = toPost.delete ? 'DELETE' : 'POST';

                // POST
                $.ajax({
                    "url": route,
                    "data": JSON.stringify(toPost),
                    "method": method,
                    "success": function(response) {
                        /*
                        default_starting_value = toPost;
                        // restart editor with new specific sensortype properties form 
                        // and fill the new editor with current data
                        startEditor();
                        */
                       reloadTable(type="by_member", id=document.getElementById("root[member_id]").value);
                       reloadTable(type="by_role", id=document.getElementById("root[role_id]").value);
                    },
                    "error": function(xhr) {
                        $('#server_response').html(xhr.responseJSON.error)
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

                if (editor) {
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
                }
            });
        }

        // fill lists used for select form elements
        function fillEnum(data, propertyKey) {
            //console.log(data);
            var custom_enum = new Array();
            var custom_enum_titles = new Array();
            custom_enum.push(null);
            custom_enum_titles.push("--- Select one ---");
            for (var i=0; i<data.length; i++) {
                custom_enum.push(data[i].id);
                custom_enum_titles.push(data[i].name);
            }
            //console.log(mySchema, propertyKey);
            mySchema.properties[propertyKey].enum = custom_enum;
            mySchema.properties[propertyKey].options.enum_titles = custom_enum_titles;
        }

        function reloadTable(type, id) {

            let input = {};
            if (type == "by_member") {
                input["member_id"] = id;
            } else {
                input["role_id"] = id;
            } 

            $.ajax({
                "url": route,
                "data": input,
                "success": function(response) {
                    let selector = "";
                    if (type == "by_member") {
                        selector = "#table_by_member";
                        let selectedMember = $(document.getElementById("root[member_id]")).find('option:selected').text();
                        let selectedMemberID = $(document.getElementById("root[member_id]")).val();
                        $("#selectedMember").html("(<i>selected</i>: <b>" + selectedMember + "</b> [ID:" + selectedMemberID + "])");
                    } else {
                        selector = "#table_by_role";
                        let selectedRole = $(document.getElementById("root[role_id]")).find('option:selected').text();
                        let selectedRoleID = $(document.getElementById("root[role_id]")).val()
                        $("#selectedRole").html("(<i>selected</i>: <b>" + selectedRole + "</b> [ID:" + selectedRoleID + "])");
                    }
                    writeListFromJSON(response.data, selector);
                }
            });
        }

        function writeListFromJSON(data, selector) {
            //console.log(data);
            if (data.length > 0) {

                var tableID = selector + "_datatable";
                //console.log(tableID);
                var table = "<table id='" + tableID + "' class='table table-striped table-bordered dt-responsive'>";

                // header
                table += "<thead>";
                table += "<tr>";
                var thead = Object.keys(data[0]);
                $.each(thead, function(index, key) {
                    table += "<th>" + key + "</th>";
                });
                table += "</tr>";
                table += "</thead>";

                // body
                table += "<tbody>";
                $.each(data, function(index, tr) {
                    table += "<tr>";
                    $.each(tr, function(index, td) {
                        // columns
                        table += "<td>" + td + "</td>";
                    });
                    table += "</tr>";
                });
                table += "</tbody>";

                // footer
                table += "<tfoot>";
                table += "<tr>";
                var thead = Object.keys(data[0]);
                $.each(thead, function(index, key) {
                    table += "<th>" + key + "</th>";
                });
                table += "</tr>";
                table += "</tfoot>";

                table += '</table>';

                onScreen(selector, table, false);

            } else {
                onScreen(selector, "<p style='font-style:italic'>No data available in table</p>", false);
            }
        }

        function onScreen(target, msg, append) {
            //console.log(target, msg);
            if (append) {
                $(target).append(msg);
            } else {
                $(target).html(msg);
            }
        }
    </script>
</body>
</html>