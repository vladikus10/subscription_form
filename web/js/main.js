//Called when a new subscriber submited a form
$("#subscriptionForm").submit(function(e) {
    var url = "/";
    console.log($("#subscriptionForm").serialize());
    $.ajax({
            type: "POST",
            url: url,
            data: $("#subscriptionForm").serialize(),
            success: function(data)
            {
            	//Displays success message
                $('#errors').html("");
            	$('#messages').html("<p>" + data + "</p>");
            	$('#subscriptionForm')[0].reset();
            },
            error: function(errors)
            {
            	//Displays errors
            	$('#errors').html("");
            	$('#messages').html("");
            	var err = JSON.parse(errors.responseText);
            	err.forEach(function(e){
            		$('#errors').append("<p>" + e + "</p>");
            	});
            }
        });

    e.preventDefault(); 
});

var selected = {};// Object to hold the selected subscriber data
//Opens an edit dialog box
function openSub(data){
	$("#edit_name").val($("#" + data.id + " td:nth-child(1)").text());
	$("#edit_email").val($("#" + data.id + " td:nth-child(2)").text());
	selected = data;
	$('#modal').show();
	$('#editForm').show();
}

//Called on submitting the edit form
$("#editForm").submit(function(e) {
    var url = "/list";
    selected.name = $("#edit_name").val();
    selected.email = $("#edit_email").val()
    $.ajax({
            type: "PUT",
            url: url,
            data: selected,
            success: function(data)
            {
               //alert(JSON.stringify(data));
                $('#modal').hide();
			    $('#editForm').hide();
                $("#" + selected.id + " td:nth-child(1)").html(selected.name); 
                $("#" + selected.id + " td:nth-child(2)").html(selected.email); 
            },
            error: function(errors)
            {
            	$('#errors').html("");
            	var err = JSON.parse(errors.responseText);
            	err.forEach(function(e){
            		$('#errors').append("<p>" + e + "</p>");
            	});
            }
        });

    e.preventDefault(); 
});

//Closes the edit dialog
$('#close').click(function(){
	$('#modal').hide();
	$('#editForm').hide();
});

//Deletes a subscriber
function deleteSub(id){
	//alert(id);
	var url = "/list";
	$.ajax({
            type: "DELETE",
            url: url,
            data: {id: id},
            success: function(data)
            {
               //alert(JSON.stringify(data));
               $("#" + id).remove(); 
            },
            error: function(errors)
            {
            	alert(errors.responseText);
            }
        });
}

//Sorts the table based on which table header was clicked on
$('th').click(function(){
    var table = $(this).parents('table').eq(0)
    var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
    this.asc = !this.asc
    if (!this.asc){rows = rows.reverse()}
    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
})
function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index), valB = getCellValue(b, index)
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB)
    }
}
function getCellValue(row, index){ return $(row).children('td').eq(index).html() }

//Submits login information
$("#loginForm").submit(function(e) {
    var url = "/login";
    $.ajax({
            type: "POST",
            url: url,
            data: $("#loginForm").serialize(),
            success: function(data)
            {
				//Redirects on successful login
                window.location.href = '/list'
            },
            error: function(errors)
            {
            	$('#errors').html("");
            	var err = JSON.parse(errors.responseText);
            	err.forEach(function(e){
            		$('#errors').append("<p>" + e + "</p>");
            	});
            }
        });

    e.preventDefault(); 
});