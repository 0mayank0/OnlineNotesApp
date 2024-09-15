$(function() {
    var activeNote = 0;
    var editMode = false;

    // Load notes on page load
    function loadNotes() {
        $.ajax({
            url: "loadnotes.php",
            success: function(data) {
                $('#notes').html(data);
                clickOnNote();
                clickOnDelete();
            },
            error: function() {
                $('#alertContent').text("Error with the Ajax Call. Try again later.");
                $("#alert").fadeIn();
            }
        });
    }

    loadNotes();

    // Add a new note
    $('#addNote').click(function() {
        $.ajax({
            url: "createnote.php",
            success: function(data) {
                if (data === 'error') {
                    $('#alertContent').text("Issue inserting the new note!");
                    $("#alert").fadeIn();
                } else {
                    activeNote = data;
                    $("textarea").val("");
                    showHide(["#notePad", "#allNotes"], ["#notes", "#addNote", "#edit", "#done"]);
                    $("textarea").focus();
                }
            },
            error: function() {
                $('#alertContent').text("Error with the Ajax Call. Try again later.");
                $("#alert").fadeIn();
            }
        });
    });

    // Update note on keyup
    $("textarea").keyup(function() {
        $.ajax({
            url: "updatenote.php",
            type: "POST",
            data: { note: $(this).val(), id: activeNote },
            success: function(data) {
                if (data === 'error') {
                    $('#alertContent').text("Issue updating the note!");
                    $("#alert").fadeIn();
                }
            },
            error: function() {
                $('#alertContent').text("Error with the Ajax Call. Try again later.");
                $("#alert").fadeIn();
            }
        });
    });

    // Click on all notes button
    $("#allNotes").click(function() {
        loadNotes();
        showHide(["#addNote", "#edit", "#notes"], ["#allNotes", "#notePad"]);
    });

    // Click on done after editing
    $("#done").click(function() {
        editMode = false;
        $(".noteheader").removeClass("col-xs-7 col-sm-9");
        showHide(["#edit"], ["#done", ".delete"]);
    });

    // Click on edit
    $("#edit").click(function() {
        editMode = true;
        $(".noteheader").addClass("col-xs-7 col-sm-9");
        showHide(["#done", ".delete"], ["#edit"]);
    });

    // Click on a note
    function clickOnNote() {
        $(".noteheader").click(function() {
            if (!editMode) {
                activeNote = $(this).attr("id");
                $("textarea").val($(this).find('.text').text());
                showHide(["#notePad", "#allNotes"], ["#notes", "#addNote", "#edit", "#done"]);
                $("textarea").focus();
            }
        });
    }

    // Click on delete
    function clickOnDelete() {
        $(".delete").click(function() {
            var deleteButton = $(this);
            $.ajax({
                url: "deletenote.php",
                type: "POST",
                data: { id: deleteButton.next().attr("id") },
                success: function(data) {
                    if (data === 'error') {
                        $('#alertContent').text("Issue deleting the note!");
                        $("#alert").fadeIn();
                    } else {
                        deleteButton.parent().remove();
                    }
                },
                error: function() {
                    $('#alertContent').text("Error with the Ajax Call. Try again later.");
                    $("#alert").fadeIn();
                }
            });
        });
    }

    // Show/Hide elements
    function showHide(showArray, hideArray) {
        $(showArray.join(', ')).show();
        $(hideArray.join(', ')).hide();
    }
});
